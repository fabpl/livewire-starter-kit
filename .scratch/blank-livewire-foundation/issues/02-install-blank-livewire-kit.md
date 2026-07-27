# 02 — Install the blank Livewire starter kit into the existing repository

**What to build:** the repository stops being an empty shell and becomes a running
application. A developer clones it, runs the setup command, runs the development
command, opens the local URL and sees the welcome page. They run the quality command
and it passes. Nothing they did not ask for is present: no login screen, no component
library, no tooling beyond what the kit ships.

The existing repository content survives untouched — the commit history, the agent
skills, the agent instruction file and the agent documentation are all still there
afterwards. The installer creates no repository of its own, so the history is not at
risk; the only path that collides is the ignore file, and ticket 01 has already
settled it.

**Blocked by:** 01 — Extend the ignore rules to the framework's artefacts.

**Status:** ready-for-agent

- [ ] The blank Livewire starter kit is installed at the versions recorded in the spec
- [ ] PHPUnit is the test runner and no Pest package is present
- [ ] The database is SQLite and requires no external service to run or test
- [ ] The kit's aggregate quality command completes green, unchanged from what it ships
- [ ] The root route responds successfully and renders the welcome view
- [ ] The asset build completes successfully
- [ ] The application key and the local database file exist after installation
- [ ] Commit history, agent skills, agent instruction file and agent documentation are unchanged
- [ ] No dependency directory or build output appears as untracked in the working tree
- [ ] Nothing is added beyond the kit — no extra package, tool or configuration
- [ ] No authentication scaffolding and no UI component library are present
- [ ] Committed as a single commit following the repository's Conventional Commits convention
