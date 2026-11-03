# Frontend

Written for server-rendered Blade with small amounts of Alpine or plain JavaScript. Most of it applies to any stack.

## Blade

- **No `@php` blocks in views.** Page-level values are computed in the controller. Per-row values come from a model method or accessor. Label, colour and icon maps are enum methods.
- **No `app()` in a view.** A view that needs a service gets it from the controller, or becomes a class-based component that injects it in its constructor.
- **An anonymous component that queries, reads config or builds real data becomes a class component.** Anonymous components are for markup and `@props` defaults.
- **Partials that need defaults become components.** An `@include` with a missing variable fails at render time. `@props` gives it a default.
- **Named routes everywhere:** `route('orders.show', $order)`, never `/orders/{{ $order->id }}`.

## Design every state

Each page, and each region that loads on its own, has five states: empty, loading, partial, success and error. The one most often missing is error, drawn as "nothing here".

- **An API failure is never shown as an empty list.** "You have no DNS records" when the registrar timed out leads a customer to recreate records that still exist.
- An empty state says what will appear and offers one action to start.
- Outcomes go in a toast or an inline alert, not a modal that blocks the page.
- Keep submit enabled until the request starts, then mark it busy (`aria-busy="true"`). Disabling it on page load because the form "isn't valid yet" hides the reason from the user.

Build these once as components (`empty-state`, `alert`, `field-error`, `async-region`) and a small `fetchJson` helper that turns a failed response into the error state. Then pages can't skip them by accident.

## Forms and errors

- Every input has a visible `<label>`, the right `type`, `inputmode` and `autocomplete`. Never block paste.
- Validate on submit. Mark bad fields `aria-invalid="true"`, link the message with `aria-describedby`, and move focus to the first bad field.
- Confirm destructive actions with a small inline popover that names the action ("Delete record", next to "Cancel"), not `window.confirm()`. The native `popover` attribute does this with no script.

## Accessibility that gets missed

- Icon-only buttons carry `aria-label`, and the icon inside gets `aria-hidden="true"`.
- Every `<img>` has `alt`: what it's for, or `alt=""` when it's decoration.
- `tabindex` is only ever `0` or `-1`.
- Status is never colour alone. A badge says "Overdue" as well as being red.
- Hit areas are at least 24 by 24 px, and 40 to 44 px for touch. Grow small icon buttons with padding.
- The first Tab stop is a "Skip to content" link to `<main id="main" tabindex="-1">`.

## Images

The one above-the-fold hero image gets `loading="eager" fetchpriority="high"`. Every other image gets `loading="lazy" decoding="async"`. Lazy-loading everything looks finished but delays the most important paint. A test checks that each `<img>` has a hint and that a page has at most one high-priority image.

## Scripts

- Libraries come from npm through Vite, not a CDN tag.
- Vite entry points are `type="module"`, so they run after every classic inline script. An inline call to a library (Chart.js, Swiper, GSAP) must sit inside a `DOMContentLoaded` listener, or it runs before the library exists. The first CDN-to-Vite migration here shipped exactly that bug and was reverted.
- Delegate shared behaviour from one listener on `document` (copy buttons, password toggles). A page that adds its own listener for the same attribute double-fires.
- Use custom DOM events (`cart-updated`) to let components react to each other without knowing about each other.

## CSS traps

- **Never `transition: all`.** It animates layout and colour changes nobody meant to move, and it costs paint. Name the properties: `transition: opacity 200ms, scale 200ms ease-out`.
- **Gate hover with `@media (hover: hover)`.** A bare `:hover` sticks after a tap on touch screens. The exception: when you override a framework's hover on links or buttons, leave yours ungated. The framework's fires on tap anyway, so gating yours lets its colour stick instead.
- **Style `:focus-visible`, not `:focus`**, so mouse clicks don't leave rings. Never `outline: none` without a visible replacement.
- **Animate only `opacity`, `translate`, `scale` and `rotate`**, inside `@media (prefers-reduced-motion: no-preference)`. Exits are shorter and quieter than entries. Set `transform-origin` toward the thing that was clicked.
- **Nested rounded corners are concentric:** inner radius = outer radius minus the padding between them.
- **Semantic tokens, not hex.** `--color-text-muted`, `--color-danger`. A hex literal in a component is a colour nobody can change globally.
- **Shared foundations go in a file every layout imports.** The admin area here loaded a different stylesheet from the customer portal, so a focus style added to the portal CSS never reached admin pages.

## Copy

- Buttons start with a verb and say what happens: "Renew domain", not "OK" or "Submit".
- A toggle's label describes the on state: "Renew automatically", not "Disable auto-renew".
- Sentence case everywhere. Write text in normal case and let CSS uppercase it if the design wants that.
- Talk to the reader as "you".
- Every string goes through `__()` with a literal key, in every language file, from the first commit. Adding a second language later means finding hundreds of hard-coded strings.
