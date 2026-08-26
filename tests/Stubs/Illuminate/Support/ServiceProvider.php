<?php

declare(strict_types=1);

namespace Illuminate\Support;

/**
 * Minimal stand-in for Illuminate\Support\ServiceProvider that records
 * mergeConfigFrom() and publishes() calls.
 */
class ServiceProvider
{
    public $app;

    /** @var array<int, array{path: string, key: string}> */
    public array $merged = [];

    /** @var array<int, array{paths: array, groups: mixed}> */
    public array $published = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function mergeConfigFrom(string $path, string $key): void
    {
        $this->merged[] = ['path' => $path, 'key' => $key];
    }

    public function publishes(array $paths, $groups = null): void
    {
        $this->published[] = ['paths' => $paths, 'groups' => $groups];
    }
}
