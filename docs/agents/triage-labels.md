# Triage Labels

The skills speak in terms of five canonical triage roles. This file maps those roles to the actual label strings used in this repo's issue tracker.

| Label in mattpocock/skills | Label in our tracker | Meaning                                  |
| -------------------------- | -------------------- | ---------------------------------------- |
| `needs-triage`             | `needs-triage`       | Maintainer needs to evaluate this issue  |
| `needs-info`               | `needs-info`         | Waiting on reporter for more information |
| `ready-for-agent`          | `ready-for-agent`    | Fully specified, ready for an AFK agent  |
| `ready-for-human`          | `ready-for-human`    | Requires human implementation            |
| `wontfix`                  | `wontfix`            | Will not be actioned                     |

When a skill mentions a role (e.g. "apply the AFK-ready triage label"), use the corresponding label string from this table.

Edit the right-hand column to match whatever vocabulary you actually use.

## Terminal state

The five roles above describe what still has to happen; none of them means "finished". On a
tracker with a close operation, completion is expressed by closing the issue. This repo's
tracker is markdown files, and a file has nothing to close — so completion needs a state of
its own.

| Label      | Meaning                                                                     |
| ---------- | --------------------------------------------------------------------------- |
| `resolved` | The work is done and its acceptance criteria are met — nothing left to pick up |

Applies to specs and tickets alike, written on the same `Status:` line as the roles above.
The word is the one the wayfinding conventions in `issue-tracker.md` already use for
completed tickets.

`resolved` is local to this repo and has no counterpart among the canonical roles, so no
skill looks for it. That is enough in practice: skills look for `ready-for-agent`, and a
resolved item no longer carries it.
