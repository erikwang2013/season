<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests\ThinkPHP;

use Erikwang2013\Season\SeasonService;
use Erikwang2013\Season\ThinkPHP\Service;
use PHPUnit\Framework\TestCase;
use think\App;

require_once __DIR__ . '/../Stubs/think/Service.php';
require_once __DIR__ . '/../Stubs/think/App.php';
require_once __DIR__ . '/../Stubs/think/Config.php';

class ServiceTest extends TestCase
{
    public function testRegisterBindsSeasonServiceWithConfig(): void
    {
        $app = new App(['country_season' => ['default_country_code' => 'AU']]);
        $service = new Service($app);
        $service->register();

        $this->assertArrayHasKey(SeasonService::class, $app->bindings);

        $resolved = $app->make(SeasonService::class);
        $this->assertInstanceOf(SeasonService::class, $resolved);
        $this->assertSame('winter', $resolved->getSeasonForDefault(new \DateTimeImmutable('2026-07-15')));
    }

    public function testRegisterResolvesDefaultNullWhenNotConfigured(): void
    {
        $app = new App();
        $service = new Service($app);
        $service->register();

        $resolved = $app->make(SeasonService::class);
        $this->assertInstanceOf(SeasonService::class, $resolved);
        $this->assertNull($resolved->getSeasonForDefault());
    }

    public function testBootDoesNotThrow(): void
    {
        $app = new App(['country_season' => ['default_country_code' => 'CN']]);
        $service = new Service($app);
        $service->register();
        $service->boot();

        $this->assertTrue(true);
    }
}
