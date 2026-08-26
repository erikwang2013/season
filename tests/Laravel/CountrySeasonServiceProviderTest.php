<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests\Laravel;

use Erikwang2013\Season\Laravel\CountrySeasonServiceProvider;
use Erikwang2013\Season\SeasonService;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Stubs/Illuminate/Container/Container.php';
require_once __DIR__ . '/../Stubs/Illuminate/Support/ServiceProvider.php';

class CountrySeasonServiceProviderTest extends TestCase
{
    private function makeApp(array $config = []): Container
    {
        $app = new Container($config);
        $app->console = true;
        return $app;
    }

    public function testRegisterMergesConfigFrom(): void
    {
        $app = $this->makeApp();
        $provider = new CountrySeasonServiceProvider($app);
        $provider->register();

        $this->assertCount(1, $provider->merged);
        $this->assertSame('country_season', $provider->merged[0]['key']);
        $this->assertSame(
            realpath(__DIR__ . '/../../config/country_season.php'),
            realpath($provider->merged[0]['path'])
        );
    }

    public function testRegisterBindsSeasonServiceSingleton(): void
    {
        $app = $this->makeApp(['country_season' => ['default_country_code' => 'AU']]);
        $provider = new CountrySeasonServiceProvider($app);
        $provider->register();

        $this->assertArrayHasKey(SeasonService::class, $app->bindings);

        $service = $app->make(SeasonService::class);
        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertSame('winter', $service->getSeasonForDefault(new \DateTimeImmutable('2026-07-15')));
    }

    public function testRegisterResolvesDefaultNullWhenNotConfigured(): void
    {
        $app = $this->makeApp();
        $provider = new CountrySeasonServiceProvider($app);
        $provider->register();

        $service = $app->make(SeasonService::class);
        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertNull($service->getSeasonForDefault());
    }

    public function testBootPublishesWhenRunningInConsole(): void
    {
        $app = $this->makeApp();
        $provider = new CountrySeasonServiceProvider($app);
        $provider->boot();

        $this->assertCount(1, $provider->published);
        $published = $provider->published[0];
        $this->assertSame('country-season-config', $published['groups']);
        $paths = $published['paths'];
        $this->assertCount(1, $paths);
        $this->assertSame(
            realpath(__DIR__ . '/../../config/country_season.php'),
            realpath(array_key_first($paths))
        );
        $this->assertSame($app->configPath('country_season.php'), reset($paths));
    }

    public function testBootDoesNotPublishWhenNotRunningInConsole(): void
    {
        $app = $this->makeApp();
        $app->console = false;
        $provider = new CountrySeasonServiceProvider($app);
        $provider->boot();

        $this->assertSame([], $provider->published);
    }
}
