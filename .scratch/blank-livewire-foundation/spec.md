# Spec: blank Livewire foundation

Status: resolved

## Problem Statement

The repository is empty. It carries four commits of agent configuration — a commit
convention, an issue tracker convention, domain doc rules — and no application code
whatsoever. Nothing in it says which framework, which frontend approach, which test
runner or which database this project will use.

Every subsequent decision depends on that answer, and right now every subsequent
decision is blocked. A developer opening this repository cannot run it, cannot test
it, and cannot tell what it is meant to become. An agent asked to build a feature has
no conventions to follow and would invent its own.

## Solution

Install the official `laravel/blank-livewire-starter-kit` as the repository's
foundation, unmodified.

The blank kit is the Livewire starter kit with no authentication scaffolding: it
provides the stack and nothing else. After installation the repository has one route,
one view, a working build pipeline, a working test pipeline, and no application
features. The stack becomes a fact recorded in the lockfiles rather than an intention.

Nothing is added on top of what the kit ships. The value of this foundation is that it
is the upstream base, recognisable to anyone who knows the kit, and cheap to re-derive
if it ever needs to be rebuilt.

## User Stories

1. As a developer, I want the repository to declare its framework and version, so that I can tell what I am working on without asking.
2. As a developer, I want a single command that installs every dependency, so that I can go from clone to running in one step.
3. As a developer, I want a single command that starts the application, so that I do not have to remember which processes must run together.
4. As a developer, I want a single command that runs every quality gate, so that I can know whether my change is acceptable before committing it.
5. As a developer, I want the application to answer on a local URL immediately after installation, so that I have proof the foundation works.
6. As a developer, I want a database that requires no external service, so that I can run and test the project on a fresh machine with nothing installed.
7. As a developer, I want the database file created automatically at install time, so that the first migration does not fail on a missing file.
8. As a developer, I want assets compiled by a modern bundler with no configuration on my part, so that styling works from the first minute.
9. As a developer, I want a utility-first CSS framework already wired into the build, so that I do not have to choose and configure one.
10. As a developer, I want the Livewire runtime present and registered, so that I can write my first component without setup work.
11. As a developer, I want a code formatter with an agreed configuration, so that formatting is never a matter of opinion in review.
12. As a developer, I want the quality checks the kit ships to run and pass unchanged, so that I inherit its bar instead of inventing one.
13. As a developer, I want a test runner configured with an example test, so that I have a working pattern to copy.
14. As a developer, I want log tailing available locally, so that I can watch what the application does while I use it.
15. As a developer, I want a containerised environment available but not mandatory, so that I can choose between local and container development later.
16. As a developer, I want no authentication scaffolding, so that I am not deleting login screens I never asked for.
17. As a developer, I want no component library imposed, so that the UI decision stays open until a real screen needs it.
18. As a developer, I want no tooling beyond what the kit ships, so that the foundation stays close to upstream and cheap to update.
19. As a maintainer, I want the foundation to match the published starter kit exactly, so that upstream changes can be diffed against it.
20. As a maintainer, I want the stack pinned in a lockfile, so that a rebuild six months from now produces the same versions.
21. As a maintainer, I want the existing commit history preserved, so that the four commits of agent configuration are not lost to the install.
22. As a maintainer, I want the existing agent configuration preserved, so that the commit convention and issue tracker rules survive the install.
23. As a maintainer, I want the ignore rules to cover both the framework's artefacts and my own machine's, so that neither build output nor editor files reach the history.
24. As a maintainer, I want the installation expressed as a single reproducible command, so that the foundation can be recreated rather than archaeologically reconstructed.
25. As an agent working in this repository, I want a documented quality gate, so that I can verify my own work before reporting it done.
26. As an agent working in this repository, I want the framework conventions to be the standard ones, so that my knowledge of the framework applies directly.
27. As an agent working in this repository, I want no bespoke abstractions in the foundation, so that there is no local dialect to learn first.
28. As a future contributor, I want the absence of authentication to be a recorded decision rather than an oversight, so that I do not add it by accident.
29. As a future contributor, I want the absence of a UI component layer to be a recorded decision, so that I know the choice is mine to make and is expected.

## Implementation Decisions

**Foundation.** The base is `laravel/blank-livewire-starter-kit`, installed via the
Laravel installer. Verified by throwaway scaffolding: `--livewire --no-authentication`
produces dependency-for-dependency the same result as installing the named kit
directly. Either invocation is acceptable; the named kit is preferred because it states
the intent.

