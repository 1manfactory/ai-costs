<?php

declare(strict_types=1);

$roots = [
    'src',
    'tests',
    'resources',
];

$files = [];

foreach ($roots as $root) {
    if (!is_dir($root)) {
        continue;
    }

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $files[] = $file->getPathname();
    }
}

sort($files);

if ($files === []) {
    fwrite(STDOUT, "No PHP files found to lint.\n");

    exit(0);
}

$phpBinary = escapeshellarg(PHP_BINARY);
$hasErrors = false;

foreach ($files as $file) {
    $command = sprintf('%s -l %s', $phpBinary, escapeshellarg($file));
    passthru($command, $exitCode);

    if ($exitCode !== 0) {
        $hasErrors = true;
    }
}

exit($hasErrors ? 1 : 0);
