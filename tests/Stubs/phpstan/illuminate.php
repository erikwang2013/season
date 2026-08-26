<?php

declare(strict_types=1);

/**
 * PHPStan-only stubs for the runtime test stubs under tests/Stubs/Illuminate/.
 * The runtime stubs implement \ArrayAccess; this PHPStan version cannot bind
 * ArrayAccess template parameters (class.notFound for TKey/TValue), so the
 * analysis-time definitions omit the interface. Method bodies are empty on
 * purpose: PHPStan reads only signatures from stub files.
 */

namespace Illuminate\Container {

    class ConfigRepository
    {
        /** @var array<string, mixed> */
        public array $items = [];

        /** @param array<string, mixed> $items */
        public function __construct(array $items = [])
        {
        }

        /** @return mixed */
        public function get(string $key, mixed $default = null)
        {
        }

        public function set(string $key, mixed $value): void
        {
        }
    }

    class Container
    {
        /** @var ConfigRepository */
        public $config;

        /** @var array<string, mixed> */
        public array $bindings = [];

        public bool $console = false;

        /** @param array<string, mixed> $config */
        public function __construct(array $config = [])
        {
        }

        public function runningInConsole(): bool
        {
        }

        public function configPath(string $path = ''): string
        {
        }

        public function singleton(mixed $abstract, mixed $concrete = null): void
        {
        }

        public function bind(mixed $abstract, mixed $concrete = null): void
        {
        }

        /** @return mixed */
        public function make(mixed $abstract)
        {
        }

        public function offsetExists(mixed $offset): bool
        {
        }

        /** @return mixed */
        public function offsetGet(mixed $offset)
        {
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
        }

        public function offsetUnset(mixed $offset): void
        {
        }
    }
}

namespace Illuminate\Support {

    class ServiceProvider
    {
        /** @var \Illuminate\Container\Container */
        public $app;

        /** @var array<int, array{path: string, key: string}> */
        public array $merged = [];

        /** @var array<int, array{paths: array<string, mixed>, groups: mixed}> */
        public array $published = [];

        public function __construct(mixed $app)
        {
        }

        public function mergeConfigFrom(string $path, string $key): void
        {
        }

        /** @param array<string, mixed> $paths */
        public function publishes(array $paths, mixed $groups = null): void
        {
        }
    }
}
