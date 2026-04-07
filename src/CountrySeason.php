<?php

declare(strict_types=1);

namespace CountrySeason;

use DateTimeInterface;

/**
 * 根据 ISO 3166-1 alpha-2 国家简码获取当前季节
 * 国家代码由调用方动态传入，须为两字母 ISO 3166-1 alpha-2 格式（如 CN、US）。
 * 北半球：春 3-5，夏 6-8，秋 9-11，冬 12/1/2
 * 南半球：秋 3-5，冬 6-8，春 9-11，夏 12/1/2
 */
class CountrySeason
{
    public const SEASON_SPRING = 'spring';
    public const SEASON_SUMMER = 'summer';
    public const SEASON_AUTUMN = 'autumn';
    public const SEASON_WINTER = 'winter';

    public const HEMISPHERE_NORTH = 'north';
    public const HEMISPHERE_SOUTH = 'south';

    /** 南半球国家/地区 ISO 3166-1 alpha-2 代码（键为代码，便于 O(1) 查找；其余默认为北半球） */
    private const SOUTH_HEMISPHERE_CODES = [
        'AQ' => true, 'AR' => true, 'AU' => true, 'BV' => true, 'BO' => true,
        'BW' => true, 'BR' => true, 'IO' => true, 'BI' => true, 'CL' => true,
        'CC' => true, 'CK' => true, 'FK' => true, 'FJ' => true, 'TF' => true,
        'GS' => true, 'GY' => true, 'HM' => true, 'KI' => true, 'LS' => true,
        'MG' => true, 'MW' => true, 'MU' => true, 'YT' => true, 'NR' => true,
        'NC' => true, 'NZ' => true, 'NU' => true, 'NF' => true, 'PG' => true,
        'PY' => true, 'PE' => true, 'PN' => true, 'RE' => true, 'RW' => true,
        'SH' => true, 'WS' => true, 'SC' => true, 'SB' => true, 'ZA' => true,
        'SR' => true, 'SZ' => true, 'TO' => true, 'TV' => true, 'UM' => true,
        'UY' => true, 'VU' => true, 'WF' => true, 'ZM' => true, 'ZW' => true,
        'CX' => true, 'TK' => true, 'PF' => true, 'CD' => true, 'MZ' => true,
        'NA' => true, 'TZ' => true, 'AO' => true, 'KM' => true,
    ];

    /** 北半球：月份 => 季节 */
    private const MONTH_TO_SEASON_NORTH = [
        1 => self::SEASON_WINTER, 2 => self::SEASON_WINTER,
        3 => self::SEASON_SPRING, 4 => self::SEASON_SPRING, 5 => self::SEASON_SPRING,
        6 => self::SEASON_SUMMER, 7 => self::SEASON_SUMMER, 8 => self::SEASON_SUMMER,
        9 => self::SEASON_AUTUMN, 10 => self::SEASON_AUTUMN, 11 => self::SEASON_AUTUMN,
        12 => self::SEASON_WINTER,
    ];

    /** 南半球：月份 => 季节 */
    private const MONTH_TO_SEASON_SOUTH = [
        1 => self::SEASON_SUMMER, 2 => self::SEASON_SUMMER,
        3 => self::SEASON_AUTUMN, 4 => self::SEASON_AUTUMN, 5 => self::SEASON_AUTUMN,
        6 => self::SEASON_WINTER, 7 => self::SEASON_WINTER, 8 => self::SEASON_WINTER,
        9 => self::SEASON_SPRING, 10 => self::SEASON_SPRING, 11 => self::SEASON_SPRING,
        12 => self::SEASON_SUMMER,
    ];

