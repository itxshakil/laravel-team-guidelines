# Coding standards

Pint and Rector handle formatting and most modernisation (configs in this repo's root). This page covers what they can't decide for you.

## Baseline

- `declare(strict_types=1);` in every PHP file.
- PSR-12 through Pint's Laravel preset, 120 characters a line.
- Types on every parameter, property and return. No `mixed` unless it's genuinely anything.
- `final` classes by default. Remove `final` when you actually need to extend, not in case you might.
- Constructor property promotion for dependencies. Use `readonly` when the value must never change by design, not as decoration.
- `snake_case` columns, `camelCase` variables and methods, `PascalCase` classes. Methods start with a verb (`renewPlan`); booleans start with `is`, `has` or `can`.

## Guard first, happy path last

```php
public function handle(Order $order): void
{
    if ($order->isPaid()) {
        return;
    }

    if (! $order->hasItems()) {
        throw new EmptyOrderException($order);
    }

    $this->charge($order);
}
```

No `else` after a `return` or `throw`.

## Pass arguments by position

Write `in_array($needle, $haystack, true)`, not `in_array($needle, $haystack, strict: true)`. Named arguments make parameter names part of the API, so renaming a parameter in a package or your own class breaks every named caller. Use a named argument only to skip optional parameters, or where the order would otherwise be unreadable. Rector's `namedArgs` set is off in `rector.php` for this reason.

## Small things that add up

- String interpolation (`"Order {$order->number}"`) over concatenation.
- One trait per `use` line inside a class, so a diff that adds or removes one is one line.
- `match` over `switch` when you're mapping values.
- Collections for transforming data. A `foreach` for side effects.
- No `env()` outside `config/`. Once config is cached it returns `null`.
- No `dd()`, `dump()`, `ray()` or `console.log()` in a commit. Rector strips the PHP ones; the hook catches the rest.
- No commented-out code. Git remembers it.

## Arrays with a shape are a missing class

When a method returns `array{plan: Plan, cents: int, tax: int}`, the array is a type PHP can't check. Make it a `final readonly` DTO when you next touch the code. A DTO also gives you a constructor to reject impossible values.

## Translations

- Every user-facing string goes through `__()` with a literal key.
- Map enums to labels with a `match` and one literal key per case. A key built at runtime (`__("status.{$this->value}")`) is invisible to any test that checks every key exists in every language.

## Comments

- Say why, not what.
- Comment the surprising parts: a workaround, an ordering constraint, a guard that exists because of a past failure.
- Point to an ADR or runbook for the long reason, never to an issue number.
- Don't restate the method name in a docblock. If that's all it says, delete it.
- Array shape docblocks (`@return array{...}`) are fine until the DTO exists.

## Files

- Use the `Storage` facade, never direct paths. Store the path in the database, not the file.
- Organise by entity: `users/{id}/avatar.jpg`, `invoices/{id}/receipt.pdf`.
- Validate uploads by MIME type and size (`mimes:jpg,png|max:2048`), and serve private files through a controller that authorizes the request.

## APIs

- Return API Resources, never Eloquent models. A model leaks every column you add later, including the ones you'd never expose.
- One response shape: `{"success": bool, "message": string, "data": {}}`, with honest status codes (201 created, 422 validation, 404 not yours or not found).
- Version from day one (`/api/v1`).
- Paginate lists.
- When a third-party API can answer with an empty body, don't declare the result `array` and call `->json()`. It returns `null` and your typed method throws. The registrar answered DNS record deletes with an empty body, so every delete returned a 500 even though the record was gone.

## Reading

- [Laravel Beyond CRUD](https://spatie.be/guidelines/laravel-beyond-crud), on structuring larger apps.
- *Refactoring* by Martin Fowler, on improving code you already have.
