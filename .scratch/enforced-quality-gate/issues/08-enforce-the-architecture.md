# 08 — Enforce the architecture and correct what it forbids

**What to build:** structural drift starts failing the suite instead of accumulating quietly.
Classes are final, none are abstract, none expose protected methods, debugging and unsafe
functions are rejected, framework conventions are checked — and no web route action is a
closure, so business logic cannot hide outside the perimeter that the next ticket measures.

Configuration and code corrections land together, in one ticket, because separating them would
mechanically produce a red commit: the rules fail on the tree as it stands. Three collisions
are known and their resolutions already decided. An abstract, empty controller base that
nothing extends is deleted. A protected helper on the service provider becomes private. And a
model's cast declaration becomes **public** rather than private — necessarily so, because the
framework declares it protected and the language forbids narrowing visibility on override.

The route-closure rule is not an architecture assertion and cannot be one: the architecture
rules operate on classes and namespaces, and a route file declares neither, so the file is
invisible to them. It is an ordinary test reading the framework's own route registry, where a
single assertion covers every route present and future.

No exemptions anywhere. Rule sets cannot be cherry-picked — exemptions apply to namespaces,
never to individual expectations — so if the prohibition on protected methods proves
intolerable against the framework's protected extension points, the recorded fallback is to
write the individual expectations by hand minus that one. **That is a decision to raise with a
human, not to take in passing.**

**Blocked by:** 04, 06

**Status:** ready-for-agent

- [ ] The four agreed rule sets are active in the suite
- [ ] No exemption appears anywhere in the architecture rules
- [ ] The empty abstract controller base is deleted
- [ ] The service provider's protected helper is private
- [ ] The model's cast declaration is public
- [ ] Application classes are final wherever Rector can finalise them
- [ ] A test asserts that no web route action is a closure
- [ ] The treatment of the shipped console closure is decided and recorded — either the rule targets web routes only, or that command becomes a class
- [ ] Whether the rules reach the test namespace is established by observation, and the finding recorded in the spec
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
