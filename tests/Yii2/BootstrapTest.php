<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests\Yii2;

use Erikwang2013\Season\SeasonService;
use Erikwang2013\Season\Yii2\Bootstrap;
use PHPUnit\Framework\TestCase;
use yii\base\Application;
use yii\di\Container;

require_once __DIR__ . '/../Stubs/yii/Yii.php';
require_once __DIR__ . '/../Stubs/yii/base/BootstrapInterface.php';
require_once __DIR__ . '/../Stubs/yii/base/Application.php';
require_once __DIR__ . '/../Stubs/yii/di/Container.php';

class BootstrapTest extends TestCase
{
    protected function setUp(): void
    {
        \Yii::$container = new Container();
    }

    /**
     * @param array<string, mixed> $params
     */
    private function app(array $params = []): Application
    {
        return new Application($params);
    }

    public function testBindsSeasonServiceFromTheProperty(): void
    {
        $bootstrap = new Bootstrap();
        $bootstrap->defaultCountryCode = 'AU';
        $bootstrap->bootstrap($this->app());

        $service = \Yii::$container->get(SeasonService::class);

        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertSame('winter', $service->getSeasonForDefault(new \DateTimeImmutable('2026-07-15')));
    }

    public function testFallsBackToParamsWhenPropertyIsEmpty(): void
    {
        $bootstrap = new Bootstrap();
        $bootstrap->bootstrap($this->app(['country_season' => ['default_country_code' => 'CN']]));

        $service = \Yii::$container->get(SeasonService::class);

        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertSame('summer', $service->getSeasonForDefault(new \DateTimeImmutable('2026-07-15')));
    }

    public function testPropertyWinsOverParams(): void
    {
        $bootstrap = new Bootstrap();
        $bootstrap->defaultCountryCode = 'AU';
        $bootstrap->bootstrap($this->app(['country_season' => ['default_country_code' => 'CN']]));

        $service = \Yii::$container->get(SeasonService::class);

        $this->assertSame('winter', $service->getSeasonForDefault(new \DateTimeImmutable('2026-07-15')));
    }

    public function testResolvesNullDefaultWhenNothingIsConfigured(): void
    {
        $bootstrap = new Bootstrap();
        $bootstrap->bootstrap($this->app());

        $service = \Yii::$container->get(SeasonService::class);

        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertNull($service->getSeasonForDefault());
    }

    public function testRegistersTheServiceUnderItsClassName(): void
    {
        (new Bootstrap())->bootstrap($this->app(['country_season' => ['default_country_code' => 'JP']]));

        $this->assertSame([SeasonService::class], \array_keys(\Yii::$container->definitions));
    }

    public function testBootstrapCanBeRunTwice(): void
    {
        $bootstrap = new Bootstrap();
        $bootstrap->bootstrap($this->app(['country_season' => ['default_country_code' => 'AU']]));
        $bootstrap->bootstrap($this->app(['country_season' => ['default_country_code' => 'AU']]));

        $this->assertCount(1, \Yii::$container->definitions);
    }
}
