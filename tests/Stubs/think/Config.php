<?php

declare(strict_types=1);

namespace think;

/**
 * Minimal stand-in for think\Config.
 */
class Config
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
        return $this->items[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->items[$key] = $value;
    }
}
