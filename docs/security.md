# Security

## Authorization lives in policies

- Every resource has a policy. Controllers call `$this->authorize()` or use `can:` middleware; no inline `if ($user->id !== $order->user_id)`.
- Prefer `Response::denyAsNotFound()` for records the user doesn't own. A 403 confirms the record exists, and for a sequential id that tells an attacker how many orders you have.
- Give dangerous abilities their own policy method: spending money, cancelling a service, revealing a password or API key. Don't fold them into `view` or `update`. When we added shared account access (a delegate acting for an owner), a delegate could reach the owner's rows by URL. Only the separate abilities stopped them from paying or revealing secrets.
- Scope portal queries by the account being acted on, not blindly by `auth()->id()`, once anything like delegation or teams exists. Retrofitting that later means auditing every query.

## Guards have overlapping ids

With separate guards for customers, staff and admins, user #5 and admin #5 are different people with the same id. Anything keyed by id alone mixes them up:

- **Rate limits.** Key them by guard and id (`"admin:{$id}"`), plus a site-wide per-IP cap on top.
- **Broadcast channels.** Name them by model (`App.Models.Admin.{id}`) and accept only that guard on each.
- **Polymorphic authors.** Store the type as well as the id (`replier_type`, `replier_id`).

## Public forms without a CAPTCHA

A CAPTCHA means a third-party script, a CSP exception, a privacy question and a puzzle for real customers. A throttle alone let bots through. What works instead:

- A honeypot field, an encrypted render timestamp, and a marker set by JavaScript, scored together with soft signals (duplicate message, disposable email domain, a link in the name field).
- **Flagged submissions are quarantined, not rejected.** The bot sees the normal success page, no email goes out, and the row is kept for review. Bots learn nothing, and a false positive is one click to recover.
- Store what the detector thought (`spam_score`, `spam_signals`) separately from what a person decided (`is_spam`, `reviewed_by`). With one boolean you can't tell a correct prediction from a corrected one, so the labels are useless for tuning.
- Name the honeypot something your business never asks for (`fax_number`). Because quarantine looks exactly like success, a real field with the same name would silently drop every customer who filled it in. A test guards against this.

[laravel-form-shield](https://github.com/itxshakil/laravel-form-shield) packages this approach.

## Content Security Policy

- **No CDN assets.** Load libraries from npm through Vite. Every CDN host is a CSP exception and a supply-chain risk.
- **Nonce, don't `unsafe-inline`.** Call `Vite::useCspNonce($nonce)` before `$next($request)` in the middleware, or `@vite` tags render without the nonce and the browser silently blocks them.
- `style-src-attr` is a separate directive and can't carry nonces. Inline `style=""` attributes need it relaxed, so remove them page by page as you touch views.
- Keep the Vite dev server's `ws://localhost:5173` out of the production `connect-src`.
- Collect reports at a CSP report endpoint, with no session middleware on it. Reports are frequent and anonymous, and writing a session for each one added to the database stalls described in [Database](database.md#dont-make-the-database-your-log-store).
- When marketing adds a tag (Bing UET, Google Ads, Clarity), the CSP needs it too, or it's blocked on every page while the ad spend keeps running. Some of the blocks in reports will be bots injecting scripts. That's the CSP doing its job, so don't widen it for them.
- A larger CSP means larger response headers. See [the proxy header limit](production.md#watch-response-header-size).

## Secrets

- `.env` only, read through `config()`. `env()` outside `config/` returns `null` once config is cached.
- Third-party API keys that customers give you are stored with `Crypt::encryptString()`.
- Never log tokens, passwords or full card data. Redact named fields (`password`, `token`, `card_number`) in request logs and error emails, and keep the list in config so it's reviewed.
- Run a secret scanner (gitleaks) in CI. A leaked key is rotated, not just deleted from git; the history keeps it.
- Docker Compose reads database passwords from `.env` and refuses to start without them, instead of falling back to a default.

## Dependencies

Run `composer audit` and `npm audit` in CI. Composer now refuses to install a version with an open advisory by default. A whole Laravel major line can stop installing once it's past its security window, and CI jobs pinned to it start failing overnight.
