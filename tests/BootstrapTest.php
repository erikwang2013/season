<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests;

use PHPUnit\Framework\TestCase;

/**
 * 原生 PHP（不使用 Composer）用法：在干净进程里只 require src/bootstrap.php。
 */
class BootstrapTest extends TestCase
{
    /** 子进程输出的分隔标记：屏蔽本机 php.ini / xdebug 等启动期噪声 */
    private const MARKER = '<<<season-bootstrap-test>>>';

    private function bootstrapPath(): string
    {
        return \dirname(__DIR__) . '/src/bootstrap.php';
    }

    private function runInFreshProcess(string $code): string
    {
        $command = \escapeshellarg(\PHP_BINARY)
            . ' -d error_reporting=E_ALL -d display_errors=1 -d xdebug.mode=off'
            . ' -r ' . \escapeshellarg('echo "' . self::MARKER . '";' . $code)
            . ' 2>&1';

        $output = (string) \shell_exec($command);
        $start = \strpos($output, self::MARKER);
        $this->assertNotFalse($start, '子进程未跑完，原始输出：' . $output);

        return \trim((string) \substr($output, $start + \strlen(self::MARKER)));
    }

    private function requireBootstrap(): string
    {
        return 'require ' . \var_export($this->bootstrapPath(), true) . ';';
    }

    public function testBootstrapExposesHelpersClassesAndMascot(): void
    {
        $output = $this->runInFreshProcess($this->requireBootstrap() . '
            echo \country_season("CN", new \DateTimeImmutable("2026-07-15")), "|",
                 \country_season("AU", new \DateTimeImmutable("2026-07-15")), "|",
                 \country_season_zh("CN", new \DateTimeImmutable("2026-01-15")), "|",
                 \country_season_flag("AU"), "|",
                 (\strpos(\Erikwang2013\Season\Mascot::svg(), "Seasony") !== false ? "mascot" : "no-mascot"), "|",
                 (new \Erikwang2013\Season\SeasonService("JP"))->getSeasonForDefault(new \DateTimeImmutable("2026-04-01")), "|",
                 \Erikwang2013\Season\Hyperf\ConfigProvider::class;'
        );

        $this->assertSame(
            'summer|winter|冬|🇦🇺|mascot|spring|Erikwang2013\Season\Hyperf\ConfigProvider',
            $output
        );
    }

    public function testBootstrapCanBeRequiredTwice(): void
    {
        $output = $this->runInFreshProcess(
            $this->requireBootstrap()
            . $this->requireBootstrap()
            . 'echo \country_season("JP", new \DateTimeImmutable("2026-04-01"));'
        );

        $this->assertSame('spring', $output, 'repeated require must not warn or redeclare');
    }

    public function testVersionGuardRunsBeforeTheAutoloader(): void
    {
        $bootstrap = (string) \file_get_contents($this->bootstrapPath());
        $guard = \strpos($bootstrap, 'PHP_VERSION_ID < 80000');
        $autoload = \strpos($bootstrap, 'spl_autoload_register');

        $this->assertIsInt($guard);
        $this->assertIsInt($autoload);
        $this->assertLessThan($autoload, $guard, '老版本 PHP 应先得到明确报错，而不是语法错误');
    }
}
