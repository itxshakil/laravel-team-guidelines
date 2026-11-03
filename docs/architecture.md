# Architecture

## The request path

```
Route -> FormRequest -> Controller -> Action or Service -> Model -> Event -> queued Listeners
```

- **FormRequest** validates and, through `authorize()` or a policy, decides who may do it. Controllers read `$request->validated()`, never `$request->all()`.
- **Controllers** stay under about 30 lines per method. Validate, authorize, call one Action or Service, return a response. If a controller loops over records, the loop belongs somewhere else.
- **Actions** (`app/Actions/<Area>/CreateOrderAction.php`) do one unit of work and have one public method. They are the easiest thing in the app to test.
- **Services** (`app/Services/`) hold multi-step flows and wrap third-party APIs. Keep them stateless.
- **Models** hold data, casts, scopes and small derived values. No HTTP calls, no mail.

Stick to resource methods (`index`, `store`, `show`, `update`, `destroy`). `approveInvoice()` on a controller is a sign you're missing a resource: `InvoiceApprovalController@store` is usually the honest name.

## External services sit behind an interface

Every third-party client (payment gateway, registrar, server panel, LLM) is injected as an interface and bound in a service provider.

```php
$this->app->singleton(PaymentGatewayInterface::class, StripePaymentGateway::class);
```

Two reasons, both practical:

- Tests bind a Mockery double with `$this->app->instance(...)`. SDK classes are often `final`, so mocking them directly doesn't work.
- When an interface method returns SDK objects, type it as `object` so a test can return a `stdClass` instead of building a fake SDK object.

All outbound HTTP goes through Laravel's `Http` facade, never a raw Guzzle client. `Http::fake()` can only intercept the facade, and a raw client in one service once made the test suite slow and non-deterministic (see [Testing](testing.md#tests-never-touch-the-network)).

## No external calls inside a transaction

```php
// Wrong: if the insert after the charge fails, the rollback leaves a paid card and no order.
DB::transaction(function () use ($cart) {
    $charge = $this->gateway->charge($cart->totalCents());
    Order::create([...]);
});
```

Charge first and record the result, or create the record as pending, commit, and then call out. Checkout here creates the order and its hosting items inside a transaction, commits, and only then calls the domain registrar for each domain item, because a registered domain can't be rolled back.

## State changes fire events; side effects live in listeners

Before this rule, mail and follow-up work was written inline at about 130 places. Four sign-up paths each sent the welcome email and the admin alert themselves, and the copies drifted.

- An event is named in the past tense (`TicketReplied`, `ServiceRenewed`), carries models not arrays, and implements `ShouldDispatchAfterCommit`. Outside a transaction it fires immediately, so the interface costs nothing.
- One listener per side effect, named for what it does: `SendWelcomeNotification`, `NotifyStaffOfNewTicket`. Let event discovery find them.
- Listeners are queued (`ShouldQueueAfterCommit`). Inside a queued listener, send with `notifyNow()`, or the notification gets queued a second time.
- The exceptions are listed at the class with a reason, such as a payment receipt that must go out even when the queue is down.

"What happens when a ticket is replied to?" now has one answer: list the listeners for `TicketReplied`.

## Live updates never fail the action

If you broadcast over WebSockets (Reverb, Pusher), push through one publisher that:

- defers to `DB::afterCommit()` itself,
- sends with `ShouldBroadcastNow`, because a queued broadcast can sit behind a 25-minute job,
- catches and logs any failure.

Don't put `ShouldDispatchAfterCommit` on a broadcast event. Laravel then sends it inside a commit callback, so a broadcaster error is thrown out of `DB::commit()` and fails work that already succeeded. Broadcast payloads carry ids, statuses and labels, never whole models or personal data.

## Enums for every status

Statuses, types and modes are backed enums cast on the model. Give the enum the presentation too:

```php
enum TicketStatus: string implements HasBadge
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('tickets.status.open'),
            self::Closed => __('tickets.status.closed'),
        };
    }
}
```

Without this, the same `@if ($status === 'open') badge-success` chain gets copied into every view and they drift. Write one literal translation key per case, never `__("tickets.status.{$this->value}")` (see [Testing](testing.md#watch-the-assertion-count)).

## DTOs instead of array shapes

An `array{name: string, cents: int, ...}` docblock is a type that PHP can't check. When you touch code that builds or reads one, turn it into a `final readonly class` in `app/DTOs/`. Don't convert untouched code in bulk. Hundreds of shapes converted in one PR are impossible to review.

Make the DTO refuse bad states in its constructor. A priced cart whose lines don't add up to its total should not be constructible.

## Some data is duplicated on purpose

Normalisation instincts will tell you to remove these. Don't.

- Orders snapshot the customer's name, email and address. An invoice shows who bought it then, not who owns the account now.
- Order items keep the configuration and price at purchase time.
- A local `payment_methods` table mirrors card brand, last four and expiry so the billing page doesn't call the gateway on every load.
- Webhook calls are recorded in a table, not a cache key. That makes them the idempotency record: it survives restarts and can be audited.

Write the reason in a comment or an ADR next to the column. Otherwise the next person "fixes" it.

## Nested route parameters arrive by position

Under a prefix like `suites/{suite}/members/{member}`, Laravel passes parameters to the controller in order. A method that only needs `$member` must still declare `Suite $suite` first, or it receives the suite in the `$member` slot and throws a `TypeError`. Implicit binding only binds type-hinted parameters, so add `Route::model('suite', Suite::class)` if middleware such as `can:view,suite` needs the model rather than a raw id.
