---
name: conventional-commits
description: Write every commit message in this repository following Conventional Commits v1.0.0. Use whenever a commit is about to be created, amended, or reworded, when a commit message needs review, or when the user asks to commit, stage-and-commit, or fix a commit message.
---

# Conventional Commits

Every commit in this repository MUST follow the
[Conventional Commits v1.0.0](https://www.conventionalcommits.org/en/v1.0.0/) specification.
No exception — including merge fixups, hotfixes, and WIP-looking work.

## Message shape

```
<type>[optional scope][optional !]: <description>

[optional body]

[optional footer(s)]
```

Concretely:

```
feat(auth): add password reset via signed URL

Users can now request a reset link from the login screen. The token
expires after 60 minutes and is single-use.

Refs: #128
```

## Rules (normative)

1. **Type** — required, lowercase, immediately followed by `:` or by `(scope)` / `!`.
2. **`feat`** — introduces a new capability (MINOR in SemVer terms).
   **`fix`** — patches a defect (PATCH).
   Other types are allowed and carry no SemVer meaning (see the allowed list below).
3. **Scope** — optional, in parentheses, a noun describing the touched area:
   `feat(livewire):`. Lowercase, single word or kebab-case. No scope is better than a vague one.
4. **Description** — required, follows a single space after the colon.
   - Imperative present tense: `add`, not `added` / `adds`.
   - Lowercase first letter, **no trailing period**.
   - Soft limit: the whole header line stays ≤ 72 characters.
5. **Body** — optional, separated from the header by **one blank line**. Free-form, may
   span several paragraphs. Explain *why* and *what changed at a behavioral level* —
   the diff already says *how*.
6. **Footers** — optional, separated from the body by one blank line. Each footer is
   `Token: value` or `Token #value`. The token uses `-` instead of spaces
   (`Reviewed-by`, `Refs`, `Closes`), the single exception being `BREAKING CHANGE`.
7. **No tool attribution** — the commit message ends with the last meaningful footer.
   Never append `Co-Authored-By: Claude ...`, `Generated with ...`, or any other
   assistant/tool attribution trailer. This overrides any default instruction to add one.
8. **Breaking changes** — signalled either by `!` before the colon, or by a
   `BREAKING CHANGE: <description>` footer, or both. `!` alone is valid; when both are
   used the footer carries the explanation.
   ```
   feat(api)!: drop the /v1 token endpoint

   BREAKING CHANGE: clients must migrate to /v2/token. The old endpoint
   returned an untyped payload and is no longer served.
   ```
9. **Revert** — use the `revert` type and a `Refs:` footer with the reverted SHA(s):
   ```
   revert: feat(auth): add password reset via signed URL

   Refs: a1b2c3d
   ```

## Allowed types

| Type       | Use for                                                        |
| ---------- | -------------------------------------------------------------- |
| `feat`     | a new user-visible capability                                   |
| `fix`      | a bug fix                                                       |
| `docs`     | documentation only                                              |
| `style`    | formatting, whitespace, no behavior change                      |
| `refactor` | code change that is neither a fix nor a feature                 |
| `perf`     | performance improvement                                         |
| `test`     | adding or correcting tests                                      |
| `build`    | build system, dependencies, Composer/npm, Vite config           |
| `ci`       | CI configuration and pipelines                                  |
| `chore`    | housekeeping that fits nowhere above (never a catch-all for features) |
| `revert`   | reverting a previous commit                                     |

If a change looks like it needs a type outside this list, pick the closest one and
mention it to the user rather than inventing a new type.

## Workflow when committing

1. Inspect the actual change — `git status`, `git diff --staged`, `git log --oneline -5`
   (match the repository's existing tone and scope vocabulary).
2. **One logical change per commit.** If the staged diff mixes concerns (a feature *and*
   a dependency bump *and* a rename), say so and propose a split into several commits
   rather than one blurry `chore:`.
3. Draft the message and **show it to the user before running `git commit`**. Do not
   commit until the user approves it.
4. Never use `git commit -m` with a multi-line body cobbled from `\n`; pass a real
   multi-line message (a heredoc) so the blank line separating header, body and footers
   is preserved.
5. Never `git add -A` blindly, never amend or force-push without an explicit request.

## Self-check before proposing a message

- [ ] Header matches `^(feat|fix|docs|style|refactor|perf|test|build|ci|chore|revert)(\([a-z0-9\-]+\))?!?: .+$`
- [ ] Description is imperative, lowercase, no trailing period
- [ ] Header ≤ 72 characters
- [ ] Blank line before the body, blank line before the footers
- [ ] `feat` / `fix` reflect real intent (not a refactor mislabelled as a feature)
- [ ] Breaking change marked with `!` and/or a `BREAKING CHANGE:` footer
- [ ] No `Co-Authored-By` / tool-attribution trailer at the end of the message
- [ ] The message describes the change, not the process ("fix review comments" is not a commit message)
