# 🚀 Deployment & Production Rules

> **Opinionated. Reliable. Reversible.**
> Deployment is not a ceremony — it’s a **system**.
> Great teams don’t “push” code; they **promote** it through predictable pipelines.

---

## 📑 Table of Contents

* [🧭 Core Philosophy](#-core-philosophy)
* [🧱 Immutable Builds](#-immutable-builds)
* [🧰 CI/CD as the Source of Truth](#-cicd-as-the-source-of-truth)
* [🧳 Environment Configuration](#-environment-configuration)
* [🧩 Database Migrations & Backups](#-database-migrations--backups)
* [🔄 Rollback & Recovery](#-rollback--recovery)
* [🧯 Monitoring & Health Checks](#-monitoring--health-checks)
* [🧼 Production Sanity Checklist](#-production-sanity-checklist)
* [🧠 Post-Deployment Ritual](#-post-deployment-ritual)
* [💡 The Golden Mindset](#-the-golden-mindset)
* [📘 Reference Links](#-reference-links)
* [👨‍💻 Author](#-author)

---

## 🧭 Core Philosophy

A deployment pipeline should be **predictable, auditable, and reversible**.
Every environment — development, staging, and production — must behave consistently.

> Think: *“Build once, deploy many times.”*
> CI builds the artifact; environments merely **receive** it.

---

## 🧱 Immutable Builds

> “What passes CI is what ships.”

* No manual edits, SSH fixes, or hot-swaps — if it’s not in Git, it doesn’t exist.
* Always deploy **locked dependencies** (`composer.lock`, `package-lock.json`).
* Run production installs using:

```bash
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci && npm run build
```

> 🧩 **Golden Rule:** Servers receive **artifacts**, not source chaos.

---

## 🧰 CI/CD as the Source of Truth

Your **pipeline** — not humans — owns the deployment.

* Validate tests, linting, and security scans before deploying.
* Build and store **versioned artifacts** (zip, tarball, or Docker image).
* Tag releases automatically (e.g. `v1.4.3`, `v1.4.4`).
* Deploy via CI tools like **GitHub Actions**, **GitLab CI**, or **Forge Pipelines**.

Example GitHub Action snippet:

```yaml
- name: Deploy to Production
  run: php artisan down && php artisan migrate --force && php artisan up
```

> 💬 Add Slack or Discord alerts for visibility and quick response.

---

## 🧳 Environment Configuration

* Never clone `.env` files between environments.
* Use `.env.example` for onboarding and **Secrets Manager** for production.
* Cache configs post-deployment:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

* Rotate secrets periodically and revoke unused tokens.

> ⚠️ **Never** run `php artisan optimize` on Laravel 10+ — it’s deprecated.

---

## 🧩 Database Migrations & Backups

Schema changes are **surgical operations**, not “push and pray”.

### ✅ DO

* Back up before destructive migrations.
* Run migrations automatically via CI/CD.
* Validate schema with `php artisan migrate:status`.
* Wrap destructive migrations in **transactions** if possible.

### 🚫 DON’T

* Don’t run migrations manually via SSH.
* Don’t restore local dumps to live systems.

**Example Safe Flow:**

```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## 🔄 Rollback & Recovery

Failures happen — downtime shouldn’t.

* Keep **previous release artifacts** (last 3–5).
* Roll back using version tags or stored artifacts.
* For high-traffic apps, use **Blue-Green** or **Canary Deployments**.
* Employ **feature flags** for progressive rollouts and quick disables.

> 🧠 Rollback fast — analyze later.

---

## 🧯 Monitoring & Health Checks

Deployment isn’t done when code goes live — it’s done when **metrics stabilize**.

* Add `/health` or `/status` endpoints for uptime checks.
* Integrate **Sentry**, **Bugsnag**, or **Telescope** for error tracking.
* Use **Blackfire** or **Laravel Debugbar** in staging for performance insights.

**Monitor Key Metrics:**

* Error rates
* Response times
* Queue job failures
* Disk & DB utilization

> 📈 Observability builds trust — without it, you’re flying blind.

---

## 🧼 Production Sanity Checklist

Before deploying, confirm every box ✅:

* [ ] All tests pass
* [ ] Code reviewed & merged via PR
* [ ] Config, routes, and views cached
* [ ] Logs cleared and rotated
* [ ] No debug calls (`dd`, `dump`, `ray`, `console.log`)
* [ ] DB + Storage backups complete
* [ ] Migration plan validated
* [ ] Monitoring active and alerting

---

## 🧠 Post-Deployment Ritual

* Verify health endpoints, queues, and caches.
* Tag deployment in Git (`v1.5.0`).
* Announce release in team chat.
* Archive deployment logs for traceability.

Example Git tag flow:

```bash
git tag -a v1.5.0 -m "Release: Improved order approval flow"
git push origin v1.5.0
```

> 🪶 Simple rituals prevent expensive chaos.

---

## 💡 The Golden Mindset

> *“Production is sacred.”*
> Treat it like a temple — everything done there should **honor reliability**, not gamble with it.

---

## 📘 Reference Links

* 📦 [Laravel Deployment Docs](https://laravel.com/docs/deployment)
* 🔐 [OWASP Laravel Security Guide](https://owasp.org/www-project-laravel-security/)
* ⚙️ [GitHub Actions for Laravel](https://github.com/marketplace/actions/laravel-deploy)
* 🧠 [12-Factor App Principles](https://12factor.net/)

---

## 👨‍💻 Author

**Shakil Alam**
Full Stack Laravel Developer
🔗 GitHub: [@itxshakil](https://github.com/itxshakil)
🌐 [shakiltech.com](https://shakiltech.com)

---
