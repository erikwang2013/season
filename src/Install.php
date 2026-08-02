<?php

declare(strict_types=1);

namespace Erikwang2013\Season;

/**
 * Webman 插件安装/卸载
 */
class Install
{
    public const WEBMAN_PLUGIN = true;

    /** @var array<string, string> */
    protected static array $pathRelation = [
        'config/plugin/erikwang2013/season' => 'config/plugin/erikwang2013/season',
    ];

    public static function install(): void
    {
        static::installByRelation();
    }

    public static function uninstall(): void
    {
        static::uninstallByRelation();
    }

    protected static function installByRelation(): void
    {
        if (!\function_exists('base_path') || !\function_exists('copy_dir')) {
            throw new \RuntimeException('Install requires a webman environment.');
        }
        $base = base_path();
        foreach (static::$pathRelation as $source => $dest) {
            $sourceDir = __DIR__ . '/' . $source;
            if (!\is_dir($sourceDir)) {
                continue;
            }
            $destFull = $base . '/' . $dest;
            $parent = \dirname($destFull);
            if (!\is_dir($parent)) {
                \mkdir($parent, 0755, true);
            }
            copy_dir($sourceDir, $destFull);
        }
    }

    protected static function uninstallByRelation(): void
    {
        if (!\function_exists('base_path') || !\function_exists('remove_dir')) {
            throw new \RuntimeException('Install requires a webman environment.');
        }
        $base = base_path();
        foreach (static::$pathRelation as $_source => $dest) {
            $path = $base . '/' . $dest;
            if (\is_dir($path) || \is_file($path)) {
                remove_dir($path);
            }
        }
    }
}
