# Jobs and queues

Queue delivery is at least once. A job can run minutes late, run twice, or run at the same time as another copy on a different worker. Write every job assuming all three.

## Re-read, re-check, then act

A queued job carries data as it was at dispatch. By the time it runs, the order may have been refunded or the service renewed by hand.

```php
public function handle(): void
{
    $plan = $this->plan->fresh();

    if ($plan === null || ! $plan->isDueForRenewal()) {
        return;
    }

    // ...
}
```

Pass ids or models, never arrays of values copied from them.

## Make it idempotent

- Skip work that's already done. Check the state, not a "job ran" flag.
- Use conditional updates instead of read-then-write: `Order::whereKey($id)->where('status', 'pending')->update(['status' => 'processing'])` returns 0 for the second runner.
- Give every payment call an idempotency key ([Money and payments](money-and-payments.md#idempotency-keys-need-care-when-they-include-a-date)).

## Dispatch after commit

A job dispatched inside `DB::transaction()` can be picked up before the transaction commits, find no row and fail. Use `->afterCommit()` on the dispatch, or `ShouldQueueAfterCommit` on the listener.

## Retry only what can change

Retrying a refusal wastes the retries and delays the alert.

- A 4xx, bad credentials, or "this account doesn't exist" will fail the same way next time. Call `$this->fail($e)`.
- A timeout, a 5xx, or a lock will often work next time. Let it throw.
- Declare `#[Tries]`, `#[Timeout]` and `#[Backoff]` (when tries > 1) on every job. A test enforces it here, so nobody ships a job with the default of one try and no timeout by accident.
- Jobs that change a real server (resize, restore, reinstall) get `#[Tries(1)]` and are retried by a person after reading the log, never by `queue:retry`.

## `retry_after` belongs to the connection, not the job

If a job runs longer than its connection's `retry_after`, the queue assumes the worker died and hands the same job to the next worker. Now two copies run at once.

So one value can't serve both a 2-minute email job (you want it recovered quickly if a worker crashes) and a 30-minute server restore (you want it left alone). Use two connections on the same `jobs` table:

| Connection | `retry_after` | Jobs |
|---|---|---|
| `database` | 150 s | everything up to 120 s |
| `database-long` | 1900 s | server operations up to 1800 s |

Pin long jobs to the long connection with `#[Connection('database-long')]`. A test fails if a job's timeout is at or above the short connection's `retry_after` and it isn't pinned.

A worker started without naming the connection still polls the long queue, but with the short window. Always name the connection when you start a worker by hand.

In tests, a job pinned to a connection ignores `QUEUE_CONNECTION=sync`. Set the long connection's driver to `sync` in `phpunit.xml` too.

## Split workers by job length

One worker running every queue meant a 27-minute server restore held up card renewals and every email behind it. Queue priority only decides which job is picked next. It can't interrupt one that's running.

Three workers, one process each:

| Worker | Queues |
|---|---|
| priority | `notifications`, `critical` |
| general | `default`, `bulk` |
| long | `long`, `low` |

Two processes on the same queue were considered and rejected for now. Parallel copies of the same job type expose races that a single process hides. Add them only after the jobs are proven idempotent.

Give each worker a stop grace period (`stop_grace_period` in Docker Compose) longer than its longest job, so a deploy lets the running job finish.

## Monitor the wait, not the count

"500 jobs pending" means nothing on its own. Alert on the age of the oldest ready job per queue: a notification waiting 10 minutes is an incident, while a bulk job waiting 10 minutes is normal.

## Scheduled commands

- `withoutOverlapping()` on anything that can run longer than its interval.
- Run the scheduler as its own process (`schedule:work`), not from cron inside the app container, so it restarts with the stack.
- Long sweeps dispatch one job per record rather than doing the work inline. One bad record then fails one job, not the whole run.