**Stack.** PHP `^8.3` (8.5.8 available locally), Laravel `^13.17`, Livewire `^4.1`,
Blaze `^1.0`, Tinker `^3.0`. Frontend: Tailwind CSS 4 and Vite 8 through the Laravel
Vite plugin. Development dependencies: Pint, Larastan, Pail, Pao, Sail, Collision,
Mockery, Faker, PHPUnit 12.

**Test runner.** PHPUnit 12, the kit's default. The installer can substitute Pest via a
flag at install time and this was deliberately declined, to stay close to upstream.

**Database.** SQLite. No external service is required to run or test the project. The
framework's defaults place session, queue and cache on the database connection.

**UI component layer.** None. Tailwind is present, no component library is installed.
The choice is deferred until a real screen requires components. This is a deferral, not
a rejection.

**Authentication.** None. The blank kit ships no authentication scaffolding, and none
is added. The `User` model and its migration arrive with the kit and stay as they come.

**Additions.** None. No package, no tool and no configuration is added on top of what
the kit ships. The foundation is the kit, unmodified.

**Installation into a non-empty repository.** The repository already contains a git
history, agent skills, an agent instruction file and agent documentation. The installer
creates no git repository of its own, so the history is not at risk. Of everything the
kit writes, exactly one path collides with existing content: the ignore file. It is
merged rather than replaced — the kit's rules plus the existing macOS and JetBrains
rules, since the kit covers one JetBrains directory but no macOS metadata. Every other
existing path is untouched because the kit does not contain it.

**Node dependencies.** Installed and built as part of the installation, so that the
development command is functional on first run. npm is the package manager; neither
pnpm nor bun is available locally.

**Commit.** A single commit under the repository's Conventional Commits convention.
The installation is one logical change and does not decompose into commits that stand
up on their own.

## Testing Decisions

**What makes a good test here.** This spec adds no application behaviour, so there is
nothing whose external behaviour could be asserted beyond the framework's own. Tests
written against the foundation itself would assert that the framework works, which is
the framework's job and not this repository's. The correct verification is therefore
that the kit's own pipeline runs green, not that new tests exist.

**Seam.** One, and it already exists: the kit's aggregate test command, which runs every
check the kit ships. No new seam is introduced. This is the highest seam available — it
is the same command a CI run executes, so passing locally and passing in CI cannot
diverge.

**What is verified.**

- The kit's aggregate quality command completes successfully, unchanged from what it ships.
- The kit's smoke test on the root route passes, proving the application boots, routes, and renders.
- The asset build completes, proving the frontend pipeline is wired.
- The application key and database file exist after installation, proving the post-install scripts ran.

**Prior art.** The example unit and feature tests shipped by the kit are the pattern for
tests written later.

**Not verified.** Nothing asserts the absence of authentication or the absence of a
component library. Those are decisions, and a test that asserts a file does not exist
tests the installer rather than the software.

## Out of Scope

- Authentication of any kind, including the framework's first-party options.
- Any UI component library.
- Any tool, package or configuration beyond what the kit ships.
- Continuous integration configuration beyond what the kit ships. The repository has no
  git remote, so any workflow would never run.
- Deployment, hosting, and environment configuration beyond local development.
- Domain modelling. No glossary and no architecture decision records exist yet, and this
  spec creates none. There is no domain here — only a stack.
- Any application feature. The repository after this work has one route and one view.

## Further Notes

The stated purpose of this work is that the foundation determines the stack. That is
worth taking literally: after this spec is implemented, the stack is no longer a matter
of opinion, and reopening it means replacing the foundation rather than amending it.

The single most reversible-looking decision here is the test runner, and it is the least
reversible in practice: the installer substitutes it for free at install time and never
again. Changing it later means rewriting every test by hand or introducing a conversion
tool. It was chosen knowing that.

## Comments

> *This was generated by AI during triage.*

Resolved. Both tickets are complete and their twenty acceptance criteria are all met.
The ignore rules landed first, then the kit itself, installed unmodified. The kit's
aggregate quality command runs green on the committed tree, the root route is covered
by the kit's own feature test, and the asset build was rebuilt from scratch in place
rather than accepted from the scaffold.

Marked `resolved` rather than one of the five triage roles, which describe what remains
to be done and have no terminal state. `resolved` is the word this repository's tracker
configuration already uses for completed work in its wayfinding section.
