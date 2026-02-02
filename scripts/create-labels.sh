#!/bin/bash

echo "Creating GitHub labels..."

# -----------------
# Type
# -----------------
gh label create "type: bug" --color d73a4a --description "Something isn’t working as expected" --force
gh label create "type: feature" --color 0e8a16 --description "A new feature or capability" --force
gh label create "type: enhancement" --color 1d76db --description "Improvement to an existing feature" --force
gh label create "type: refactor" --color 6f42c1 --description "Code changes without behavior change" --force

# -----------------
# Priority
# -----------------
gh label create "priority: high" --color b60205 --description "Critical and release-blocking" --force
gh label create "priority: medium" --color fbca04 --description "Important but not blocking" --force
gh label create "priority: low" --color 0e8a16 --description "Low urgency or optional" --force

# -----------------
# Status
# -----------------
gh label create "status: ready" --color 2cbe4e --description "Ready to be worked on" --force
gh label create "status: in-progress" --color 0052cc --description "Currently being worked on" --force
gh label create "status: blocked" --color b60205 --description "Blocked by dependency or issue" --force
gh label create "status: needs-review" --color 5319e7 --description "Awaiting review or feedback" --force

# -----------------
# Severity (Bugs)
# -----------------
gh label create "severity: critical" --color 7f0000 --description "System outage, data loss, or security risk" --force
gh label create "severity: major" --color d93f0b --description "Major functionality impacted" --force
gh label create "severity: minor" --color fef2c0 --description "Minor or cosmetic issue" --force

echo "✅ All labels created successfully."
