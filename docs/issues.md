# Issues

## What a good issue has

- **A title someone can search for later:** "Checkout: 502 after adding a domain when remember-me is on", not "cart broken".
- **For a bug:** steps to reproduce, what you expected, what happened, where (local, staging or production), and the request id or log line if there is one.
- **For a feature:** the problem first, then the solution you have in mind.
- **A checklist** of the work, with file paths when you know them.
- **A "Done when" line** that someone else could check. "Done when a declined renewal is retried on the next scheduled night and the customer gets one email" can be verified. "Improve renewals" can't.

One problem per issue. If it grows parts, make a parent issue with linked children.

Search before opening. If it exists, add what you know there.

## Labels

Exactly one from each group, plus severity for bugs. `scripts/create-labels.sh` creates them.

| Group | Labels |
|---|---|
| Type | `type: bug`, `type: feature`, `type: enhancement`, `type: refactor` |
| Priority | `priority: high`, `priority: medium`, `priority: low` |
| Status | `status: ready`, `status: in-progress`, `status: blocked`, `status: needs-review` |
| Severity (bugs only) | `severity: critical`, `severity: major`, `severity: minor` |

**Agree on what each status means, and write it in the label's description.** Our `status: ready` label's description said "Ready to be worked on", but in practice it came to mean "done and verified". New issues got it from people reading the description, and the board stopped meaning anything. Pick one meaning and make the label description say it.

`status: needs-review` means someone is testing the fix right now. Leave those issues alone until they report back.

Don't add a new label unless the existing ones can't say it, it will be reused, and the team agrees what it means.

## Closing

- A commit or PR that fixes the issue but still needs someone to verify it says `Refs #42`, not `Closes #42`. `Closes` shuts the issue on merge, before anyone has checked it on staging.
- Close with a short note saying how it was verified: "Verified on staging with a test card that needs 3D Secure."
- Close only after the issue's own "Done when" has actually been checked.

## Related issues ship together

Several small fixes in the same area go on one branch and one pull request. You get one review, one CI run and one deploy instead of five, and the fixes are tested against each other.
