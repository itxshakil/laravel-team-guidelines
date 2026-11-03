# Testing

## Write the failing test first

For a bug, write the test that reproduces it and watch it fail before touching app code. A test written after the fix often passes for the wrong reason, and you only find out when the bug comes back.

## Shape

- PHPUnit classes, `final class OrderRefundTest extends TestCase`, methods named `test_refund_returns_the_tax_share_of_each_line(): void`. The name says the behaviour, so a failure reads like a bug report.
- Arrange, act, assert, in that order, with a blank line between them.
- Feature tests for anything that touches HTTP, the database or the container. Unit tests only for pure logic.
- Build data with factories and their states. If you need a state twice, add it to the factory.

## Tests never touch the network

The unit suite here once took 288 seconds. 81 tests made up 94% of that, and none of them meant to make a network call. The chain was: a factory creates a hosting plan, an observer dispatches a job to write a help article, `QUEUE_CONNECTION=sync` runs it inline, a listener asks the local LLM for an embedding, and the LLM client used raw Guzzle, which `Http::fake()` can't see. Each call to a dead port cost about 2.2 seconds on Windows. On a machine with the LLM running, the same tests made real, random LLM calls.

What fixed it, and what we do now:

- `Http::preventStrayRequests()` in the base `TestCase`. An unfaked call fails at once with `StrayRequestException` instead of hanging. Fix it with `Http::fake()` in the test, never by removing the guard.
- All app HTTP goes through the `Http` facade. A convention test fails if `app/` news up a Guzzle client.
- The base `TestCase` binds harmless fakes for services that factories trigger by accident. A test that needs real behaviour overrides the binding with `$this->app->instance()`.

Two traps when you port a client to the `Http` facade:

- **It doesn't throw on 4xx or 5xx.** Check `$response->failed()` or call `->throw()`, or a 504 sails through as an empty success.
- **Catch `ConnectionException`, not `Throwable`.** `StrayRequestException` is a plain `RuntimeException`. Catch everything and your graceful "service is down" path swallows the test guard too.

## Strict models in tests

`Model::shouldBeStrict()` is on in tests, so a lazy load, a missing attribute or a silently discarded mass-assignment fails loudly. Child-to-parent access inside a loop needs eager loading or `chaperone()` on the relation.

## Mock at the interface

Bind a Mockery double for the interface: `$this->app->instance(PaymentGatewayInterface::class, $mock)`. SDK objects (`PaymentIntent`, `SetupIntent`) are often `final`, so return a `stdClass` with the properties you need. Same for `final` resource classes: mock the client that hands them out.

Fake what Laravel can fake: `Notification::fake()`, `Mail::fake()`, `Bus::fake()`, `Event::fake([OnlyThis::class])`. Then assert the behaviour, such as `Notification::assertSentTo($user, RenewalFailed::class)`, not which method was called.

## Turn conventions into tests

A rule in a doc gets missed in review. A rule in a test doesn't. One `ViewConventionsTest` here scans the codebase and fails when:

- a public form is missing the spam shield component after `@csrf`,
- any input reuses the honeypot field's name (that would silently quarantine every real customer who filled it in),
- an `<img>` has no `alt` or no loading hint, or a page has more than one `fetchpriority="high"`,
- an icon-only button has no `aria-label`,
- `transition: all` appears in CSS,
- code in `app/` sends HTTP without the facade.

`JobRetryPolicyTest` does the same for jobs: every job declares tries and a timeout, and long jobs sit on the long connection. These tests are cheap, run in milliseconds and catch a whole class of mistake for good. When a review comment is "we always do X", ask whether X can be a test.

## Watch the assertion count

A translation test here scans for literal `__('key')` calls and checks each key exists in every language file. A refactor that changed an enum's labels to `__("invoices.terms.{$this->value}")` hid 7 keys from it. The suite stayed green.

The only signal was the total assertion count: 7 lower than the base branch plus the new tests. After a refactor, compare the totals. If yours is lower, run both branches with `--log-junit` and diff the per-test counts to find what stopped being checked.

## Components with named slots

Rendering a component that takes a named `<x-slot>` inline with `$this->blade()` leaves an output buffer open, and PHPUnit marks the test risky. Test slot output by requesting a real page that uses the component.

## Keep the suite fast

- Run the narrowest set while you work: `php artisan test --compact --filter=OrderRefund`, or one file.
- Read the failing test and the code once, fix everything you can see, then run. Don't loop edit-run-edit on a suite that takes minutes.
- Use `--parallel` in CI.
- A slow test is usually a stray network call, a real sleep, or a factory building far more than the test needs. Profile with `--log-junit` and sort by time.