    /**
     * 根据国家简码与日期获取季节（英文键名）
     * 国家代码为动态传入，支持大小写，自动规范为两字母大写。
     *
     * @param string $countryCode ISO 3166-1 alpha-2 两字母国家代码（动态传入），如 CN、US
     * @param DateTimeInterface|null $date 日期，默认当前时间
     * @return string 季节：spring | summer | autumn | winter
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    public static function getSeason(string $countryCode, ?DateTimeInterface $date = null): string
    {
        $code = self::normalizeCountryCode($countryCode);
        $month = (int) ($date ?? new \DateTimeImmutable())->format('n');
        $hemisphere = self::getHemisphere($code);
        return self::monthToSeason($month, $hemisphere);
    }

    /**
     * 获取季节（中文名称）
     * 国家代码为动态传入。
     *
     * @param string $countryCode ISO 3166-1 alpha-2（动态传入）
     * @param DateTimeInterface|null $date 日期
     * @return string 春 | 夏 | 秋 | 冬
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    public static function getSeasonNameZh(string $countryCode, ?DateTimeInterface $date = null): string
    {
        $season = self::getSeason($countryCode, $date);
        return self::seasonToNameZh($season);
    }

    /**
     * 判断国家所在半球
     * 国家代码为动态传入，支持大小写。
     *
     * @param string $countryCode ISO 3166-1 alpha-2（动态传入）
     * @return string north | south
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    public static function getHemisphere(string $countryCode): string
    {
        $code = self::normalizeCountryCode($countryCode);
        return isset(self::SOUTH_HEMISPHERE_CODES[$code])
            ? self::HEMISPHERE_SOUTH
            : self::HEMISPHERE_NORTH;
    }

    /**
     * 检查是否为有效的 ISO 3166-1 alpha-2 格式（2 字母）
     */
    public static function isValidCode(string $countryCode): bool
    {
        return \strlen($countryCode) === 2 && \ctype_alpha($countryCode);
    }

    /**
     * 规范化动态传入的国家代码：去空格、转大写并校验格式
     *
     * @throws \InvalidArgumentException 当为空或非两字母字母时
     */
    private static function normalizeCountryCode(string $countryCode): string
    {
        $code = \strtoupper(\trim($countryCode));
        if ($code === '' || !self::isValidCode($code)) {
            throw new \InvalidArgumentException(
                'The country code must be in the two letter format of ISO 3166-1 alpha-2, currently passed in：' . (\strlen($countryCode) > 20 ? \substr($countryCode, 0, 20) . '...' : $countryCode)
            );
        }
        return $code;
    }

    /**
     * 根据月份和半球计算季节
     */
    private static function monthToSeason(int $month, string $hemisphere): string
    {
        $map = $hemisphere === self::HEMISPHERE_SOUTH
            ? self::MONTH_TO_SEASON_SOUTH
            : self::MONTH_TO_SEASON_NORTH;
        return $map[$month] ?? self::SEASON_SPRING;
    }

    private static function seasonToNameZh(string $season): string
    {
        return match ($season) {
            self::SEASON_SPRING => '春',
            self::SEASON_SUMMER => '夏',
            self::SEASON_AUTUMN => '秋',
            self::SEASON_WINTER => '冬',
            default => '春',
        };
    }

    /**
     * 根据国家简码返回对应的旗帜 Emoji（Unicode 区域指示符序列）
     * 国家代码为动态传入，须为有效的 ISO 3166-1 alpha-2。
     *
     * @param string $countryCode ISO 3166-1 alpha-2（动态传入）
     * @return string 如 🇨🇳、🇺🇸
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    public static function getCountryFlagEmoji(string $countryCode): string
    {
        $code = self::normalizeCountryCode($countryCode);
        $a = \ord($code[0]) - 65;
        $b = \ord($code[1]) - 65;
        if ($a < 0 || $a > 25 || $b < 0 || $b > 25) {
            throw new \InvalidArgumentException('The country code must be A–Z for flag emoji.');
        }
        $base = 0x1F1E6;
        return \mb_chr($base + $a, 'UTF-8') . \mb_chr($base + $b, 'UTF-8');
    }

    /**
     * 按语言区域（BCP 47，如 zh_CN、en_US、ja、de）返回季节本地化名称
     * 国家代码决定半球与季节；locale 仅决定显示语言。
     *
     * @param string $countryCode ISO 3166-1 alpha-2（动态传入）
     * @param string $locale      如 zh_CN、en、ja_JP、pt_BR（大小写、-/_ 均可）
     * @param DateTimeInterface|null $date 日期
     * @throws \InvalidArgumentException 当国家代码格式无效时
     */
    public static function getSeasonNameLocalized(
        string $countryCode,
        string $locale,
        ?DateTimeInterface $date = null
    ): string {
        $season = self::getSeason($countryCode, $date);
        $names = self::resolveSeasonNamesForLocale($locale);
        return $names[$season] ?? $names[self::SEASON_SPRING];
    }

