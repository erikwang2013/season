<?php

declare(strict_types=1);

namespace yii\di;

/**
 * Minimal stand-in for yii\di\Container that records definitions and, like the
 * real container, invokes callable definitions on get().
 */
class Container
{
    /** @var array<string, mixed> */
    public array $definitions = [];

    /**
     * @param mixed $class
     * @param mixed $definition
     * @param array<int, mixed> $params
     */
    public function set($class, $definition = null, array $params = []): void
    {
        $this->definitions[$class] = $definition;
    }

    /**
     * @param mixed $class
     * @param array<int, mixed> $params
     * @param array<string, mixed> $config
     * @return mixed
     */
    public function get($class, array $params = [], array $config = [])
    {
        $definition = $this->definitions[$class] ?? null;

        if ($definition instanceof \Closure) {
            return $definition($this, $params, $config);
        }

        return $definition;
    }
}
