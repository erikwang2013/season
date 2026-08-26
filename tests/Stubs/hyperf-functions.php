<?php

declare(strict_types=1);

/**
 * Stub of the Hyperf global config() helper. Loaded by
 * tests/Hyperf/ConfigProviderTest.php via require_once AFTER that test's
 * "no config() function" case has run.
 */

final class HyperfConfigFixture
{
    /** @var array<string, mixed> */
    public static array $values = [];
}

if (!function_exists('config')) {
    /**
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        $value = HyperfConfigFixture::$values[$key] ?? null;
        return $value ?? $default;
    }
}
