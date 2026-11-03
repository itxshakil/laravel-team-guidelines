# Running in production

## A deploy script that refuses to do damage

Ours runs on the server and does only the steps a pull needs:

```
preflight -> database backup -> git pull -> composer install (if the lock changed)
-> migrate (if migrations are pending) -> optimize -> queue:restart
-> docker compose up (only after typing "apply") -> fix storage permissions -> site check
```

The parts that earned their place:

- **Back up before pulling.** A failed dump stops the deploy before anything changes. The dump is deleted once the health check on `/up` passes and kept on any failure.
- **A lock file** so two deploys can't run at once.
- **Refuse a dirty working tree.** Someone's hotfix on the server would otherwise be silently merged or lost.
- **`--dry-run`** prints what would run.
- **Warn when `.env.example` gained keys**, because a new required variable is the commonest "works locally, 500s in production" cause.
- **Container changes need confirmation.** A changed `docker-compose.yml` can restart the database. Show the diff and ask.
- **`queue:restart` every deploy.** Workers keep the old code in memory until they restart.

What it doesn't do yet, and what we'd do next: build an image with the code inside in CI, tagged by commit, so rollback is switching to the previous tag. On a plain VPS, release folders with a `current` symlink (Deployer, Envoyer, Forge) get you most of the way.

Deploy in batches. Push fixes as they're ready and deploy several together at a quiet time, rather than one deploy per commit.

## Logs

- **Give every request an id.** Middleware sets a UUID as `X-Request-Id` on the request and response and adds it to `Log::shareContext()`. Every log line and error email from that request carries it, and a customer can quote it.
- **`LOG_LEVEL=info` in production.** With `debug`, the server-panel client's "API success" and raw-response lines were 85% of a week's log: about 28 MB of 32, from two polling calls every 5 minutes logging the full body twice.
- **Log the failure, not every success.** A routine success at `info` every 5 minutes is noise. Log it at `debug`, or not at all.
- **Review logs on a schedule.** Group a week's errors by message and compare against last week's list. That's how you notice "this went quiet after the fix" and "this has been failing every night for a month".

## Alerts people will read

- **Throttle error emails by fingerprint** (exception class, message, file, line). Choose the fingerprint carefully: one included the slug of the article being processed, so a single missing LLM model sent 78 alert emails in one day.
- **Alert on symptoms customers feel:** oldest job wait per queue, failed renewals, the health check. Not on every exception.
- **Watch for jobs creeping toward their timeout.** A heartbeat job took 5.3 s, then 7.8 s, then 9.7 s against a 10 s timeout before it failed. The trend was visible two days earlier.
- **Have an external uptime check.** An app can't email you that it's down.

## Watch response header size

Every successful "add to cart" request started returning a 502 from the reverse proxy while the item was still saved. The cause: a new response header carrying analytics events, on top of about 1.9 KB of CSP and the cookies, pushed the response headers past nginx's default 4 KB proxy buffer.

A week later the admin login did the same thing, but only when "Remember me" was ticked. The extra cookie pushed it over.

- A 502 that fails fast while the work still completes points to header size first. Check the proxy's error log for "upstream sent too big header".
- Don't put payloads in response headers. Send them in the body.
- Raise the proxy buffers (`proxy_buffer_size 16k; proxy_buffers 8 16k;`) on every proxy host, not just the one that broke.
- Keep a test that measures the largest real response's headers (a remember-me login) against a budget. CSP growth from a new marketing tag will eat the margin otherwise.

## Shared servers

If the app shares a host with other projects, don't install packages, restart shared services or change system config from a deploy or a debugging session. Debug one command at a time and read each result before running the next.

## Backups

- Dump the database nightly to another machine and keep a schedule (for example 7 daily and 4 weekly).
- Alert when a backup fails or doesn't run. Ours timed out every week for a month before anyone looked, because nothing reported it.
- **Test a restore.** A backup that has never been restored is a hope, not a backup.
- Encrypt dumps that leave the server.
