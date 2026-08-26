<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests;

use Erikwang2013\Season\CountrySeason;
use Erikwang2013\Season\LocaleData;
use PHPUnit\Framework\TestCase;

class LocaleDataTest extends TestCase
{
    private const SEASON_KEYS = [
        CountrySeason::SEASON_SPRING,
        CountrySeason::SEASON_SUMMER,
        CountrySeason::SEASON_AUTUMN,
        CountrySeason::SEASON_WINTER,
    ];

    public function testNamesEachLocaleHasExactlyFourSeasons(): void
    {
        foreach (LocaleData::NAMES as $locale => $names) {
            $this->assertSame(self::SEASON_KEYS, array_keys($names), "Season key mismatch for locale: $locale");
            foreach ($names as $season => $name) {
                $this->assertIsString($name, "Locale $locale season $season is not a string");
                $this->assertNotSame('', $name, "Locale $locale season $season is empty");
            }
        }
    }

    public function testNamesLocaleKeysAreLowercaseUnderscore(): void
    {
        foreach (LocaleData::NAMES as $locale => $names) {
            $this->assertSame($locale, strtolower($locale), "Locale key not lowercase: $locale");
            $this->assertStringNotContainsString('-', $locale, "Locale key contains a hyphen: $locale");
        }
    }

    public function testOverridesEachLocaleHasExactlyFourSeasons(): void
    {
        foreach (LocaleData::OVERRIDES as $locale => $names) {
            $this->assertSame(self::SEASON_KEYS, array_keys($names), "Season key mismatch for override: $locale");
            foreach ($names as $season => $name) {
                $this->assertIsString($name, "Override $locale season $season is not a string");
                $this->assertNotSame('', $name, "Override $locale season $season is empty");
            }
        }
    }

    public function testOverridesHaveMatchingLanguageEntry(): void
    {
        foreach (LocaleData::OVERRIDES as $locale => $names) {
            $lang = explode('_', $locale)[0];
            $this->assertArrayHasKey($lang, LocaleData::NAMES, "No language entry for override: $locale");
        }
    }

    public function testEnUsOverrideDiffersFromEnOnAutumnOnly(): void
    {
        $this->assertSame('Fall', LocaleData::OVERRIDES['en_us'][CountrySeason::SEASON_AUTUMN]);
        $this->assertSame('Autumn', LocaleData::NAMES['en'][CountrySeason::SEASON_AUTUMN]);
        $this->assertNotSame(
            LocaleData::OVERRIDES['en_us'][CountrySeason::SEASON_AUTUMN],
            LocaleData::NAMES['en'][CountrySeason::SEASON_AUTUMN]
        );
        foreach ([CountrySeason::SEASON_SPRING, CountrySeason::SEASON_SUMMER, CountrySeason::SEASON_WINTER] as $season) {
            $this->assertSame(
                LocaleData::NAMES['en'][$season],
                LocaleData::OVERRIDES['en_us'][$season],
                "en_us and en must agree on season: $season"
            );
        }
    }
}
