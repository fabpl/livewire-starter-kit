<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/*
 * @note A new seam: the suite reads the frontend source tree. No existing seam reached it,
 * because resolving a Token otherwise requires a browser and the browser suite does not block.
 * The shape is the routing test's — an invariant asserted by traversal, with the reason for the
 * traversal written where it happens.
 *
 * This is not the class of test the quality-gate spec refused, one that reads a configuration
 * file and restates what it says so the two can drift. Nothing is restated: the colours are
 * declared once, in the stylesheet; the thresholds come from WCAG 2.2 level AA; the test holds
 * neither and derives a consequence from them.
 *
 * Assertions are made against the thresholds and never against an expected ratio. A pinned
 * ratio would be a change detector reddening on every adjustment of hue, and it has never
 * caught a defect.
 */

/* @note The two thresholds of WCAG 2.2 level AA: 1.4.3 for text, 1.4.11 for component
   boundaries and focus indicators. They are named rather than inlined so that a pairing below
   reads as a decision about what sits on what, and never as a decision about how much contrast
   is enough — that one belongs to the standard. */
const CONTRAST_TEXT = 4.5;

const CONTRAST_BOUNDARY = 3.0;

/*
 * @note Which foreground sits on which background is knowledge the stylesheet does not carry,
 * so the pairings are declared here — and no colour value is.
 *
 * Decorative separators are absent on purpose rather than by oversight, and this is the one
 * judgement in the file worth defending. 1.4.11 reaches what is "required to identify a user
 * interface component": `--input` is the boundary of a control and is held at 3:1, while
 * `--border` and `--sidebar-border` draw card edges and rules that identify nothing. Holding
 * those two would darken every surface edge in the kit — the published `--border` sits at
 * 1.34:1 against the stage — to satisfy a rule the standard does not impose, and it would cost
 * the theme the warm, low-contrast character that is the reason for adopting it. ADR-0004
 * settles that contrast outranks fidelity; it does not license inventing thresholds.
 */

/**
 * @return list<array{string, string, float}>
 */
function contrastPairings(): array
{
    return [
        ['foreground', 'background', CONTRAST_TEXT],
        ['card-foreground', 'card', CONTRAST_TEXT],
        ['popover-foreground', 'popover', CONTRAST_TEXT],
        ['muted-foreground', 'background', CONTRAST_TEXT],
        ['muted-foreground', 'muted', CONTRAST_TEXT],
        ['primary-foreground', 'primary', CONTRAST_TEXT],
        ['secondary-foreground', 'secondary', CONTRAST_TEXT],
        ['accent-foreground', 'accent', CONTRAST_TEXT],
        ['destructive-foreground', 'destructive', CONTRAST_TEXT],
        ['sidebar-foreground', 'sidebar', CONTRAST_TEXT],
        ['sidebar-primary-foreground', 'sidebar-primary', CONTRAST_TEXT],
        ['sidebar-accent-foreground', 'sidebar-accent', CONTRAST_TEXT],
        ['input', 'background', CONTRAST_BOUNDARY],
        ['ring', 'background', CONTRAST_BOUNDARY],
        ['sidebar-ring', 'sidebar', CONTRAST_BOUNDARY],
    ];
}

/* @note The hexadecimal declarations of one scope of the stylesheet, keyed by Token name. Only
   six-digit hexadecimal is matched, which is what keeps `--radius` and the font stacks out
   without naming them here.

   Comments are stripped before anything is matched. Every moved Token carries a note quoting
   the published value it moved away from, so the file is full of hexadecimal that must not be
   read as a declaration — and a note phrased `--primary: #c96442;` would otherwise be checked
   in place of the value that actually ships, which is the one failure this test could not
   afford to miss. */

/**
 * @return array<string, string>
 */
function themeScope(string $selector): array
{
    $stylesheet = Str::of(File::get(base_path('resources/css/app.css')))
        ->replaceMatches('#/\*.*?\*/#s', '')
        ->toString();

    $pattern = '/'.preg_quote($selector, '/').'\s*\{(.*?)\n\}/s';

    expect(preg_match($pattern, $stylesheet, $block))->toBe(1, "The stylesheet declares no `{$selector}` block.");

    preg_match_all('/--([a-z0-9-]+):\s*(#[0-9a-f]{6})\s*;/i', $block[1], $declarations, PREG_SET_ORDER);

    $tokens = [];

    foreach ($declarations as $declaration) {
        $tokens[strtolower($declaration[1])] = strtolower($declaration[2]);
    }

    return $tokens;
}

/* @note A theme as the cascade resolves it. The dark scope declares only what it must, so every
   token it leaves out inherits the value declared at the root — and the inherited pair is
   exactly what has to be checked, since that is what a reader in dark mode sees. */

/**
 * @return array<string, string>
 */
function resolvedTheme(string $theme): array
{
    $root = themeScope(':root');

    return $theme === 'dark' ? [...$root, ...themeScope('.dark')] : $root;
}

/* @note Relative luminance and contrast ratio, both as the standard defines them. This is the
   whole of what the test knows that the stylesheet does not, and it is arithmetic rather than a
   restatement of any value declared elsewhere. */
function relativeLuminance(string $hex): float
{
    $channels = [];

    foreach ([1, 3, 5] as $offset) {
        $channel = hexdec(substr($hex, $offset, 2)) / 255;

        $channels[] = $channel <= 0.04045 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
    }

    return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

function contrastRatio(string $foreground, string $background): float
{
    $first = relativeLuminance($foreground);
    $second = relativeLuminance($background);

    return (max($first, $second) + 0.05) / (min($first, $second) + 0.05);
}

/*
 * @note A pairing naming a Token the stylesheet does not declare is accumulated like any other
 * failure rather than asserted on the spot, so that one run reports every pairing that is wrong
 * instead of stopping at the first.
 */
it('meets the level AA threshold for every declared pairing in both themes', function (): void {
    $failures = [];

    foreach (['light', 'dark'] as $theme) {
        $tokens = resolvedTheme($theme);

        foreach (contrastPairings() as [$foreground, $background, $threshold]) {
            if (! isset($tokens[$foreground], $tokens[$background])) {
                $failures[] = sprintf('%s: --%s on --%s names a Token the stylesheet does not declare', $theme, $foreground, $background);

                continue;
            }

            $ratio = contrastRatio($tokens[$foreground], $tokens[$background]);

            if ($ratio < $threshold) {
                $failures[] = sprintf('%s: --%s on --%s is %.2f:1, below %.1f:1', $theme, $foreground, $background, $ratio, $threshold);
            }
        }
    }

    expect($failures)->toBe([]);
});

/*
 * @note Without this, a Token added later escapes the check by being forgotten rather than by
 * being argued for. It is the reason the pairings above may be incomplete only deliberately.
 *
 * Both scopes are read, not just the root one. A Token declared only inside the dark scope is
 * still a Token that has to sit on something, and reading one scope would let it through
 * unpaired — the exact escape this test exists to close.
 */
it('pairs every foreground token with a background', function (): void {
    $declared = [...array_keys(resolvedTheme('light')), ...array_keys(resolvedTheme('dark'))];

    $foregrounds = array_unique(array_filter(
        $declared,
        static fn (string $token): bool => str_ends_with($token, '-foreground'),
    ));

    $paired = array_column(contrastPairings(), 0);

    expect(array_values(array_diff($foregrounds, $paired)))->toBe([]);
});
