<?php

declare(strict_types=1);

namespace think;

/**
 * Minimal stand-in for think\Service.
 */
class Service
{
    /** @var App */
    public $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }
}
