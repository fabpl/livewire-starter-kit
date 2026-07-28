<?php

declare(strict_types=1);

$forbidden = [
    '@codeCoverageIgnore',
    '@pest-mutate-ignore',
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
$scanned = 0;

foreach ($files as $file) {
    $path = $root.DIRECTORY_SEPARATOR.$file;
    if ($path === __FILE__) {
        continue;
    }
    if (! is_file($path)) {
        continue;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);

    if ($lines === false) {
        continue;
    }

    $scanned++;

    foreach ($lines as $index => $line) {
        foreach ($forbidden as $annotation) {
            if (stripos($line, $annotation) !== false) {
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

fwrite(STDOUT, json_encode([
    'tool' => 'annotations',
    'result' => 'passed',
    'files' => $scanned,
], JSON_THROW_ON_ERROR)."\n");

exit(0);
