<?php

declare(strict_types=1);

/*
 * @note The needles are patterns rather than literals, and each one carries a single-character
 * class where the plain string would carry a letter. That is what stops this file from
 * reporting itself, and it replaces the per-file exemption that used to sit in the loop below:
 * the check that exists because this repository grants no waiver was granting exactly one.
 * Matching is case-insensitive, which is what lets a single pattern cover the annotation and
 * the attribute form of the same suppression.
 */
$forbidden = [
    '/codeCoverage[I]gnore/i',
    '/pest-mutate[-]ignore/i',
];

$phpSources = ['*.php', 'artisan'];

$root = dirname(__DIR__);

$files = [];
$status = 0;

exec(
    sprintf(
        'git -C %s ls-files --cached --others --exclude-standard -- %s',
        escapeshellarg($root),
        implode(' ', array_map(escapeshellarg(...), $phpSources))
    ),
    $files,
    $status
);

if ($status !== 0) {
    fwrite(STDERR, "check-annotations: the tree could not be listed; this check requires a git working tree.\n");

    exit(1);
}

$offences = [];

foreach ($files as $file) {
    $path = $root.'/'.$file;

    if (! is_file($path)) {
        continue;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);

    if ($lines === false) {
        continue;
    }

    foreach ($lines as $index => $line) {
        foreach ($forbidden as $pattern) {
            if (preg_match($pattern, $line) === 1) {
                $offences[] = sprintf('%s:%d: %s', $file, $index + 1, trim($line));

                continue 2;
            }
        }
    }
}

if ($offences !== []) {
    fwrite(STDERR, sprintf("Forbidden suppression annotations found (%d):\n\n", count($offences)));

    foreach ($offences as $offence) {
        fwrite(STDERR, '  '.$offence."\n");
    }

    fwrite(STDERR, "\nThese annotations exempt code from coverage or from mutation testing.\n");
    fwrite(STDERR, "This repository has no suppression mechanism: remove the annotation and fix what it hides.\n");

    exit(1);
}

fwrite(STDOUT, "No forbidden suppression annotations.\n");

exit(0);
