# Money and payments

Money bugs are the ones customers notice first and forgive last. Everything here came from a real disagreement between two numbers that should have matched.

## Charge in integer cents

The gateway takes integer cents, so do the arithmetic in cents.

```php
// Wrong: float sum, then convert. 19.99 + 2.60 can land a cent off.
$cents = (int) round(($price + $tax) * 100);

// Right: round each part to cents once, then add integers.
$cents = $this->priceCents() + $this->taxCents();
```

Store prices as `decimal(10,2)` and keep every price column the same width. The hosting prices here were `decimal(8,2)` while carts and orders were `decimal(10,2)`, and nobody could say why.

## One currency, set in config

If you sell in one currency, read it from `config('app.currency')` and write that into any currency column. Never take it from the request. A cart column here was filled from whatever the browser posted, defaulted to `USD` while every price was in `CAD`, and was read by nothing except a duplicate check. Supplier costs in another currency (a registrar quoting in USD) are costs, converted when the price is set and stored with the rate used.

## One place computes the price; everything else copies it

An order's total used to be worked out separately by the order page, the admin page, the success page, the email and two invoice PDFs. On the same order they disagreed.

- One service prices the cart and returns a value object. The gateway charge, the order row and every line's discount are written from that object.
- The value object's constructor refuses lines that don't sum to its totals. A bad price can't exist long enough to be charged.
- Anything that reads an order reads stored columns. Nothing re-derives.

## Store what you can't recompute, and nothing else

The test is whether you could get the number back later from what's on the row.

- **Store it:** coupon allocation, the exchange rate, the supplier cost, the tax rate at purchase. The inputs are gone by the time anyone reads the row.
- **Don't store it:** `price * quantity - discount` on the same row. A column for that is a second copy of a fact, free to drift. Make it a method.

## Tax is rounded once and shared out

Round tax once on the order's taxable total, then split it across lines by largest remainder so the shares add up to the order's tax exactly.

Rounding each line's tax separately looks equivalent and isn't: refunding every line of an order then came out a cent or two short of what was charged.

## Verify with the gateway before you create the order

Never mark an order paid because the browser said so. Before the order exists, the server asks the gateway for the payment and checks three things: it's captured, the amount in cents matches the priced cart, and the currency matches.

Webhooks are the safety net, not the main path. They cover the customer who paid and closed the tab. A webhook for a payment with no order rebuilds the order from the gateway's data, re-checking the amount. Whichever path gets there first claims the row (`pending` to `processing` with a conditional update); the other sees it was handled.

## An unknown outcome is not a decline

A dropped connection, a 429 or a 5xx from the gateway means you don't know whether the card was charged. If you record that as a decline, two bad things happen: the customer gets a "your payment failed" email for a payment that may have worked, and the next attempt can charge them again.

- Rethrow unknown outcomes so the queue retries the job under the **same idempotency key**. The gateway answers a repeated key with the original result.
- Record only definite outcomes (succeeded, declined) as transactions.
- After the last retry, alert a person. Don't guess.

## Idempotency keys need care when they include a date

Renewal charges here use a key built from the service, its current expiry and the night of the run. The date is there on purpose: Stripe caches a decline under its key, so without the date, tonight's retry would replay last night's decline.

The catch is that tomorrow's key is new, so the gateway no longer protects a charge whose response was lost last night. The fix:

1. Log every off-session charge in a `gateway_calls` table **before** calling the gateway, and put the idempotency key in the payment's metadata.
2. Before charging, look for an earlier attempt on the same expiry that never got a definite answer.
3. If there is one, ask the gateway (list recent payments and match the key prefix). If it succeeded for the same amount, fulfil without charging. If you can't tell, hold the renewal for a person.

## Retry declines on a schedule, not twice

The first version tried a saved card on the two nights before expiry. Then auto-renew switched off. Most declines are temporary (insufficient funds, a fraud hold that clears), so renewals that would have worked a few days later were lost.

What replaced it:

- Fixed tries counted in days before expiry, for example 30, 21, 14, 7, 3, 1 and 0, plus a few after expiry for services that allow it.
- At most one try a night, stamped against the expiry it was for. A renewal moves the expiry, which starts a fresh schedule.
- Hard declines (`expired_card`, stolen card, and the gateway's other non-retryable codes) stop the tries until the customer changes their default card.

Charging early costs the customer nothing when the renewal extends from the current expiry, not from today.

## Some cards need the customer present

**3D Secure on a saved card.** Some banks ask the cardholder to approve even off-session charges. Stripe answers with a 402 `authentication_required` and leaves the payment open. Don't record a decline. Keep the open payment, email the customer a link to approve it (never the client secret itself), and block every other charge for that expiry until they do. Otherwise a card change followed by an approval charges them twice.

**Indian cards.** RBI rules require an e-mandate, registered with an OTP, before a merchant can charge an Indian card off-session. Without one, Stripe refuses with a 400 and no error code. Four of the first six saved cards here were Indian, so every one of their renewals failed. Request a mandate on every card save (Stripe ignores the options for other countries), store its id and status, and pass it explicitly on each off-session charge. Mandate charges sit in `processing` for about 26 hours while the bank sends its pre-debit notice, so don't treat `processing` as failure.

## Warn before the card expires

A nightly command emails anyone with something on auto-renew whose default card expires within 30 days, once per card expiry. It's cheaper than any retry logic.
