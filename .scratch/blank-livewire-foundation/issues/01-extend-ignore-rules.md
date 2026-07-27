# 01 — Extend the ignore rules to the framework's artefacts

**What to build:** the repository refuses to track anything that is generated rather
than authored — dependency directories for both PHP and Node, compiled asset output,
the local environment file, and the local database. A developer who installs the
framework immediately afterwards sees only authored files in their working tree, with
no risk of committing a dependency tree by accident.

This lands *before* any framework code, and that order is the point. If the framework
arrives first, its generated directories show up as untracked and the installation can
no longer be committed cleanly without dealing with ignore rules in the same breath.
Expanding the rules first keeps each step independently committable.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [x] Dependency directories for PHP and for Node are ignored
- [x] Compiled asset output and the development-server marker files are ignored
- [x] Local environment files are ignored, while the committed example environment file is not
- [x] The local database file is ignored
- [x] Framework-generated storage artefacts are ignored
- [x] The existing macOS and JetBrains rules survive unchanged
- [x] Each rule is verified with git's own ignore-check rather than by reading the file
- [x] Committed as a single commit following the repository's Conventional Commits convention