    /**
     * @return list<string> 已内置的语言区域标签（小写，下划线）
     */
    public static function getSupportedLocales(): array
    {
        $keys = \array_keys(self::LOCALE_SEASON_NAMES);
        \sort($keys, \SORT_STRING);
        return $keys;
    }

    /** 季节键 => 本地化名称；键为 normalize 后的小写下划线形式 */
    private const LOCALE_SEASON_NAMES = [
        'en' => [
            self::SEASON_SPRING => 'Spring',
            self::SEASON_SUMMER => 'Summer',
            self::SEASON_AUTUMN => 'Autumn',
            self::SEASON_WINTER => 'Winter',
        ],
        'en_us' => [
            self::SEASON_SPRING => 'Spring',
            self::SEASON_SUMMER => 'Summer',
            self::SEASON_AUTUMN => 'Fall',
            self::SEASON_WINTER => 'Winter',
        ],
        'en_gb' => [
            self::SEASON_SPRING => 'Spring',
            self::SEASON_SUMMER => 'Summer',
            self::SEASON_AUTUMN => 'Autumn',
            self::SEASON_WINTER => 'Winter',
        ],
        'zh' => [
            self::SEASON_SPRING => '春',
            self::SEASON_SUMMER => '夏',
            self::SEASON_AUTUMN => '秋',
            self::SEASON_WINTER => '冬',
        ],
        'zh_cn' => [
            self::SEASON_SPRING => '春',
            self::SEASON_SUMMER => '夏',
            self::SEASON_AUTUMN => '秋',
            self::SEASON_WINTER => '冬',
        ],
        'zh_tw' => [
            self::SEASON_SPRING => '春',
            self::SEASON_SUMMER => '夏',
            self::SEASON_AUTUMN => '秋',
            self::SEASON_WINTER => '冬',
        ],
        'ja' => [
            self::SEASON_SPRING => '春',
            self::SEASON_SUMMER => '夏',
            self::SEASON_AUTUMN => '秋',
            self::SEASON_WINTER => '冬',
        ],
        'ja_jp' => [
            self::SEASON_SPRING => '春',
            self::SEASON_SUMMER => '夏',
            self::SEASON_AUTUMN => '秋',
            self::SEASON_WINTER => '冬',
        ],
        'ko' => [
            self::SEASON_SPRING => '봄',
            self::SEASON_SUMMER => '여름',
            self::SEASON_AUTUMN => '가을',
            self::SEASON_WINTER => '겨울',
        ],
        'ko_kr' => [
            self::SEASON_SPRING => '봄',
            self::SEASON_SUMMER => '여름',
            self::SEASON_AUTUMN => '가을',
            self::SEASON_WINTER => '겨울',
        ],
        'de' => [
            self::SEASON_SPRING => 'Frühling',
            self::SEASON_SUMMER => 'Sommer',
            self::SEASON_AUTUMN => 'Herbst',
            self::SEASON_WINTER => 'Winter',
        ],
        'de_de' => [
            self::SEASON_SPRING => 'Frühling',
            self::SEASON_SUMMER => 'Sommer',
            self::SEASON_AUTUMN => 'Herbst',
            self::SEASON_WINTER => 'Winter',
        ],
        'fr' => [
            self::SEASON_SPRING => 'Printemps',
            self::SEASON_SUMMER => 'Été',
            self::SEASON_AUTUMN => 'Automne',
            self::SEASON_WINTER => 'Hiver',
        ],
        'fr_fr' => [
            self::SEASON_SPRING => 'Printemps',
            self::SEASON_SUMMER => 'Été',
            self::SEASON_AUTUMN => 'Automne',
            self::SEASON_WINTER => 'Hiver',
        ],
        'es' => [
            self::SEASON_SPRING => 'Primavera',
            self::SEASON_SUMMER => 'Verano',
            self::SEASON_AUTUMN => 'Otoño',
            self::SEASON_WINTER => 'Invierno',
        ],
        'es_es' => [
            self::SEASON_SPRING => 'Primavera',
            self::SEASON_SUMMER => 'Verano',
            self::SEASON_AUTUMN => 'Otoño',
            self::SEASON_WINTER => 'Invierno',
        ],
        'it' => [
            self::SEASON_SPRING => 'Primavera',
            self::SEASON_SUMMER => 'Estate',
            self::SEASON_AUTUMN => 'Autunno',
            self::SEASON_WINTER => 'Inverno',
        ],
        'it_it' => [
            self::SEASON_SPRING => 'Primavera',
            self::SEASON_SUMMER => 'Estate',
            self::SEASON_AUTUMN => 'Autunno',
            self::SEASON_WINTER => 'Inverno',
        ],
        'pt' => [
            self::SEASON_SPRING => 'Primavera',
            self::SEASON_SUMMER => 'Verão',
            self::SEASON_AUTUMN => 'Outono',
            self::SEASON_WINTER => 'Inverno',
        ],
        'pt_br' => [
            self::SEASON_SPRING => 'Primavera',
            self::SEASON_SUMMER => 'Verão',
            self::SEASON_AUTUMN => 'Outono',
            self::SEASON_WINTER => 'Inverno',
        ],
        'pt_pt' => [
            self::SEASON_SPRING => 'Primavera',
            self::SEASON_SUMMER => 'Verão',
            self::SEASON_AUTUMN => 'Outono',
            self::SEASON_WINTER => 'Inverno',
        ],
        'ru' => [
            self::SEASON_SPRING => 'Весна',
            self::SEASON_SUMMER => 'Лето',
            self::SEASON_AUTUMN => 'Осень',
            self::SEASON_WINTER => 'Зима',
        ],
        'ru_ru' => [
            self::SEASON_SPRING => 'Весна',
            self::SEASON_SUMMER => 'Лето',
            self::SEASON_AUTUMN => 'Осень',
            self::SEASON_WINTER => 'Зима',
        ],
        'nl' => [
            self::SEASON_SPRING => 'Lente',
            self::SEASON_SUMMER => 'Zomer',
            self::SEASON_AUTUMN => 'Herfst',
            self::SEASON_WINTER => 'Winter',
        ],
        'nl_nl' => [
            self::SEASON_SPRING => 'Lente',
            self::SEASON_SUMMER => 'Zomer',
            self::SEASON_AUTUMN => 'Herfst',
            self::SEASON_WINTER => 'Winter',
        ],
        'pl' => [
            self::SEASON_SPRING => 'Wiosna',
            self::SEASON_SUMMER => 'Lato',
            self::SEASON_AUTUMN => 'Jesień',
            self::SEASON_WINTER => 'Zima',
        ],
        'pl_pl' => [
            self::SEASON_SPRING => 'Wiosna',
            self::SEASON_SUMMER => 'Lato',
            self::SEASON_AUTUMN => 'Jesień',
            self::SEASON_WINTER => 'Zima',
        ],
        'sv' => [
            self::SEASON_SPRING => 'Vår',
            self::SEASON_SUMMER => 'Sommar',
            self::SEASON_AUTUMN => 'Höst',
            self::SEASON_WINTER => 'Vinter',
        ],
        'sv_se' => [
            self::SEASON_SPRING => 'Vår',
            self::SEASON_SUMMER => 'Sommar',
            self::SEASON_AUTUMN => 'Höst',
            self::SEASON_WINTER => 'Vinter',
        ],
        'uk' => [
            self::SEASON_SPRING => 'Весна',
            self::SEASON_SUMMER => 'Літо',
            self::SEASON_AUTUMN => 'Осінь',
            self::SEASON_WINTER => 'Зима',
        ],
        'uk_ua' => [
            self::SEASON_SPRING => 'Весна',
            self::SEASON_SUMMER => 'Літо',
            self::SEASON_AUTUMN => 'Осінь',
            self::SEASON_WINTER => 'Зима',
        ],
        'ar' => [
            self::SEASON_SPRING => 'الربيع',
            self::SEASON_SUMMER => 'الصيف',
            self::SEASON_AUTUMN => 'الخريف',
            self::SEASON_WINTER => 'الشتاء',
        ],
        'hi' => [
            self::SEASON_SPRING => 'वसंत',
            self::SEASON_SUMMER => 'ग्रीष्म',
            self::SEASON_AUTUMN => 'शरद्',
            self::SEASON_WINTER => 'शीत',
        ],
        'hi_in' => [
            self::SEASON_SPRING => 'वसंत',
            self::SEASON_SUMMER => 'ग्रीष्म',
            self::SEASON_AUTUMN => 'शरद्',
            self::SEASON_WINTER => 'शीत',
        ],
        'th' => [
            self::SEASON_SPRING => 'ฤดูใบไม้ผลิ',
            self::SEASON_SUMMER => 'ฤดูร้อน',
            self::SEASON_AUTUMN => 'ฤดูใบไม้ร่วง',
            self::SEASON_WINTER => 'ฤดูหนาว',
        ],
        'th_th' => [
            self::SEASON_SPRING => 'ฤดูใบไม้ผลิ',
            self::SEASON_SUMMER => 'ฤดูร้อน',
            self::SEASON_AUTUMN => 'ฤดูใบไม้ร่วง',
            self::SEASON_WINTER => 'ฤดูหนาว',
        ],
        'vi' => [
            self::SEASON_SPRING => 'mùa xuân',
            self::SEASON_SUMMER => 'mùa hè',
            self::SEASON_AUTUMN => 'mùa thu',
            self::SEASON_WINTER => 'mùa đông',
        ],
        'vi_vn' => [
            self::SEASON_SPRING => 'mùa xuân',
            self::SEASON_SUMMER => 'mùa hè',
            self::SEASON_AUTUMN => 'mùa thu',
            self::SEASON_WINTER => 'mùa đông',
        ],
        'id' => [
            self::SEASON_SPRING => 'musim semi',
            self::SEASON_SUMMER => 'musim panas',
            self::SEASON_AUTUMN => 'musim gugur',
            self::SEASON_WINTER => 'musim dingin',
        ],
        'id_id' => [
            self::SEASON_SPRING => 'musim semi',
            self::SEASON_SUMMER => 'musim panas',
            self::SEASON_AUTUMN => 'musim gugur',
            self::SEASON_WINTER => 'musim dingin',
        ],
        'tr' => [
            self::SEASON_SPRING => 'İlkbahar',
            self::SEASON_SUMMER => 'Yaz',
            self::SEASON_AUTUMN => 'Sonbahar',
            self::SEASON_WINTER => 'Kış',
        ],
        'tr_tr' => [
            self::SEASON_SPRING => 'İlkbahar',
            self::SEASON_SUMMER => 'Yaz',
            self::SEASON_AUTUMN => 'Sonbahar',
            self::SEASON_WINTER => 'Kış',
        ],
        'cs' => [
            self::SEASON_SPRING => 'jaro',
            self::SEASON_SUMMER => 'léto',
            self::SEASON_AUTUMN => 'podzim',
            self::SEASON_WINTER => 'zima',
        ],
        'cs_cz' => [
            self::SEASON_SPRING => 'jaro',
            self::SEASON_SUMMER => 'léto',
            self::SEASON_AUTUMN => 'podzim',
            self::SEASON_WINTER => 'zima',
        ],
        'da' => [
            self::SEASON_SPRING => 'forår',
            self::SEASON_SUMMER => 'sommer',
            self::SEASON_AUTUMN => 'efterår',
            self::SEASON_WINTER => 'vinter',
        ],
        'da_dk' => [
            self::SEASON_SPRING => 'forår',
            self::SEASON_SUMMER => 'sommer',
            self::SEASON_AUTUMN => 'efterår',
            self::SEASON_WINTER => 'vinter',
        ],
        'fi' => [
            self::SEASON_SPRING => 'kevät',
            self::SEASON_SUMMER => 'kesä',
            self::SEASON_AUTUMN => 'syksy',
            self::SEASON_WINTER => 'talvi',
        ],
        'fi_fi' => [
            self::SEASON_SPRING => 'kevät',
            self::SEASON_SUMMER => 'kesä',
            self::SEASON_AUTUMN => 'syksy',
            self::SEASON_WINTER => 'talvi',
        ],
        'nb' => [
            self::SEASON_SPRING => 'vår',
            self::SEASON_SUMMER => 'sommer',
            self::SEASON_AUTUMN => 'høst',
            self::SEASON_WINTER => 'vinter',
        ],
        'nb_no' => [
            self::SEASON_SPRING => 'vår',
            self::SEASON_SUMMER => 'sommer',
            self::SEASON_AUTUMN => 'høst',
            self::SEASON_WINTER => 'vinter',
        ],
        'ro' => [
            self::SEASON_SPRING => 'primăvară',
            self::SEASON_SUMMER => 'vară',
            self::SEASON_AUTUMN => 'toamnă',
            self::SEASON_WINTER => 'iarnă',
        ],
        'ro_ro' => [
            self::SEASON_SPRING => 'primăvară',
            self::SEASON_SUMMER => 'vară',
            self::SEASON_AUTUMN => 'toamnă',
            self::SEASON_WINTER => 'iarnă',
        ],
        'el' => [
            self::SEASON_SPRING => 'άνοιξη',
            self::SEASON_SUMMER => 'καλοκαίρι',
            self::SEASON_AUTUMN => 'φθινόπωρο',
            self::SEASON_WINTER => 'χειμώνας',
        ],
        'el_gr' => [
            self::SEASON_SPRING => 'άνοιξη',
            self::SEASON_SUMMER => 'καλοκαίρι',
            self::SEASON_AUTUMN => 'φθινόπωρο',
            self::SEASON_WINTER => 'χειμώνας',
        ],
        'he' => [
            self::SEASON_SPRING => 'אביב',
            self::SEASON_SUMMER => 'קיץ',
            self::SEASON_AUTUMN => 'סתיו',
            self::SEASON_WINTER => 'חורף',
        ],
        'he_il' => [
            self::SEASON_SPRING => 'אביב',
            self::SEASON_SUMMER => 'קיץ',
            self::SEASON_AUTUMN => 'סתיו',
            self::SEASON_WINTER => 'חורף',
        ],
        'hu' => [
            self::SEASON_SPRING => 'tavasz',
            self::SEASON_SUMMER => 'nyár',
            self::SEASON_AUTUMN => 'ősz',
            self::SEASON_WINTER => 'tél',
        ],
        'hu_hu' => [
            self::SEASON_SPRING => 'tavasz',
            self::SEASON_SUMMER => 'nyár',
            self::SEASON_AUTUMN => 'ősz',
            self::SEASON_WINTER => 'tél',
        ],
    ];

    /**
     * @return array<string, string> 季节键 => 本地化名称
     */
    private static function resolveSeasonNamesForLocale(string $locale): array
    {
        $key = \strtolower(\str_replace('-', '_', \trim($locale)));
        if ($key === '') {
            return self::LOCALE_SEASON_NAMES['en'];
        }
        if (isset(self::LOCALE_SEASON_NAMES[$key])) {
            return self::LOCALE_SEASON_NAMES[$key];
        }
        $underscore = \strpos($key, '_');
        if ($underscore !== false) {
            $lang = \substr($key, 0, $underscore);
            if (isset(self::LOCALE_SEASON_NAMES[$lang])) {
                return self::LOCALE_SEASON_NAMES[$lang];
            }
        }
        return self::LOCALE_SEASON_NAMES['en'];
    }
}
