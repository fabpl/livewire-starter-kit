# Pest replaces PHPUnit as the test runner

The foundation spec chose PHPUnit 12 deliberately, to stay close to upstream, and warned
that the test runner is "the single most reversible-looking decision here, and the least
reversible in practice". That choice is reversed. Pest 4 becomes the runner, and tests are
written in its functional style exclusively — `it()` and `test()`, with no test classes in
the tree.

This is a consequence rather than a preference. The quality gate requires a coverage
threshold and architecture tests, and PHPUnit 12 provides neither: `phpunit --help` exposes
coverage report writers and no minimum at all. Obtaining both under PHPUnit would mean two
third-party dependencies where Pest bundles them. The original motive for refusing Pest —
proximity to upstream — died with [ADR-0001](0001-enforced-quality-gate-over-upstream-fidelity.md).

The warning about irreversibility is the reason to act now rather than an argument against
acting: the migration costs three files today and grows with every test written afterwards.

## Considered options

**Keep PHPUnit classes, run them under Pest.** Pays the migration without taking its
benefit, and leaves two idioms in the tree for agents to choose between.

**Allow both styles, class-based where fixtures are heavy.** Rejected most firmly. It does
not institutionalise flexibility, it institutionalises a per-file judgement call that no
linter can adjudicate — the opposite of the goal, which is to remove choices from agents.

## Consequences

Pest wraps PHPUnit rather than excluding it: `phpunit.xml` is still read, and
`phpunit/phpunit` remains as a transitive dependency.

The functional style removes a conflict rather than creating one. Pest's `strict()`
architecture preset requires every class to be final and non-abstract, while `Tests\TestCase`
is abstract by necessity; with no test classes in the tree, there is nothing to exempt.

The honest cost: the functional style is a dialect, and the foundation spec argued against
dialects. It is an ecosystem dialect rather than a local one — any agent trained on Laravel
knows Pest — but `it()` is not standard PHP, and that is a real thing given up.
