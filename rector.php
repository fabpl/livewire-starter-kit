<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bin',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/public',
        __DIR__.'/routes',
        __DIR__.'/tests',

        /*
         * @note This file. It is the newest PHP in the tree and was reaching only Pint, which
         * made the tool that rewrites everything the one thing it did not rewrite.
         */
        __FILE__,
    ])

    /*
     * @note Both targets are read from composer.json rather than written here: the language
     * floor from `require.php`, the framework version from the installed `laravel/framework`.
     * A pinned constant would be a second place to state a version, free to disagree with the
     * first.
     */
    ->withPhpSets()
    ->withSetProviders(LaravelSetProvider::class)
    ->withComposerBased(laravel: true)

    /*
     * @note Enabled deliberately, and nothing else. Left off: the coding-style and
     * docblock-type groups, which belong to Pint; the naming group, the only group in the
     * chain that rewrites identifiers; named-argument conversion, which is churn without
     * benefit; the strict-boolean group, which Rector itself deprecates as risky; and the
     * PHPUnit, Doctrine and Symfony groups, which are out of stack.
     */
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        if: true,
        earlyReturn: true,
        carbon: true,
        rectorPreset: true,
    )

    ->withSkip([
        /*
         * @note The framework regenerates this directory on every package discovery and git
         * ignores it, so it is not part of the tree. Rewriting it would put Rector in
         * competition with the owner of those files and turn the check red on an unrelated
         * install. Pint excludes it for the same reason.
         */
        __DIR__.'/bootstrap/cache',

        /*
         * @note A coding-style rule that the recommended set carries in past the coding-style
         * group being off. Pint's preset writes `$i++` where this writes `++$i`, so the two
         * rewrite each other without end and `composer fix` never terminates. Increment style
         * is formatting, and formatting belongs to Pint — so Rector yields here, exactly as
         * Pint yields on finality and return types.
         */
        PostIncDecToPreIncDecRector::class,

        /*
         * @note Strict-type declarations belong to Pint, which is the whole reason these two
         * are named here: the code-quality set carries one and the recommended set the other,
         * and leaving either enabled would give one concern two owners. It is not a redundancy
         * that would merely be untidy — the safe variant declines files it judges risky, so
         * Rector alone covers the tree unevenly, and Pint's rule covers it whole.
         */
        DeclareStrictTypesRector::class,
        SafeDeclareStrictTypesRector::class,

        /*
         * @note Strict equality belongs to Pint too. This is the only rule in the enabled
         * groups whose whole effect is rewriting `==` to `===`, and Pint's rule does the same
         * to every comparison rather than only to the ones whose types it can prove equal — so
         * keeping this one would give the concern two owners and cover the tree with the
         * weaker of the two anywhere Pint had not yet run.
         */
        UseIdenticalOverEqualWithSameTypeRector::class,

        /*
         * @note Docblocks belong to Pint, and this rule writes them: it stamps a `@see` on
         * every class pointing at its test. The recommended set carries it in past the
         * docblock-type group being off, so it needs naming here or Rector becomes a second
         * docblock writer — the oscillation the ownership rule exists to prevent. Dormant
         * today only because the functional test style leaves no test classes for it to pair
         * a class with.
         */
        AddSeeTestAnnotationRector::class,
    ])

    /*
     * @note Each checkout gets its own cache directory rather than sharing one under the
     * system temporary directory. The reasoning is stated once, next to phpstan.neon's
     * `tmpDir`.
     */
    ->withCache(__DIR__.'/.rector.cache');
