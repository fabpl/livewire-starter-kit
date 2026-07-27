# 03 — Format the frontend and documentation with Prettier

**What to build:** everything that is not plain PHP gets formatted mechanically — templates,
styles, scripts, workflow files and documentation — with utility classes sorted inside the
templates. A developer stops deciding how a template is indented or in what order its classes
appear, and reviewers stop mentioning it.

The plugin combination is already settled by measurement, and the losing option is worth
naming so it is not proposed again: the better-known Blade plugin formats templates but leaves
Tailwind classes **unsorted**, because the Tailwind plugin monopolises a Prettier API and
carries workarounds for a fixed list of plugins that includes no Blade plugin. The chosen
Blade plugin declares the Tailwind plugin as a peer dependency, and sorting then works. A
third plugin, for PHP, is not an addition — npm demands it as a peer dependency of the first.

That third plugin is also the risk in this ticket. It registers itself on the PHP extension
and was measured claiming an ordinary source file in a probe, which would put two fixers on
PHP and break the ownership rule. Prettier's ignore file is what materialises the boundary
with Pint, so the criterion below is that Prettier's reach is *verified*, not assumed.

This ticket blocks nothing and nothing blocks it — it can run in parallel with the whole PHP
chain.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Prettier and its three plugins are development dependencies, with the Tailwind plugin loaded last
- [ ] The Tailwind plugin is pointed at the project's stylesheet so it sorts against the real theme
- [ ] Quote style is set explicitly, so template output agrees with Pint instead of switching to double quotes
- [ ] Indentation agrees with the editor configuration, verified file by file rather than assumed
- [ ] Prettier is verified to claim no plain PHP file, and to still claim templates
- [ ] Dependency manifests and lockfiles are excluded, so installing a dependency cannot turn the pipeline red
- [ ] A fix command and a check command both exist
- [ ] The aggregate command runs the check command
- [ ] The pipeline installs the frontend dependencies before running the gate
- [ ] Utility classes are demonstrably sorted in a template
- [ ] Every directive in the existing template survives the reformat unchanged
- [ ] The whole eligible tree is formatted and committed
- [ ] Running Prettier a second time produces no further change
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
