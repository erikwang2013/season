<?php

declare(strict_types=1);

namespace Erikwang2013\Season;

use DateTimeInterface;

/**
 * 项目吉祥物 Seasony（季灵）：输出可内联的 SVG。
 *
 * svg() 返回 docs/mascot.svg 的内容；forCountry() 在此基础上按目标国家的
 * 当前季节替换嫩芽主色与底部标注（如「🇦🇺 夏 · Summer」），可直接嵌入页面、
 * 邮件模板或错误页，无需任何前端依赖。
 *
 * 修改 docs/mascot.svg 时请保留下面两个替换锚点：底部标注文案只能出现一次，
 * 嫩芽主色 #5FBF6A 只能用于嫩芽（tests/MascotTest 会兜住这两条约束）。
 */
final class Mascot
{
    /** 吉祥物名称，同时是 SVG 底部标注的占位文案（模板中仅出现一次） */
    public const NAME = 'Seasony · 季灵';

    /** 嫩芽主色，模板中作为季节主题色的替换锚点 */
    private const PALETTE_TOKEN = '#5FBF6A';

    /** @var array<string, string> 季节 => 主题色 */
    private const SEASON_ACCENT = [
        CountrySeason::SEASON_SPRING => '#3FA96A',
        CountrySeason::SEASON_SUMMER => '#E8A11C',
        CountrySeason::SEASON_AUTUMN => '#D8602F',
        CountrySeason::SEASON_WINTER => '#3C8FD1',
    ];

    /**
     * 原样返回吉祥物 SVG（春夏秋冬四枚徽记的中性版本）。
     */
    public static function svg(): string
    {
        return self::template();
    }

    /**
     * 返回按国家当前季节着色并标注的吉祥物 SVG。
     *
     * @param string $countryCode ISO 3166-1 alpha-2 two-letter code (case-insensitive)
     * @param DateTimeInterface|null $date Defaults to current time
     * @throws \InvalidArgumentException when the country code is invalid
     */
    public static function forCountry(string $countryCode, ?DateTimeInterface $date = null): string
    {
        $season = CountrySeason::getSeason($countryCode, $date);
        $caption = CountrySeason::getCountryFlagEmoji($countryCode)
            . ' ' . CountrySeason::getSeasonNameZh($countryCode, $date)
            . ' · ' . \ucfirst($season);

        return \str_replace(
            [self::PALETTE_TOKEN, self::NAME],
            [self::SEASON_ACCENT[$season], $caption],
            self::template()
        );
    }

    /**
     * @throws \RuntimeException when docs/mascot.svg is missing from the installed package
     */
    private static function template(): string
    {
        static $svg = null;

        if ($svg === null) {
            $path = \dirname(__DIR__) . '/docs/mascot.svg';
            $content = \is_file($path) ? \file_get_contents($path) : false;
            if ($content === false || $content === '') {
                throw new \RuntimeException('Mascot SVG is missing: ' . $path);
            }
            $svg = $content;
        }

        return $svg;
    }
}
