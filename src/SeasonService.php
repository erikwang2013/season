<?php

declare(strict_types=1);

namespace CountrySeason;

use DateTimeInterface;

/**
 * 季节服务（便于在 webman 容器中注入使用）
 * 国家代码由调用方动态传入，须为 ISO 3166-1 alpha-2 两字母格式。
 */
class SeasonService
{
    /**
     * 默认国家代码（可从 config 读取）
     */
    private ?string $defaultCountryCode = null;

    public function __construct(?string $defaultCountryCode = null)
    {
        $this->defaultCountryCode = $defaultCountryCode;
    }

    /**
     * 获取指定国家的当前季节（国家代码动态传入）
     */
    public function getSeason(string $countryCode, ?DateTimeInterface $date = null): string
    {
        return CountrySeason::getSeason($countryCode, $date);
    }

    /**
     * 获取季节中文名（国家代码动态传入）
     */
    public function getSeasonNameZh(string $countryCode, ?DateTimeInterface $date = null): string
    {
        return CountrySeason::getSeasonNameZh($countryCode, $date);
    }

    /**
     * 根据国家简码返回旗帜 Emoji（ISO 3166-1 alpha-2）
     */
    public function getCountryFlagEmoji(string $countryCode): string
    {
        return CountrySeason::getCountryFlagEmoji($countryCode);
    }

    /**
     * 按语言区域返回季节本地化名称（BCP 47，如 zh_CN、en_US、ja）
     */
    public function getSeasonNameLocalized(
        string $countryCode,
        string $locale,
        ?DateTimeInterface $date = null
    ): string {
        return CountrySeason::getSeasonNameLocalized($countryCode, $locale, $date);
    }

    /**
     * 使用默认国家代码获取季节（需在 config 中配置 default_country_code）
     */
    public function getSeasonForDefault(?DateTimeInterface $date = null): ?string
    {
        if ($this->defaultCountryCode === null || $this->defaultCountryCode === '') {
            return null;
        }
        return CountrySeason::getSeason($this->defaultCountryCode, $date);
    }

    /**
     * 获取半球（国家代码动态传入）
     */
    public function getHemisphere(string $countryCode): string
    {
        return CountrySeason::getHemisphere($countryCode);
    }

    public function setDefaultCountryCode(?string $code): void
    {
        $this->defaultCountryCode = $code ? strtoupper($code) : null;
    }
}
