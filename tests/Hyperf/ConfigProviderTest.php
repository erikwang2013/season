<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests\Hyperf;

use Erikwang2013\Season\Hyperf\ConfigProvider;
use Erikwang2013\Season\SeasonService;
use PHPUnit\Framework\TestCase;

/**
 * Method order matters: testDependencyResolvesWithoutGlobalConfigFunction
 * runs BEFORE tests/Stubs/hyperf-functions.php is loaded by
 * testDependencyUsesGlobalConfigFunction.
 */
class ConfigProviderTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', sys_get_temp_dir());
        }
    }

    public function testInvokeReturnsExpectedStructure(): void
    {
        $config = (new ConfigProvider())();

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey(SeasonService::class, $config['dependencies']);
        $this->assertInstanceOf(\Closure::class, $config['dependencies'][SeasonService::class]);

        $this->assertArrayHasKey('publish', $config);
        $this->assertCount(1, $config['publish']);
        $publish = $config['publish'][0];
        $this->assertSame('country-season-config', $publish['id']);
        $this->assertIsString($publish['description']);
        $this->assertSame(realpath(__DIR__ . '/../../config/country_season.php'), realpath($publish['source']));
        $this->assertFileExists($publish['source']);
        $this->assertSame(sys_get_temp_dir() . '/config/autoload/country_season.php', $publish['destination']);
    }

    public function testDependencyResolvesWithoutGlobalConfigFunction(): void
    {
        $this->assertFalse(function_exists('config'));

        $closure = (new ConfigProvider())()['dependencies'][SeasonService::class];
        $service = $closure();

        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertNull($service->getSeasonForDefault());
    }

    public function testDependencyUsesGlobalConfigFunction(): void
    {
        require_once __DIR__ . '/../Stubs/hyperf-functions.php';
        $this->assertTrue(function_exists('config'));

        $closure = (new ConfigProvider())()['dependencies'][SeasonService::class];

        \HyperfConfigFixture::$values['country_season.default_country_code'] = 'AU';
        $service = $closure();
        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertSame('winter', $service->getSeasonForDefault(new \DateTimeImmutable('2026-07-15')));

        \HyperfConfigFixture::$values['country_season.default_country_code'] = null;
        $service = $closure();
        $this->assertNull($service->getSeasonForDefault());
    }
}
