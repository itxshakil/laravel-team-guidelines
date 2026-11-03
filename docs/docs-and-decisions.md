# Docs and decisions

## Where things go

| Kind | Where | What it's for |
|---|---|---|
| Decision | `docs/adr/` | Why we chose this, so it isn't re-argued without new information |
| Procedure | `docs/runbooks/` | Step by step for an operation or incident, including what went wrong last time |
| Reference | `docs/reference/` | How a shipped system works |
| Customer or API guide | `docs/guides/` | For people outside the team |
| Convention or trap | a rules file, and a test when it can be checked | "Always do X here", "Y looks wrong but is deliberate" |
| Open work | a GitHub issue | Anything still to do |

`docs/README.md` lists every doc. Nothing new goes at the top level of `docs/`.

## Open work is an issue, never a doc

We once had about 70 plan, backlog and audit files in `docs/`. Half their status lines were out of date, because nothing made anyone update them. They all moved into issues, each with a checklist, file paths and a "Done when" line.

Close an issue only after its own verification steps have actually run, and check its claims against the code, not its self-reported status.

## Writing an ADR

Short, numbered, and never edited once accepted, except for its status.

```markdown
# ADR 0023: Three queue workers, one process each

## Status
Accepted (2026-10-15). Supersedes ADR 0016.

## Context
What was happening, with numbers. What we considered.

## Decision
Numbered points, each a rule someone can follow.

## Consequences
What this costs, and what now has to be true.
```

When later work changes a decision, write a new ADR that supersedes the old one and update the old one's status in the same change. Keep an index table (`docs/adr/README.md`) with one line per ADR: number, decision and area.

Good ADR candidates: anything someone will want to "clean up" later. Deliberate denormalisation, a disabled TLS check on an internal endpoint, a library you chose not to use.

## Comments that still make sense in a year

- Say why, not what. The code already says what it does.
- Comment the surprising parts: a workaround, an ordering constraint, a guard that exists because of a specific failure.
- Point at an ADR, runbook or reference doc for the long reason. **Never point at an issue.** The issue closes, the link rots, and the reason was only ever in the comment thread.
- Don't restate the method name in a docblock. If that's all there is to say, delete the docblock.

## Rules for people and for AI agents

Rules files matched to paths (`app/Jobs/**`, `resources/css/**`), each rule a short heading plus the reason, work well for both new teammates and coding agents. Write them for the person first. A rule that only an agent can follow, full of "MUST" and "NEVER" with no reason, gets ignored by people and misapplied by agents.

When a rule can be checked mechanically, add a test and say in the rule which test enforces it.

## When work finishes

Keep only what still does a job. There's no archive folder. Git history and the issue keep the old text.

- A decision someone could re-argue becomes an ADR.
- A trap that should be caught next time becomes a rule, and a test if possible.
- How the system now works goes into reference; how to operate it goes into a runbook.

## Moving or deleting a doc

Code comments, `.env.example`, deploy scripts and Dockerfiles cite doc paths. After `git mv` or `git rm`, run `git grep -n "old/path"` and fix every hit, including relative links inside other docs.
