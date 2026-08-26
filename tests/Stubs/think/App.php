<?php

declare(strict_types=1);

namespace think;

/**
 * Minimal stand-in for think\App.
 */
class App
{
    /** @var Config */
    public $config;

    /** @var array<string, mixed> */
    public array $bindings = [];

    public function __construct(array $config = [])
    {
        $this->config = new Config($config);
    }

    public function bind($abstract, $concrete = null): void
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
        return $concrete;
    }
}
