# 02 — Install Rector and rewrite the tree

**What to build:** structural rewriting becomes part of the chain. Rector is installed with
its Laravel rule package, configured with the agreed sets, run once over the whole tree, and
its output committed.

Rector lands before Pint because it emits code in its own formatting and Pint normalises
afterwards — the reverse order leaves the tree dirty. It lands before the static-analysis
raise in ticket `04` because the type declarations it adds are what make level 10 reachable
without hand-written annotations.

Enabled prepared sets: `deadCode`, `codeQuality`, `typeDeclarations`, `privatization`,
`earlyReturn`, `if`, `instanceOf`, `rectorPreset`, `carbon`. Every other flag stays off, and
three of the exclusions are load-bearing rather than incidental: `codingStyle` overlaps Pint
frontally, `naming` is the only set that rewrites identifiers, and `typeDeclarationDocblocks`
would make Rector a second docblock writer alongside Pint. The reasoning is in the spec.

**Blocked by:** 01

**Status:** ready-for-agent

- [ ] `rector/rector` and `driftingly/rector-laravel` are dev dependencies
- [ ] `rector.php` enables exactly the nine agreed prepared sets and no others
- [ ] `codingStyle`, `naming`, `typeDeclarationDocblocks` and `namedArgs` are absent
- [ ] The PHPUnit, Doctrine and Symfony sets are absent
- [ ] PHP sets resolve from `composer.json` rather than a pinned constant
- [ ] Laravel sets resolve from `composer.json` rather than a pinned constant
- [ ] Rector's paths cover the same tree the rest of the chain covers
- [ ] `composer refactor` applies rewrites and `composer refactor:check` reports without writing
- [ ] `composer test` runs the check form and fails on a tree Rector would change
- [ ] Running Rector a second time produces no further change
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
