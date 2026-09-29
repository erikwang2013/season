<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests\Yii3;

use Erikwang2013\Season\SeasonService;
use PHPUnit\Framework\TestCase;

/**
 * Yii3 集成没有类：config/params.php 与 config/di.php 由 composer.json 的
 * extra.config-plugin 声明，被 yiisoft/config 合并进 params / di 组。
 * 这里按 config-plugin 的方式载入这两个文件（config 文件里能读到 $params 变量），
 * 校验参数命名空间与容器定义。
 */
class ConfigTest extends TestCase
{
    private function configPath(string $file): string
    {
        return \dirname(__DIR__, 2) . '/config/' . $file;
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function loadConfig(string $file, array $params = []): array
    {
        $loader = static function (string $path, array $params): array {
            return require $path;
        };

        return $loader($this->configPath($file), $params);
    }

    public function testParamsNamespacesTheDefaultCountryCode(): void
    {
        $params = $this->loadConfig('params.php');

        $this->assertSame(
            \getenv('COUNTRY_SEASON_DEFAULT') ?: 'CN',
            $params['erikwang2013/season']['default_country_code']
        );
    }

    public function testDiBindsSeasonServiceUsingParams(): void
    {
        $definitions = $this->loadConfig('di.php', [
            'erikwang2013/season' => ['default_country_code' => 'AU'],
        ]);

        $this->assertSame([SeasonService::class], \array_keys($definitions));

        $service = $definitions[SeasonService::class]();
        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertSame('winter', $service->getSeasonForDefault(new \DateTimeImmutable('2026-07-15')));
    }

    public function testDiResolvesNullDefaultWhenParamsAreMissing(): void
    {
        $definitions = $this->loadConfig('di.php');

        $service = $definitions[SeasonService::class]();
        $this->assertInstanceOf(SeasonService::class, $service);
        $this->assertNull($service->getSeasonForDefault());
    }

    public function testDiIgnoresNonStringCountryCode(): void
    {
        $definitions = $this->loadConfig('di.php', [
            'erikwang2013/season' => ['default_country_code' => ['CN']],
        ]);

        $service = $definitions[SeasonService::class]();
        $this->assertNull($service->getSeasonForDefault());
    }
}
