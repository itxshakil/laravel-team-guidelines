# Git and reviews

## Commits

- [Conventional Commits](https://www.conventionalcommits.org/): `feat:`, `fix:`, `refactor:`, `test:`, `docs:`, `chore:`. Add `!` for a breaking change (`chore!: drop Laravel 11 support`). A `commit-msg` hook checks the format.
- The subject says what changed for the user or the codebase. The body says why, especially when the diff alone would mislead.
- One logical change per commit. Formatting-only changes go in their own commit so the real change is reviewable.

## Stage paths, not everything

`git add -A` picks up the `.env` you edited, the debug file you forgot and another session's half-finished work. Stage the files you changed by name.

`git mv` and `git rm` stage immediately. If you move a file while working on something else, the move rides along in your next commit unless you notice. Check `git status` before every commit.

## Pre-commit hook: Rector, then Pint, on staged files

Committed in `.hooks/pre-commit` and installed by `composer install` (a `post-install-cmd` that runs `git config core.hooksPath .hooks`), so nobody has to remember to set it up.

Things we learned writing it:

- **Only staged files.** Running the formatters over the whole project makes every commit slow and drags unrelated changes in.
- **Skip Blade files for Rector.** They end in `.php` but aren't PHP source, and large templates make commits very slow. Pint with `--blade` still formats them.
- **Re-stage what the formatters changed**, or the commit contains the unformatted version.
- **Fail when a tool fails.** Both formatters exit 0 after rewriting a file, so a non-zero exit means the tool itself broke. Abort instead of printing "passed".
- **Pass file lists through `xargs`.** A large refactor stages more paths than one Windows command line can hold ("Argument list too long").
- **Windows with Laravel Herd:** `vendor/bin/pint` and `vendor/bin/rector` use a `#!/usr/bin/env php` shebang. Herd puts `php.bat` on PATH, which Git Bash can't run that way, so both exit 127. Put Herd's versioned PHP directory on PATH, not the directory with `php.bat`, and have the hook print that hint when `php` is missing.

Never skip hooks with `--no-verify`. If a hook fails, fix the cause.

## Line endings

Add a `.gitattributes` with `* text=auto eol=lf`. A script that rewrote files on Windows saved them with CRLF, and an exact-whitespace `assertSeeHtml` started failing for no visible reason.

## Codemods and scripted edits

When you change many files with a script, make it refuse to write when a replacement matched nothing or returned `null`. `preg_replace` returns `null` on a bad pattern, and `file_put_contents($path, null)` writes an empty file. Routes and whole Action classes were emptied this way. Commit before running any bulk edit, so `git checkout` is your undo.

Don't edit code through shell-quoted one-liners (`php -r '...'`, `sed` with quotes). The shell strips quotes inside the code. One such edit turned `createElement('button')` into `createElement(button)`, which still parsed, passed a syntax check, and broke a page in production.

## Pull requests

- Small enough to review in one sitting. If it isn't, say in the description what order to read it in.
- The description says why, how it was tested, and anything a reviewer should look at hardest.
- Run a simplification pass on your own diff before asking for review: dead code, duplicated helpers, names that no longer fit.
- CI must be green before merge. Turn on branch protection with the CI check required. One red badge on `main` teaches everyone to ignore red.

## CI

The gates we run:

| Gate | When |
|---|---|
| Rector, then Pint, on staged files | pre-commit hook |
| Commit message format | commit-msg hook |
| PHPUnit in parallel, coverage report in the summary | every PR and push to `main` |
| Pint check | every PR |
| Larastan with a baseline | every PR |
| Secret scan (gitleaks) | every push |
| `composer validate --strict` | every PR in packages |

## Dependabot

For packages, set `versioning-strategy: widen` on the Composer entry. The default replaced `"phpunit/phpunit": "^11.0|^12.0"` with `"^13.4.5"`. PHPUnit 13 needs PHP 8.4, so every PHP 8.3 job failed, and merging it would have dropped support for half the matrix. Widening gives `^11.0|^12.0|^13.0` instead.

Group dev-dependency updates into one PR a month and let them merge themselves when CI passes. Runtime dependencies that matter for security (a signature-checking library, the payment SDK) get their own PRs and wait for a person.
