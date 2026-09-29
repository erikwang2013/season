<?php

declare(strict_types=1);

namespace yii\base;

/**
 * Minimal stand-in for yii\base\BootstrapInterface.
 *
 * Signature matches the framework interface: no parameter type (the real one
 * documents Application) and no return type.
 */
interface BootstrapInterface
{
    /**
     * @param Application $app
     * @return void
     */
    public function bootstrap($app);
}
