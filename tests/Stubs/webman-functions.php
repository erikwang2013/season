<?php

declare(strict_types=1);

/**
 * Stub of webman helper functions (workerman/webman-framework) used by
 * src/Install.php. Loaded by tests/InstallTest.php via require_once AFTER
 * that test's "missing functions throw" cases have run.
 */

final class WebmanFixture
{
    public static ?string $basePath = null;
}

if (!function_exists('base_path')) {
    function base_path(?string $path = null): string
    {
        $base = (string) WebmanFixture::$basePath;
        return $path === null ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('copy_dir')) {
    function copy_dir(string $sourceDir, string $destDir): void
    {
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $dest = $destDir . '/' . substr($item->getPathname(), strlen($sourceDir) + 1);
            if ($item->isDir()) {
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
            } else {
                copy($item->getPathname(), $dest);
            }
        }
    }
}

if (!function_exists('remove_dir')) {
    function remove_dir(string $dir): void
    {
        if (is_file($dir)) {
            unlink($dir);
            return;
        }
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir() && !$item->isLink()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($dir);
    }
}
