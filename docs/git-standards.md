# 🌿 Branching & Git Strategy

> **Opinionated. Practical. Collaborative.**
> Our Git workflow emphasizes clarity, stability, and teamwork — ensuring every commit adds value without chaos.

---

## 📑 Table of Contents

* [🎯 Purpose](#-purpose)
* [🌲 Branch Overview](#-branch-overview)

    * [Production Branch (`main` / `production`)](#production-branch-main--production)
    * [Development Branch (`development`)](#development-branch-development)
    * [Feature / Fix Branches](#feature--fix-branches)
* [🪜 Branching Workflow](#-branching-workflow)
* [🧱 Commit Guidelines](#-commit-guidelines)
* [🧩 Pull Request Flow](#-pull-request-flow)
* [⚙️ Merge & Deployment Rules](#️-merge--deployment-rules)
* [🚫 Basic Git Hygiene](#-basic-git-hygiene)
* [🧭 Example Workflow Summary](#-example-workflow-summary)
* [👥 Collaboration Principles](#-collaboration-principles)
* [📘 Reference Links](#-reference-links)
* [👨‍💻 Author](#-author)

---

## 🎯 Purpose

To maintain a **stable, readable, and production-safe** codebase by defining a consistent Git branching and collaboration model that every developer follows.

---

## 🌲 Branch Overview

### 🟢 Production Branch (`main` / `production`)

* Always **deployment-ready**.
* Protected from direct commits or force pushes.
* Only updated via **reviewed pull requests** merged from `development`.
* CI/CD pipelines should deploy automatically from this branch.

### 🧩 Development Branch (`development`)

* The **active, long-running** branch for ongoing development.
* All feature, fix, and refactor branches are created from here.
* Code must be **fully tested and reviewed** before merging back.
* Keep it stable — it should always build and run.

### 🌱 Feature / Fix Branches

* Short-lived branches for **focused changes**:

    * `feature/<name>` → new features (e.g. `feature/user-auth`)
    * `fix/<issue>` → bug fixes (e.g. `fix/cart-validation`)
    * `hotfix/<issue>` → emergency production fixes
    * `chore/<task>` → maintenance, config, or refactors

---

## 🪜 Branching Workflow

```bash
# 1. Update your local repo
git checkout development
git pull origin development

# 2. Create a new branch for your task
git checkout -b feature/my-new-feature

# 3. Work, commit, and push
git add .
git commit -m "feat: add user registration flow"
git push origin feature/my-new-feature

# 4. Open a Pull Request (PR) → development
```

> 💡 Always pull before branching, and rebase frequently to stay updated with `development`.

---

## 🧱 Commit Guidelines

Follow the **Conventional Commits** standard for clear and searchable history.

**Format:**

```
<type>(optional-scope): <short description>
```

**Examples:**

* `feat: implement two-factor authentication`
* `fix: resolve API rate-limit issue`
* `refactor: optimize query builder usage`
* `chore: update composer dependencies`

**Principles:**

* One purpose per commit.
* Keep messages short but meaningful.
* Avoid “fixed stuff” or “final update”.

> 📘 Reference: [Conventional Commits Spec](https://www.conventionalcommits.org)

---

## 🧩 Pull Request Flow

Every pull request should follow our **PR Template** and include:

* Clear purpose (“what & why”)
* Relevant issue links
* Testing steps or screenshots
* Passing CI checks

**Base Branch:**

* All PRs target `development`
* Only tested & approved code gets merged into `production`

> 🚦 Each PR should **teach something** — reviewers should understand not just the result, but your reasoning.

---

## ⚙️ Merge & Deployment Rules

* Merge via **Squash + Merge** for cleaner commit history.
* `development → production` merges happen only after staging verification.
* No direct pushes to production. Ever.
* Hotfixes must also be merged back into `development` to avoid divergence.

---

## 🚫 Basic Git Hygiene

* Never use `--force` on shared branches.
* Avoid pushing debug or local-only commits.
* Don’t commit `.env`, `.env.example`, credentials, or personal configs.
* Rebase instead of merge for local sync:

  ```bash
  git fetch origin
  git rebase origin/development
  ```
* Keep pull requests small and focused — ideally under 500 lines of diff.

### 🧩 Temporary Logs & Debugging

> Debugging is part of development — just not in shared branches.

* Never leave `console.log`, `dd()`, `dump()`, or `var_dump()` in committed code.
* If you need to debug or log temporarily:

    1. Create a short-lived branch from your current feature branch:

       ```bash
       git checkout -b debug/feature-issue
       ```
    2. Add any logs or dumps **only in that branch**.
    3. Test locally — never merge it into `development` or `production`.
    4. Delete the debug branch once the issue is resolved.
* Alternatively, use **Laravel Telescope** or **temporary log files** for safe inspection.

> 💡 Rule of thumb: if your log helps *you*, keep it local. If it helps *the team*, convert it into a proper test or monitoring alert.

---

## 🧭 Example Workflow Summary

| Task                    | From          | To            | Example Branch              | PR Target                       |
| ----------------------- | ------------- | ------------- | --------------------------- | ------------------------------- |
| New Feature             | `development` | `development` | `feature/payment-gateway`   | ✅                               |
| Bug Fix                 | `development` | `development` | `fix/order-total-bug`       | ✅                               |
| Hotfix (Production Bug) | `production`  | `production`  | `hotfix/critical-login`     | ✅ + Merge back to `development` |
| Routine Maintenance     | `development` | `development` | `chore/update-deps`         | ✅                               |
| Temporary Debug         | `feature/*`   | none          | `debug/feature-login-error` | ❌ (Never merged)                |

---

## 👥 Collaboration Principles

> “Great teams don’t push harder — they review smarter.”

* Discuss before major refactors.
* Prioritize **clarity over cleverness**.
* Review PRs within 24 hours.
* Keep communication open in PR comments.
* Document architectural or domain decisions in `/docs/decisions/`.

---

## 📘 Reference Links

* 🧠 [Conventional Commits](https://www.conventionalcommits.org)
* 📦 [Git Branch Naming Guide](https://nvie.com/posts/a-successful-git-branching-model/)
* 🚀 [Laravel Deployment Checklist](https://laravel.com/docs/deployment)
* 🔐 [Security Practices](https://owasp.org/www-project-laravel-security/)

---

## 👨‍💻 Author

**Shakil Alam**
Full Stack Laravel Developer
🔗 GitHub: [@itxshakil](https://github.com/itxshakil)
🌐 [shakiltech.com](https://shakiltech.com)

---