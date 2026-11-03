# Database

## Run the production engine in CI

The admin's feature-usage report worked locally and in CI on SQLite and returned a 500 in production on MariaDB: error 1247, an aggregate alias used in `ORDER BY` in a way MariaDB rejects. SQLite is forgiving in exactly the places production isn't: types, `GROUP BY` rules, JSON functions, case sensitivity and locking.

If you can't run the production engine everywhere, run it in at least one CI job. Until then, treat raw expressions, `groupBy`, `orderByRaw` and JSON column queries as untested.

## Migrations

- `up()` only. Nobody runs `down()` in production, so an untested `down()` is a false sense of safety. Roll back by writing a new migration.
- Every migration gets a unique timestamp. Two files with the same prefix run in filename order, which isn't always the order you meant.
- One change per migration.
- Write changes expand-then-contract: add the new column, deploy code that writes both, backfill, switch reads, and drop the old column in a later deploy. The previous release keeps working against the new schema during the deploy.
- Once real customers exist, a migration must keep their rows. Write the backfill and test it on a copy of production data first.

## Spell out table names for Larastan

Larastan learns model columns by reading migrations statically. This hides the new columns from it:

```php
foreach (['orders', 'carts'] as $table) {
    Schema::table($table, fn (Blueprint $t) => $t->string('currency')->default('CAD'));
}
```

Analysis then fails on every model property that uses them. Write one literal `Schema::table('orders', ...)` call per table.

## Keys and indexes

- `foreignId('user_id')->constrained()->cascadeOnDelete()` unless deleting the parent must be blocked. Then say `restrictOnDelete()` and why.
- Index every column used in a `where`, a join or an `orderBy` on a table that grows. A foreign key isn't always an index on every engine.
- Singular `model_id` for foreign keys. Pivot tables are named in alphabetical order (`post_tag`).

## Queries

- Eager load with `with()`. Turn on `Model::preventLazyLoading()` outside production so N+1 queries fail in development.
- `paginate()` for lists. `chunkById()` or `lazyById()` to walk big tables. Plain `chunk()` skips rows when the loop updates a column the query filters on, because the pages shift under it.
- Select the columns you need on wide tables.
- Multi-step writes go in `DB::transaction()`, with no external API calls inside ([Architecture](architecture.md#no-external-calls-inside-a-transaction)).
- Concurrent updates use conditional writes (`where('status', 'pending')->update(...)`) or `lockForUpdate()`. Never read, decide in PHP, then write.

## Don't make the database your log store

On a shared host, about 88% of slow queries were writes to the request log table, the database session table and the database cache, not the app's own queries. The request logger was also at the top of the PHP-FPM slow log.

Request logs, sessions and cache can live in the main database while traffic is small. Plan to move them (log files or a log store for requests, Redis for cache, sessions and queues) before they compete with checkout for the same disk.

## Snapshots are not duplication

Order lines, invoice addresses and purchase-time prices are copied on purpose. See [Architecture](architecture.md#some-data-is-duplicated-on-purpose) and [Money and payments](money-and-payments.md#store-what-you-cant-recompute-and-nothing-else).
