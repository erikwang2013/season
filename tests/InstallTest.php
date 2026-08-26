<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests;

use Erikwang2013\Season\Install;
use PHPUnit\Framework\TestCase;

/**
 * Method order matters: the "webman functions missing" cases run first and
 * must pass BEFORE tests/Stubs/webman-functions.php is loaded by
 * testInstallCopiesPluginConfigToBasePath.
 */
class InstallTest extends TestCase
{
    private const SOURCE_DIR = __DIR__ . '/../src/config/plugin/erikwang2013/season';

    public function testInstallThrowsWithoutWebmanFunctions(): void
    {
        $this->assertFalse(function_exists('base_path'));
        $this->assertFalse(function_exists('copy_dir'));
        $this->expectException(\RuntimeException::class);
        Install::install();
    }

    public function testUninstallThrowsWithoutWebmanFunctions(): void
    {
        $this->assertFalse(function_exists('remove_dir'));
        $this->expectException(\RuntimeException::class);
        Install::uninstall();
    }

    public function testInstallCopiesPluginConfigToBasePath(): void
    {
        require_once __DIR__ . '/Stubs/webman-functions.php';
        $this->assertTrue(function_exists('base_path'));

        $base = sys_get_temp_dir() . '/season-webman-' . uniqid('', true);
        mkdir($base, 0755, true);
        \WebmanFixture::$basePath = $base;

        try {
            Install::install();

            $dest = $base . '/config/plugin/erikwang2013/season';
            $this->assertDirectoryExists($dest);
            $this->assertSame(
                $this->relativeFileList(self::SOURCE_DIR),
                $this->relativeFileList($dest)
            );
            $this->assertSame(
                file_get_contents(self::SOURCE_DIR . '/app.php'),
                file_get_contents($dest . '/app.php')
            );
        } finally {
            if (function_exists('remove_dir')) {
                remove_dir($base);
            }
            \WebmanFixture::$basePath = null;
        }
    }

    public function testUninstallRemovesPluginConfig(): void
    {
        $base = sys_get_temp_dir() . '/season-webman-' . uniqid('', true);
        mkdir($base, 0755, true);
        \WebmanFixture::$basePath = $base;

        try {
            Install::install();
            $dest = $base . '/config/plugin/erikwang2013/season';
            $this->assertDirectoryExists($dest);

            Install::uninstall();
            $this->assertDirectoryDoesNotExist($dest);
        } finally {
            remove_dir($base);
            \WebmanFixture::$basePath = null;
        }
    }

    public function testUninstallDoesNothingWhenNotInstalled(): void
    {
        $base = sys_get_temp_dir() . '/season-webman-' . uniqid('', true);
        mkdir($base, 0755, true);
        \WebmanFixture::$basePath = $base;

        try {
            Install::uninstall();
            $this->assertDirectoryDoesNotExist($base . '/config');
        } finally {
            remove_dir($base);
            \WebmanFixture::$basePath = null;
        }
    }

    /** @return array<int, string> */
    private function relativeFileList(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $files[] = substr($item->getPathname(), strlen($dir) + 1);
            }
        }
        sort($files);
        return $files;
    }
}
