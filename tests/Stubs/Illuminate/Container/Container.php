<?php

declare(strict_types=1);

namespace Illuminate\Container;

/**
 * Minimal stand-in for Illuminate\Container\Container (dot-notation config).
 */

class ConfigRepository
{
    /** @var array<string, mixed> */
    private array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $value = $this->items;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }

    public function set(string $key, $value): void
    {
        $this->items[$key] = $value;
    }
}

class Container implements \ArrayAccess
{
    /** @var ConfigRepository */
    public $config;

    /** @var array<string, mixed> */
    public array $bindings = [];

    public bool $console = false;

    public function __construct(array $config = [])
    {
        $this->config = new ConfigRepository($config);
    }

    public function runningInConsole(): bool
    {
        return $this->console;
    }

    public function configPath(string $path = ''): string
    {
        return '/config/' . ltrim($path, '/');
    }

    public function singleton($abstract, $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * @return mixed
     */
    public function make($abstract)
    {
        $concrete = $this->bindings[$abstract] ?? null;
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }
        if (is_string($concrete) && class_exists($concrete)) {
            return new $concrete($this);
        }
        return null;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $offset === 'config';
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $offset === 'config' ? $this->config : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
