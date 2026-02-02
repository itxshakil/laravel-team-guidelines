# 📌 Issue Labeling Standards

> **Consistent. Predictable. Scannable.**
> Labels help the team quickly understand *what* an issue is, *how urgent* it is, and *what state* it’s in — without reading the entire description.

---

## 📑 Table of Contents

* [🎯 Purpose](#-purpose)
* [🏷️ Label Categories](#️-label-categories)

  * [Type](#type)
  * [Priority](#priority)
  * [Status](#status)
  * [Severity (Bugs Only)](#severity-bugs-only)
* [🧠 Labeling Guidelines](#-labeling-guidelines)
* [📌 Examples](#-examples)

---

## 🎯 Purpose

To maintain **clarity, consistency, and shared understanding** across all GitHub Issues and Pull Requests by standardizing label usage.

Labels answer four questions instantly:

1. What kind of work is this?
2. How urgent is it?
3. What’s its current state?
4. How bad is it (for bugs)?

---

## 🏷️ Label Categories

### Type

> **Exactly one `type:` label is required on every issue.**

| Label               | Color     | Description                                    |
| ------------------- | --------- | ---------------------------------------------- |
| `type: bug`         | `#d73a4a` | Something is broken or behaving incorrectly.   |
| `type: feature`     | `#0e8a16` | A new feature or capability.                   |
| `type: enhancement` | `#1d76db` | Improvement to an existing feature.            |
| `type: refactor`    | `#6f42c1` | Internal code changes with no behavior change. |

---

### Priority

> **Exactly one `priority:` label is required.**

| Label              | Color     | Description                                     |
| ------------------ | --------- | ----------------------------------------------- |
| `priority: high`   | `#b60205` | Critical, blocks release or core functionality. |
| `priority: medium` | `#fbca04` | Important but not blocking.                     |
| `priority: low`    | `#0e8a16` | Optional or low-impact work.                    |

---

### Status

> **Status reflects the current lifecycle stage of the issue.**

| Label                  | Color     | Description                                  |
| ---------------------- | --------- | -------------------------------------------- |
| `status: ready`        | `#2cbe4e` | Clearly defined and ready to start.          |
| `status: in-progress`  | `#0052cc` | Actively being worked on.                    |
| `status: blocked`      | `#b60205` | Cannot proceed due to dependency or issue.   |
| `status: needs-review` | `#5319e7` | Work completed, awaiting review or approval. |

---

### Severity (Bugs Only)

> ⚠️ **Use only when `type: bug` is applied.**

| Label                | Color     | Description                                       |
| -------------------- | --------- | ------------------------------------------------- |
| `severity: critical` | `#7f0000` | System outage, data loss, or security risk.       |
| `severity: major`    | `#d93f0b` | Major functionality broken; workaround may exist. |
| `severity: minor`    | `#fef2c0` | Cosmetic or low-impact issue.                     |

---

## 🧠 Labeling Guidelines

### Mandatory Rules

* Every issue **must have**:

  * `type`
  * `priority`
  * `status`
* Bugs **must also have** a `severity`
* Do **not** apply multiple labels from the same category

---

### Workflow Rules

* `status: ready` → when scope and acceptance criteria are clear
* `status: in-progress` → when someone starts work
* `status: needs-review` → when a PR is opened
* `status: blocked` → when progress cannot continue

> 💡 Labels should evolve as the issue progresses.

---

### When NOT to Create New Labels

Avoid creating new labels unless:

1. The meaning is not covered by existing labels
2. The label will be reused consistently
3. The team agrees on its purpose

> Consistency beats creativity.

---

## 📌 Examples

### Example 1: Production Bug

**Issue:** Checkout fails when payment gateway times out

```
type: bug
priority: high
severity: major
status: in-progress
```

---

### Example 2: New Feature

**Issue:** Add domain privacy purchase option

```
type: feature
priority: medium
status: ready
```

---

### Example 3: Code Cleanup

**Issue:** Refactor order service for readability

```
type: refactor
priority: low
status: ready
```

---

## 🔗 Related Docs

* [🌿 Branching & Git Strategy](./git-standards.md)
* [🧠 Conventional Commits](https://www.conventionalcommits.org)

---

## 👨‍💻 Author

**Shakil Alam**
Full Stack Laravel Developer
GitHub: [@itxshakil](https://github.com/itxshakil)

---
