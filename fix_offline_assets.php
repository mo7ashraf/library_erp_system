<?php

$directory = __DIR__ . '/resources/views/prints';

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory)
);

foreach ($files as $file) {
    if (! $file->isFile()) {
        continue;
    }

    if (! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);

    if ($content === false) {
        echo "Cannot read: {$path}\n";
        continue;
    }

    $lines = preg_split("/\R/u", $content);

    if ($lines === false) {
        echo "Cannot split: {$path}\n";
        continue;
    }

    $cleanLines = array_filter($lines, function (string $line): bool {
        return ! str_contains($line, 'fonts.googleapis.com')
            && ! str_contains($line, 'fonts.gstatic.com');
    });

    $newContent = implode(PHP_EOL, $cleanLines);

    file_put_contents($path, $newContent);

    echo "Cleaned: {$path}\n";
}