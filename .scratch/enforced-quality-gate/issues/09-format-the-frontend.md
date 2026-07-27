# 09 — Format the frontend and documentation with Prettier

**What to build:** Prettier takes ownership of everything that is not plain PHP — Blade, CSS,
JavaScript, YAML, Markdown — with Tailwind classes sorted inside the templates, and a boundary
with Pint that is enforced rather than assumed.

The plugin combination was settled by measurement, not reputation, and the losing option is
worth naming so it is not proposed again. With `@shufo/prettier-plugin-blade`, Blade is
formatted but Tailwind classes are **not** sorted — Tailwind's plugin monopolises a Prettier
API and lists twelve plugins it has explicit workarounds for, none of them Blade. With
`prettier-plugin-blade`, which declares Tailwind's plugin as a peer dependency, sorting works.
The third plugin, `@prettier/plugin-php`, is not an addition: npm will demand it as a peer
dependency of the first.

That third plugin is also the risk. It registers itself on `.php` and was measured claiming
an ordinary source file in a probe — two fixers on PHP, which the ownership rule forbids.
`.prettierignore` is therefore structural, not incidental: it is what materialises the
Pint/Prettier boundary, and the acceptance criterion is that Prettier's claim is *verified*
rather than assumed.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] `prettier`, `prettier-plugin-blade`, `prettier-plugin-tailwindcss` and `@prettier/plugin-php` are dev dependencies
- [ ] `prettier-plugin-tailwindcss` is last in the plugin list
- [ ] `tailwindStylesheet` points at `resources/css/app.css`
- [ ] `singleQuote: true` is set, so Blade output agrees with Pint rather than rewriting `__('x')` to `__("x")`
- [ ] Indentation agrees with `.editorconfig`, verified file by file rather than assumed
- [ ] `.prettierignore` carries `*.php` followed by `!*.blade.php`
- [ ] Prettier is verified to claim no plain PHP file
- [ ] `composer.json`, `package.json`, `composer.lock` and `package-lock.json` are excluded
- [ ] `npm run format` and `npm run format:check` exist
- [ ] `composer test` calls `format:check`
- [ ] The CI workflow runs `npm ci`
- [ ] Tailwind classes are demonstrably sorted in a Blade file
- [ ] Every Blade directive in `welcome.blade.php` survives unchanged
- [ ] The whole eligible tree is formatted and committed
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
