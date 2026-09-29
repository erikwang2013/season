<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Yii2;

use Erikwang2013\Season\SeasonService;
use yii\base\BootstrapInterface;

/**
 * Yii2 bootstrap：把 SeasonService 注册进 Yii::$container。
 *
 * 在应用配置（config/web.php）里注册，默认国家码写在属性上：
 *
 *     'bootstrap' => [
 *         ['class' => \Erikwang2013\Season\Yii2\Bootstrap::class, 'defaultCountryCode' => 'CN'],
 *     ],
 *
 * 属性留空时回退到 params（config/params.php 的 country_season.default_country_code）：
 *
 *     'country_season' => ['default_country_code' => 'CN'],
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @var string|null 默认国家代码（ISO 3166-1 alpha-2），优先级高于 params
     */
    public $defaultCountryCode;

    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app): void
    {
        $code = $this->defaultCountryCode;

        if (!\is_string($code) || $code === '') {
            $code = $app->params['country_season']['default_country_code'] ?? null;
        }

        $code = \is_string($code) && $code !== '' ? $code : null;

        \Yii::$container->set(SeasonService::class, static function () use ($code): SeasonService {
            return new SeasonService($code);
        });
    }
}
