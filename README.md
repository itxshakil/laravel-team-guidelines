# Laravel Team Guidelines

How we build and run Laravel apps, written down from what went wrong and what held up in production.

Most of this comes from running a hosting and billing platform: card payments, auto-renewals, server provisioning over SSH and third-party APIs, queue workers, a customer portal and an admin panel. Where a rule exists because something broke, the doc says what broke. A rule you can't trace to a reason is a rule nobody follows.

## If you only read one section

1. **Controllers validate, authorize and delegate.** Work happens in single-purpose Actions; multi-step flows and third-party APIs live in Services behind an interface.
2. **Never call an external API inside a database transaction.** A rollback can't take back a charge or a created server.
3. **Every job can run late, run twice, or run alongside a copy of itself.** Re-read state at the top of `handle()` and make the work idempotent.
4. **An unknown payment outcome is not a decline.** A timeout or a 5xx means "we don't know yet", and retrying blind can charge a card twice.
5. **Store money you can't recompute later.** Don't store money you can compute from the same row.
6. **Tests never touch the network.** `Http::preventStrayRequests()` in the base test case, plus `Model::shouldBeStrict()`.
7. **If a convention can be checked by a test, write the test.** Docs and code review miss things; a failing test doesn't.
8. **Open work is a GitHub issue, not a doc.** Decisions go in ADRs, procedures in runbooks.
9. **Production uses the same database engine as CI**, or you find the differences in production.
10. **Logs and alerts are a product.** If an alert fires 78 times for one cause, it will be ignored on the day it matters.

## The guides

| Guide | Covers |
|---|---|
| [Architecture](docs/architecture.md) | Request flow, Actions and Services, interfaces, events, DTOs |
| [Coding standards](docs/coding-standards.md) | Style, naming, comments, the small rules Pint can't enforce |
| [Money and payments](docs/money-and-payments.md) | Cents, rounding, idempotency keys, webhooks, renewals |
| [Jobs and queues](docs/jobs-and-queues.md) | Writing safe jobs, retries, workers, `retry_after` |
| [Database](docs/database.md) | Migrations, indexes, snapshots, engine differences |
| [Testing](docs/testing.md) | What to test, isolation, convention tests, speed |
| [Security](docs/security.md) | Policies, guards, rate limits, CSP, secrets |
| [Frontend](docs/frontend.md) | Blade, states, accessibility, motion, CSS traps |
| [Running in production](docs/production.md) | Deploys, logs, alerts, proxies, backups |
| [Docs and decisions](docs/docs-and-decisions.md) | ADRs, runbooks, issues, comments that age well |
| [Git and reviews](docs/git-and-reviews.md) | Commits, hooks, pull requests, CI |
| [Issues](docs/issues.md) | Writing and labelling issues people can act on |

`examples/` holds short PHP files that show the patterns from these guides. CI runs Pint over them with this repo's `.pint.json`, and `rector.php` and `phpstan.neon` are starting configs to copy into a project.

## Changing a guideline

Open a pull request. Say what happened that makes the current rule wrong or missing. "I prefer" is a fine start for a discussion, but a rule lands when it has a reason someone else can check.

## Credits

Earlier versions drew on [alexeymezenin/laravel-best-practices](https://github.com/alexeymezenin/laravel-best-practices), [xqsit94/laravel-coding-guidelines](https://github.com/xqsit94/laravel-coding-guidelines) and [protonemedia/laravel-guidelines](https://github.com/protonemedia/laravel-guidelines).

Maintained by [Shakil Alam](https://github.com/itxshakil). MIT licensed.
