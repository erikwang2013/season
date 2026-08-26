<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests;

use Erikwang2013\Season\CountrySeason;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    // ── country_season ─────────────────────────────────────────

    public function testCountrySeasonMatchesStaticApi(): void
    {
        $this->assertSame(
            CountrySeason::getSeason('CN', new \DateTimeImmutable('2026-07-15')),
            country_season('CN', new \DateTimeImmutable('2026-07-15'))
        );
    }

    public function testCountrySeasonNorthernAndSouthernHemisphere(): void
    {
        $this->assertSame('summer', country_season('CN', new \DateTimeImmutable('2026-07-15')));
        $this->assertSame('winter', country_season('AU', new \DateTimeImmutable('2026-07-15')));
    }

    public function testCountrySeasonCaseInsensitive(): void
    {
        $this->assertSame('summer', country_season('us', new \DateTimeImmutable('2026-07-15')));
    }

    public function testCountrySeasonInvalidCodeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        country_season('XXX');
    }

    // ── country_season_zh ──────────────────────────────────────

    public function testCountrySeasonZhMatchesStaticApi(): void
    {
        $this->assertSame('夏', country_season_zh('CN', new \DateTimeImmutable('2026-07-15')));
        $this->assertSame(
            CountrySeason::getSeasonNameZh('AU', new \DateTimeImmutable('2026-07-15')),
            country_season_zh('AU', new \DateTimeImmutable('2026-07-15'))
        );
    }

    public function testCountrySeasonZhInvalidCodeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        country_season_zh('XXX');
    }

    // ── country_season_flag ────────────────────────────────────

    public function testCountrySeasonFlagMatchesStaticApi(): void
    {
        $this->assertSame('🇨🇳', country_season_flag('CN'));
        $this->assertSame('🇦🇺', country_season_flag('au'));
        $this->assertSame(
            CountrySeason::getCountryFlagEmoji('  us  '),
            country_season_flag('us')
        );
    }

    public function testCountrySeasonFlagInvalidCodeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        country_season_flag('123');
    }

    // ── country_season_locale ──────────────────────────────────

    public function testCountrySeasonLocaleExactMatch(): void
    {
        $this->assertSame('Fall', country_season_locale('US', 'en_US', new \DateTimeImmutable('2026-10-15')));
        $this->assertSame('Herbst', country_season_locale('DE', 'de', new \DateTimeImmutable('2026-10-15')));
    }

    public function testCountrySeasonLocaleFallbackToLanguage(): void
    {
        $this->assertSame('Herbst', country_season_locale('DE', 'de_AT', new \DateTimeImmutable('2026-10-15')));
    }

    public function testCountrySeasonLocaleFallbackToEnglish(): void
    {
        $this->assertSame('Autumn', country_season_locale('GB', 'zz_ZZ', new \DateTimeImmutable('2026-10-15')));
    }

    public function testCountrySeasonLocaleMatchesStaticApi(): void
    {
        $this->assertSame(
            CountrySeason::getSeasonNameLocalized('CN', 'zh_TW', new \DateTimeImmutable('2026-07-15')),
            country_season_locale('CN', 'zh_TW', new \DateTimeImmutable('2026-07-15'))
        );
    }

    public function testCountrySeasonLocaleInvalidCodeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        country_season_locale('XXX', 'en');
    }
}
