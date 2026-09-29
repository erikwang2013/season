<?php

declare(strict_types=1);

namespace yii\base;

/**
 * Minimal stand-in for yii\base\Application (only what the bootstrap reads).
 */
class Application
{
    /** @var array<string, mixed> */
    public array $params = [];

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }
}
