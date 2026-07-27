# 03 — Tighten Pint and reformat the tree

**What to build:** Pint moves from the bare `laravel` preset to that preset plus the agreed
additive rules, and the whole tree is reformatted to match. Measured beforehand: strict types
alone reach 26 of the 30 PHP files, so this is a large diff by design and reviewing it means
reviewing the rules rather than the lines.

One rule deserves attention before it runs. `Pint/phpdoc_type_annotations_only` does not tidy
docblocks, it forbids comments — any block or line comment without an `@` annotation is
deleted unless prefixed `@note`, `@warning` or `@todo`. On the current tree it removes the
prose docblocks of `AppServiceProvider` and `User` and a commented-out import, while keeping
every typed annotation.

Three exclusions are deliberate and must not be quietly added back: `final_class` and
`void_return` belong to Rector, and `native_function_invocation` conflicts directly with
`global_namespace_import`.

**Blocked by:** 02

**Status:** ready-for-agent

- [ ] `pint.json` keeps `"preset": "laravel"` and adds the agreed rules
- [ ] `final_class`, `void_return` and `native_function_invocation` are absent
- [ ] `global_namespace_import` is limited to classes
- [ ] The whole tree is reformatted and committed
- [ ] Running Pint a second time produces no further change
- [ ] Running Rector then Pint produces no further change — the chain has a fixed point
- [ ] No path was added to `exclude`, `notPath` or `notName` to make the run pass
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
