<?php

declare(strict_types=1);

/**
 * 原生 PHP 引导文件（项目不使用 Composer 时）。
 *
 *     require '/path/to/season/src/bootstrap.php';
 *     echo country_season('CN');
 *
 * 做三件事：PHP 版本检查、注册 PSR-4 风格的 SPL 自动加载、载入全局助手函数。
 * 使用 Composer 安装时无需引入本文件（Composer 的 autoload files 已加载 helpers.php），
 * 重复引入也是安全的：自动加载器可重复注册，helpers.php 由 require_once 与 function_exists 双重保护。
 */

if (\PHP_VERSION_ID < 80000) {
    throw new \RuntimeException(
        'erikwang2013/season requires PHP >= 8.0, current version: ' . \PHP_VERSION
    );
}

\spl_autoload_register(static function (string $class): void {
    $prefix = 'Erikwang2013\\Season\\';
    if (\strpos($class, $prefix) !== 0) {
        return;
    }

    $file = __DIR__ . '/' . \str_replace('\\', '/', \substr($class, \strlen($prefix))) . '.php';
    if (\is_file($file)) {
        require $file;
    }
});

require_once __DIR__ . '/helpers.php';
