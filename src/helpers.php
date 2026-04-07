<?php

declare(strict_types=1);

use CountrySeason\CountrySeason;

if (!function_exists('country_season')) {
    /**
     * 根据 ISO 3166-1 国家简码获取当前季节（英文键名）
     * 国家代码为动态传入，支持大小写。
     *
     * @param string $countryCode 两字母国家代码（动态传入），如 CN、US
     * @param \DateTimeInterface|null $date 日期，默认当前时间
     * @return string spring | summer | autumn | winter
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    function country_season(string $countryCode, ?\DateTimeInterface $date = null): string
    {
        return CountrySeason::getSeason($countryCode, $date);
    }
}

if (!function_exists('country_season_zh')) {
    /**
     * 根据国家简码获取当前季节（中文）
     * 国家代码为动态传入。
     *
     * @param string $countryCode ISO 3166-1 alpha-2（动态传入）
     * @param \DateTimeInterface|null $date 日期
     * @return string 春 | 夏 | 秋 | 冬
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    function country_season_zh(string $countryCode, ?\DateTimeInterface $date = null): string
    {
        return CountrySeason::getSeasonNameZh($countryCode, $date);
    }
}

if (!function_exists('country_season_flag')) {
    /**
     * 根据国家简码返回旗帜 Emoji（Unicode 区域指示符）
     *
     * @param string $countryCode ISO 3166-1 alpha-2
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    function country_season_flag(string $countryCode): string
    {
        return CountrySeason::getCountryFlagEmoji($countryCode);
    }
}

if (!function_exists('country_season_locale')) {
    /**
     * 按语言区域返回当前季节本地化名称
     *
     * @param string $countryCode ISO 3166-1 alpha-2
     * @param string $locale      BCP 47，如 zh_CN、en、ja_JP、de_DE
     * @param \DateTimeInterface|null $date 日期
     */
    function country_season_locale(
        string $countryCode,
        string $locale,
        ?\DateTimeInterface $date = null
    ): string {
        return CountrySeason::getSeasonNameLocalized($countryCode, $locale, $date);
    }
}
