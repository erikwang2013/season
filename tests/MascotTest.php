<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Tests;

use Erikwang2013\Season\Mascot;
use PHPUnit\Framework\TestCase;

class MascotTest extends TestCase
{
    private const WINTER_ACCENT = '#3C8FD1';
    private const SUMMER_ACCENT = '#E8A11C';

    public function testSvgIsWellFormedXml(): void
    {
        $svg = Mascot::svg();

        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith("</svg>\n", $svg);

        $doc = new \DOMDocument();
        $this->assertTrue($doc->loadXML($svg), 'Mascot SVG must be parseable XML');
        $this->assertSame(1, $doc->getElementsByTagName('svg')->length);
    }

    public function testSvgCarriesTheNeutralCaption(): void
    {
        $svg = Mascot::svg();

        $this->assertStringContainsString(Mascot::NAME, $svg);
        $this->assertStringContainsString('#5FBF6A', $svg, 'spring sprout color is the seasonal recolor anchor');
    }

    public function testForCountryThemesBySeason(): void
    {
        $svg = Mascot::forCountry('CN', new \DateTimeImmutable('2026-01-15'));

        $this->assertStringContainsString('🇨🇳 冬 · Winter', $svg);
        $this->assertStringContainsString(self::WINTER_ACCENT, $svg);
        $this->assertStringNotContainsString(Mascot::NAME, $svg, 'neutral caption must be replaced');
        $this->assertStringNotContainsString('#5FBF6A', $svg, 'every palette token occurrence must be replaced');
    }

    public function testForCountryFlipsSeasonInSouthernHemisphere(): void
    {
        $summer = Mascot::forCountry('AU', new \DateTimeImmutable('2026-01-15'));
        $winter = Mascot::forCountry('AU', new \DateTimeImmutable('2026-07-15'));

        $this->assertStringContainsString('🇦🇺 夏 · Summer', $summer);
        $this->assertStringContainsString(self::SUMMER_ACCENT, $summer);
        $this->assertStringContainsString('🇦🇺 冬 · Winter', $winter);
        $this->assertStringContainsString(self::WINTER_ACCENT, $winter);
    }

    public function testForCountryAcceptsLowercaseCode(): void
    {
        $this->assertStringContainsString(
            '🇦🇺 夏 · Summer',
            Mascot::forCountry('au', new \DateTimeImmutable('2026-01-15'))
        );
    }

    public function testForCountryRejectsInvalidCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Mascot::forCountry('XXX');
    }

    public function testHelperReturnsNeutralSvgWithoutCode(): void
    {
        $this->assertSame(Mascot::svg(), country_season_mascot());
        $this->assertSame(
            Mascot::forCountry('JP', new \DateTimeImmutable('2026-04-01')),
            country_season_mascot('JP', new \DateTimeImmutable('2026-04-01'))
        );
    }
}
