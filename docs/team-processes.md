# 🤝 Team Processes

> **Collaboration beats heroics.**
> The strongest teams don’t move faster by skipping checks — they move faster by staying in sync.
> Communication, clarity, and rhythm make every release calm and predictable.

---

## 📑 Table of Contents

* [🎯 Purpose](#-purpose)
* [🌿 Branch Naming](#-branch-naming)
* [🧩 Pull Request Guidelines](#-pull-request-guidelines)
* [👑 Code Ownership](#-code-ownership)
* [🧪 Issue & QA Workflow](#-issue--qa-workflow)
* [🪶 Writing a Useful Issue](#-writing-a-useful-issue)
* [⚡ Fast-Resolution Etiquette](#-fast-resolution-etiquette)
* [🔍 Common Misses](#-common-misses)
* [🔁 Iteration & Retrospectives](#-iteration--retrospectives)
* [🧭 Collaboration Principles](#-collaboration-principles)
* [📘 Reference Links](#-reference-links)
* [👨‍💻 Author](#-author)

---

## 🎯 Purpose

To create a **seamless collaboration loop** between developers, testers, and reviewers.
The aim is to ship confidently — not hurriedly — by keeping expectations clear and processes consistent.

> 💬 *Teamwork is not about speed — it’s about flow.*

---

## 🌿 Branch Naming

A branch name should **speak for itself** — what’s being done and why.

```
feature/123-user-profile-update
fix/456-cache-invalidation
chore/789-update-dependencies
test/901-e2e-login
```

**Guidelines:**

* Use lowercase and hyphens (`-`) for readability.
* Prefix with purpose:

    * `feature/` → new functionality
    * `fix/` → bug or regression fix
    * `chore/` → maintenance or docs
    * `refactor/` → internal cleanup
    * `test/` → QA automation or testing branch
* Reference issue or ticket IDs for traceability.
* Delete merged branches to keep repo clean.

> 💡 *Branch naming is communication — not decoration.*

---

## 🧩 Pull Request Guidelines

> *A PR should teach what changed, why it changed, and how it’s tested.*

**Checklist for PRs:**

* Keep PRs **small and focused** (under ~400 lines ideally).
* Add a **clear title and purpose**.
* Link related issues.
* Provide steps to test or verify changes.
* Request review from module owners.
* Don’t self-approve unless it’s a verified emergency.
* Ensure **CI and tests pass** before merge.

> 🚦 *The smaller the PR, the faster the feedback.*

---

## 👑 Code Ownership

Every major area should have an assigned **owner** — responsible for keeping it healthy and consistent.

Example `CODEOWNERS`:

```
# Authentication
app/Http/Controllers/Auth/*  @shakil @ananya

# Payment & Checkout
app/Services/Payment/*       @arun

# QA Tests
tests/Feature/*              @fatima
```

**Responsibilities:**

* Review PRs within 24 hours.
* Maintain naming, structure, and logic consistency.
* Guide new contributors and testers.
* Ensure fixes in their module follow domain logic.

> 🧭 *Ownership is about clarity, not control.*

---

## 🧪 Issue & QA Workflow

> *“A good tester doesn’t break things — they reveal where things are already broken.”*
> Testing is how we protect trust — not just in code, but in teamwork.

---

### 🧠 Before You File an Issue

Check like a craftsman — not a complainer.

1. **Reproduce it twice.** If it’s not reproducible, it’s not reliable.
2. **Clear cache / use incognito.** Half the “bugs” die here.
3. **Know your branch.** Make sure you’re on the latest `develop` or feature branch.

> 🧩 *Each issue must be reproducible by any developer within 60 seconds.*

---

### 🪶 Writing a Useful Issue

Your goal: make fixing effortless for the developer.

| What                   | Example                                                                  |
| ---------------------- | ------------------------------------------------------------------------ |
| **Title**              | “[Profile] Avatar upload fails on Safari 17” *(not “Image not working”)* |
| **Steps to Reproduce** | “1. Go to Profile → 2. Upload .png → 3. See 500 error”                   |
| **Expected vs Actual** | “Expected: Success toast. Actual: Server 500.”                           |
| **Screenshots / Logs** | Add if it saves time or clarifies context.                               |
| **Environment**        | Device, browser, branch, commit hash.                                    |
| **Priority**           | `P1` (Critical), `P2` (Major), `P3` (Minor).                             |

---

### 🧪 Tester Guidelines for Faster Resolution

**1. Write to Reproduce, Not to Impress**
Avoid vague lines like “form not working.”
Instead, describe *how* to make it fail — step by step.

**2. Always Include Environment Info**
Include browser, device, OS, and environment.
Missing this wastes the first 30 minutes of triage.

**3. Test Fresh After Fix**
After a fix is deployed, clear cache, cookies, and session before retesting.

**4. Attach Evidence**
Screenshot, screen recording, or console log = instant clarity.
Developers shouldn’t need to “guess” the bug.

**5. Prioritize Effectively**
Label issues by impact, not annoyance.
A typo ≠ a production blocker.

**6. Verify Related Areas**
If one fix touches others (e.g., login → signup), cross-check those too.

---

### ⚠️ Common SSE (Same Simple Errors) to Avoid

| ❌ Common Slip                        | ✅ Better Practice                                             |
| ------------------------------------ | ------------------------------------------------------------- |
| “Button not working”                 | Mention *which* button and *under what condition*.            |
| No steps to reproduce                | Always include numbered, minimal steps.                       |
| Ignoring console/network errors      | Open DevTools → copy logs or error messages.                  |
| No mention of environment            | Always add OS, browser, device info.                          |
| Reopening fixed issue without retest | Always confirm with clean cache or new session.               |
| Testing only on Chrome               | Test at least on Chrome + Safari (+ Android/iOS if relevant). |

> 💡 *QA isn’t about breaking things — it’s about understanding why they break.*

---

### 🔄 Developer–Tester Loop

| Step         | Action           | Owner                                 | Description |
| ------------ | ---------------- | ------------------------------------- | ----------- |
| 1️⃣ Identify | 🧪 Tester        | Detect and document issue clearly.    |             |
| 2️⃣ Assign   | 🧪 Tester / Lead | Assign to module owner or developer.  |             |
| 3️⃣ Fix      | 💻 Developer     | Fix and link commit to issue ID.      |             |
| 4️⃣ Verify   | 🧪 Tester        | Retest with clean cache, confirm fix. |             |
| 5️⃣ Close    | 🧩 Lead          | Close only after double confirmation. |             |

> ⚙️ *Good QA builds confidence — not confrontation.*

---

### ⚡ Fast-Resolution Etiquette

* Don’t assume the dev “knows what you mean.” Write for **tomorrow you**.
* After re-checking a bug, comment back — “verified ✅” or “still broken ❌”.
* Prefer one clear issue over five vague ones.
* Tag blockers early. Silence delays everyone.

---

### 🔍 Common Misses

* **Old migrations not refreshed** → “DB mismatch” bugs.
* **JS cache** → weird front-end behavior.
* **Env mismatch** → config or `.env` issues.
* **Partial deploy** → missing assets or routes.

---

## 🔁 Iteration & Retrospectives

Bi-weekly retros keep the **system** evolving — not just the product.

* Discuss friction, blockers, or workflow delays.
* Propose one small, meaningful improvement each sprint.
* Document in `/docs/team/retros.md`.
* Celebrate people who improve flow — not just code.

> 🌿 *Continuous improvement beats continuous firefighting.*

---

## 🧭 Collaboration Principles

> “We don’t ship features. We ship teamwork.”

* Talk early, not after it breaks.
* Ask before assumptions — clarify before coding.
* Review PRs and QA tickets with empathy.
* Prefer “we can improve this” over “who did this.”
* Every role — dev, tester, designer — owns quality.

> 💬 *Shared ownership = fewer surprises.*

---

## 📘 Reference Links

* 🧠 [CODEOWNERS Documentation](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-code-owners)
* 🧩 [GitHub Issue Templates](https://docs.github.com/en/issues/tracking-your-work-with-issues/about-issue-and-pull-request-templates)
* 🧪 [Atlassian QA Process Guide](https://www.atlassian.com/continuous-delivery/quality-assurance)
* 💬 [PR Review Guidelines](https://google.github.io/eng-practices/review/)
* 🚀 [Laravel Testing Best Practices](https://laravel.com/docs/testing)

---

## 👨‍💻 Author

**Shakil Alam**
Full Stack Laravel Developer
🔗 GitHub: [@itxshakil](https://github.com/itxshakil)
🌐 [shakiltech.com](https://shakiltech.com)

> ✨ *“A good issue saves an hour of debugging. A clear one saves a day.”*
> Write issues like you’d want to receive them: **fast to read, easy to act on, hard to misunderstand.**

---