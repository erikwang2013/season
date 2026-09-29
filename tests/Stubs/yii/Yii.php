<?php

declare(strict_types=1);

/**
 * Minimal stand-in for the global Yii class (framework/Yii.php), which sets
 * Yii::$container = new yii\di\Container() when the framework is loaded.
 */
class Yii
{
    /** @var \yii\di\Container */
    public static $container;
}
