# season

**文档语言 / Language:** [English](README.md) · [简体中文](README.zh-CN.md)

<p align="center"><img src="./docs/mascot.svg" alt="season 吉祥物 Seasony（季灵）" width="200" /></p>

**Seasony · 季灵** 是本项目的吉祥物：一只左半球是春天、右半球是秋天的地球精灵 —— 正好是这个小库在做的事：同一个月份，南北半球季节相反。它也是一个 API（`Mascot::forCountry()`，见下文），能按任意国家的当前季节换色并标注。

根据 **ISO 3166-1 alpha-2** 国家简码获取当前季节的 PHP 扩展，可作为普通 Composer 库使用，并可选集成 **Laravel 7–11**、**ThinkPHP 6 / 8**、**Hyperf 2 / 3** 与 **webman** 插件。除英文季节键名（`spring` 等）与中文名称外，还提供 **国旗 Emoji**、按 **BCP 47** 语言区域返回的 **多语言季节名称**，以及半球判断、指定日期计算等。

- 北半球：春 3–5，夏 6–8，秋 9–11，冬 12/1/2  
- 南半球：秋 3–5，冬 6–8，春 9–11，夏 12/1/2  

## 项目结构

```text
season/
├── src/
│   ├── CountrySeason.php      核心：季节 / 半球 / 国旗 Emoji / 多语言名称（静态 API）
│   ├── SeasonService.php      容器友好服务：默认国家码 + 代理核心 API
│   ├── LocaleData.php         内置多语言季节名（NAMES 语言级 + OVERRIDES 区域级）
│   ├── Mascot.php             吉祥物 Seasony：输出按季节着色的 SVG
│   ├── helpers.php            6 个全局助手函数（季节 / 中文 / 国旗 / Emoji / 多语言 / 吉祥物）
│   ├── bootstrap.php          原生 PHP（无 Composer）引导：版本检查 + SPL 自动加载 + 助手函数
│   ├── Install.php            webman 插件安装 / 卸载
│   ├── Laravel/               CountrySeasonServiceProvider（自动发现、可发布配置）
│   ├── ThinkPHP/              Service（think 扩展机制自动发现）
│   ├── Hyperf/                ConfigProvider（容器绑定、可发布配置）
│   └── config/plugin/erikwang2013/season/app.php   webman 插件默认配置
├── config/country_season.php  default_country_code（Laravel / ThinkPHP / Hyperf 共用）
├── docs/                      吉祥物与架构图（mascot / architecture / function / lifecycle .svg）
├── tests/                     PHPUnit 用例（tests/Stubs 下为框架桩）
└── composer.json              PSR-4 + files 自动加载，Laravel / ThinkPHP / Hyperf 扩展声明
```

## 架构设计

<img src="./docs/architecture.svg" alt="season 架构设计" width="880" />

右侧为配置与输出，左侧五层自上而下：使用方（原生 PHP / Laravel / ThinkPHP / Hyperf / webman）→ 集成层（各框架的服务提供者与 webman 安装器）→ 服务层（`SeasonService` 持有默认国家码）→ 核心层（`CountrySeason` 与 `helpers.php`）→ 数据层（南半球代码表、月份到季节映射、`LocaleData`）。

## 功能设计

<img src="./docs/function.svg" alt="season 功能设计" width="880" />

一个入口（国家码 + 可选日期），八类能力全部为纯函数：季节解析、中文名称、国旗 Emoji、多语言名称、半球判断、代码校验、默认国家、季节主题吉祥物。

## 生命周期

<img src="./docs/lifecycle.svg" alt="season 调用生命周期" width="880" />

一次 `getSeason()` 调用的 7 步主线：输入 → 标准化 → 两字母格式校验（失败抛 `InvalidArgumentException`）→ 半球判定（O(1) 查表）→ 取月份 → 月份到季节映射 → 输出季节键，之后按需转成中文 / 多语言 / 国旗 Emoji / 吉祥物。全程纯内存查表，无 IO、无状态变更。

## 安装

```bash
composer require erikwang2013/season
```

## 原生 PHP（不使用 Composer / 不使用框架）

把 `src/` 目录放进项目任意位置（要用 `country_season_mascot()` 时再带上 `docs/mascot.svg`），引入引导文件即可 —— 不需要 Composer、不需要框架：

```php
require '/path/to/season/src/bootstrap.php';   // PHP 版本检查 + SPL 自动加载 + 助手函数

echo country_season('CN');        // spring | summer | autumn | winter
echo country_season_zh('AU');     // 春 | 夏 | 秋 | 冬
echo country_season_flag('JP');   // 🇯🇵
echo country_season_emoji('US');  // 🌸 | ☀️ | 🍁 | ❄️
echo country_season_locale('DE', 'de_DE');  // Frühling / Sommer / Herbst / Winter
echo country_season_mascot('DE'); // 按德国当前季节着色的吉祥物 SVG

// 类同样可用（PSR-4 风格自动加载）
new \Erikwang2013\Season\SeasonService('CN');
```

- `bootstrap.php` 只做三件事：PHP 版本检查（低于 8.0 直接给出明确报错而不是语法错误）、注册 SPL 自动加载（`Erikwang2013\Season\*`）、载入 `helpers.php`。
- **重复引入是安全的**：自动加载器可重复注册，`helpers.php` 由 `require_once` + `function_exists` 双重保护。
- 已用 Composer 安装的项目无需引入本文件（`autoload files` 已加载助手函数），两种方式混用也不会冲突。
- 硬性要求仍是 **PHP ≥ 8.0** 与 **mbstring**（国旗 Emoji 依赖 `mb_chr`）。

## 在 webman 中安装插件

安装完依赖后，在 webman 项目根目录执行（需已安装 webman/console）：

```bash
php webman install erikwang2013/season
```

或手动将 `vendor/erikwang2013/season/src/config/plugin/erikwang2013/season` 复制到项目的 `config/plugin/erikwang2013/season`。

## Laravel 7–11

`composer require` 后，若未在 `composer.json` 中关闭该包的 **package discovery**，会自动注册 `Erikwang2013\Season\Laravel\CountrySeasonServiceProvider`，并向容器注册 **`SeasonService`**（默认国家码来自合并后的配置）。

可选：将默认配置发布到应用，便于修改：

```bash
php artisan vendor:publish --tag=country-season-config
```

发布后得到 `config/country_season.php`，其中 `default_country_code` 对应环境变量 **`COUNTRY_SEASON_DEFAULT`**（默认 `CN`）。在控制器或服务中注入 `Erikwang2013\Season\SeasonService` 即可。

## ThinkPHP 6 / 8

安装后由 Composer 的 **think 扩展机制** 自动发现 `Erikwang2013\Season\ThinkPHP\Service`，注册 **`SeasonService`** 并合并包内 `config/country_season.php` 到配置项 **`country_season`**。在需要处通过容器解析 `Erikwang2013\Season\SeasonService` 或依赖注入使用。

## Hyperf 2 / 3

安装后由 **Hyperf ConfigProvider** 机制合并依赖：向容器绑定 **`SeasonService`**，并从配置 **`country_season.default_country_code`** 读取默认国家码。

可选：发布配置文件到项目：

```bash
php bin/hyperf.php vendor:publish erikwang2013/season
```

生成 `config/autoload/country_season.php` 后按需修改；未发布时仍使用内置默认值 **`CN`**（可通过环境变量 **`COUNTRY_SEASON_DEFAULT`** 等在自定义配置中覆盖）。

## 使用方式

### 1. 静态方法（任意 PHP 项目）

```php
use Erikwang2013\Season\CountrySeason;

// 英文季节键名：spring | summer | autumn | winter
$season = CountrySeason::getSeason('CN');        // 中国，例如 winter
$season = CountrySeason::getSeason('AU');        // 澳大利亚，南半球，例如 summer

// 中文：春 | 夏 | 秋 | 冬
$zh = CountrySeason::getSeasonNameZh('CN');

// 指定日期
$date = new \DateTimeImmutable('2026-06-15');
$season = CountrySeason::getSeason('US', $date);  // summer

// 半球
$hemisphere = CountrySeason::getHemisphere('BR'); // south
$valid = CountrySeason::isValidCode('XX');        // true/false
```

#### 国旗 Emoji（Unicode 区域指示符）

根据国家简码生成对应旗帜，用于界面展示等场景：

```php
$flag = CountrySeason::getCountryFlagEmoji('CN');  // 🇨🇳
$flag = CountrySeason::getCountryFlagEmoji('us');  // 大小写均可 → 🇺🇸
```

无效或非两字母字母的国家代码会抛出 `InvalidArgumentException`（与 `getSeason` 等一致）。

#### 季节 Emoji

同一套半球逻辑，直接给出当季 Emoji（`🌸` 春 / `☀️` 夏 / `🍁` 秋 / `❄️` 冬），适合列表、推送、状态标签：

```php
$emoji = CountrySeason::getSeasonEmoji('CN');   // 北半球当前季节
$emoji = CountrySeason::getSeasonEmoji('AU');   // 南半球：同一时刻相反的季节
$emoji = CountrySeason::getSeasonEmoji('CN', new \DateTimeImmutable('2026-07-15'));  // ☀️
```

与项目吉祥物头顶的四季徽记是同一套图形语言（花 / 太阳 / 叶 / 雪花）。

#### 多语言季节名称（BCP 47）

`getSeasonNameLocalized` 用 **国家代码** 计算当前季节（含南北半球），用 **语言区域** 决定显示文案：

```php
// 第二个参数为 locale：zh_CN、en_US、ja、de、fr_FR 等，- 与 _ 均可
$name = CountrySeason::getSeasonNameLocalized('DE', 'de_DE');   // 如 Frühling
$name = CountrySeason::getSeasonNameLocalized('US', 'en_US');   // 秋季为 Fall（美式）
$name = CountrySeason::getSeasonNameLocalized('GB', 'en_GB');   // 秋季为 Autumn（英式）

// 可与指定日期一起使用
$date = new \DateTimeImmutable('2026-03-01');
CountrySeason::getSeasonNameLocalized('AU', 'en', $date);
```

列出库内已定义的 locale 标签（小写 + 下划线）：

```php
$locales = CountrySeason::getSupportedLocales();
```

**locale 解析规则**：先匹配完整标签（如 `zh_CN`），再尝试仅语言码（如 `zh`），都不命中则回退到 **`en`**。

内置语言覆盖常见语种（英/中/日/韩、德法西意葡、俄荷波瑞乌、阿印泰越印尼土、捷丹芬挪、罗/希腊/希伯来/匈等）；完整列表以 `getSupportedLocales()` 为准。未单独列出的方言可先试语言码（如 `es`），仍不满足可在外层自行映射或扩展。

### 2. 全局助手函数（autoload 已加载）

```php
country_season('JP');       // 日本当前季节，如 spring
country_season_zh('AU');    // 澳大利亚当前季节中文，如 秋

country_season_flag('FR');  // 🇫🇷
country_season_emoji('JP'); // 🌸 | ☀️ | 🍁 | ❄️
country_season_locale('IT', 'it_IT');  // 意大利语季节名，如 Primavera
country_season_locale('KR', 'ko', $date);  // 可传日期
country_season_mascot('DE');           // 吉祥物 SVG（见第 6 节）
```

### 3. 在 Laravel / ThinkPHP / Hyperf 中使用 SeasonService

安装对应集成后，容器中的 **`SeasonService`** 已与框架配置绑定（键名 **`country_season.default_country_code`**，与包内 `config/country_season.php` 一致）。`getSeasonForDefault()` 使用上述默认国家码。

### 4. 在 webman 中使用 SeasonService（安装插件后）

webman 需自行注册一次（例如在 `config/bootstrap.php`），再按类名从容器取出：

```php
use Erikwang2013\Season\SeasonService;
use support\Container;

Container::singleton(SeasonService::class, function () {
    $code = config('plugin.erikwang2013.season.app.default_country_code', 'CN');

    return new SeasonService(\is_string($code) ? $code : 'CN');
});
```

```php
use support\Container;
use Erikwang2013\Season\SeasonService;

/** @var SeasonService $seasonService */
$seasonService = Container::get(SeasonService::class);

$seasonService->getSeason('CN');
$seasonService->getSeasonNameZh('AU');
$seasonService->getCountryFlagEmoji('JP');
$seasonService->getSeasonEmoji('JP');        // ☀️ 等
$seasonService->getSeasonNameLocalized('FR', 'fr_FR');
$seasonService->getSeasonForDefault();  // 使用配置中的 default_country_code
$seasonService->getMascot();                 // 按默认国家着色的吉祥物 SVG
$seasonService->getHemisphere('NZ');
$seasonService->isValidCode('AU');          // true
$seasonService->getSupportedLocales();       // ['ar', 'cs', 'da', ...]
```

### 5. 配置（webman）

配置文件：`config/plugin/erikwang2013/season/app.php`

```php
return [
    'enable' => true,
    'default_country_code' => 'CN',  // 或 env('COUNTRY_SEASON_DEFAULT', 'CN')
];
```

### 6. 吉祥物 Seasony（Mascot）

`Mascot` 直接读取随包分发的 `docs/mascot.svg` 并做替换，输出可内联的 SVG 字符串 —— 无 JS、无外部图片、无额外依赖：

```php
use Erikwang2013\Season\Mascot;

echo Mascot::svg();                        // 中性版：四季徽记齐全（README 顶部那张）
echo Mascot::forCountry('CN');             // 按中国当前季节换色 + 标注
echo Mascot::forCountry('AU');             // 南半球：同一时刻季节相反
echo Mascot::forCountry('AU', new \DateTimeImmutable('2026-07-15'));  // 🇦🇺 冬 · Winter

echo country_season_mascot('JP');          // 全局函数；传 null 得到中性版
```

- 季节主题色：春 `#3FA96A`、夏 `#E8A11C`、秋 `#D8602F`、冬 `#3C8FD1`（用于头顶嫩芽与底部标注）。
- 输出为纯字符串，可写进 HTML / Blade / Twig / 邮件模板；用于页面时建议自行设宽度（如 `width="200"`）。
- 无效国家码与其它方法一致：抛 `InvalidArgumentException`。
- 容器场景（Laravel / ThinkPHP / Hyperf / webman）：`SeasonService::getMascot()` 用配置里的默认国家；未配置默认国家时返回中性版。

## 国家代码说明

- 使用 **ISO 3166-1 alpha-2** 两字母代码（如 CN、US、JP、AU）。
- 南半球国家（如澳大利亚 AU、阿根廷 AR、新西兰 NZ、巴西 BR 等）已内置映射，其余按北半球处理。

## API 速览

| 方法 / 函数 | 说明 |
|-------------|------|
| `CountrySeason::getSeason` / `country_season` | 季节英文键名 |
| `CountrySeason::getSeasonNameZh` / `country_season_zh` | 中文季节名 |
| `CountrySeason::getCountryFlagEmoji` / `country_season_flag` | 国旗 Emoji |
| `CountrySeason::getSeasonEmoji` / `country_season_emoji` | 季节 Emoji（🌸☀️🍁❄️） |
| `CountrySeason::getSeasonNameLocalized` / `country_season_locale` | 按 locale 的季节名 |
| `CountrySeason::getSupportedLocales` | 内置 locale 列表 |
| `CountrySeason::getHemisphere` | north / south |
| `SeasonService::getSeasonForDefault` | 使用配置的默认国家 |
| `SeasonService::getMascot` | 按默认国家着色的吉祥物 SVG |
| `SeasonService::isValidCode` | 校验代码格式 |
| `SeasonService::getSupportedLocales` | 内置 locale 列表 |
| `Mascot::svg` / `Mascot::forCountry` / `country_season_mascot` | 吉祥物 SVG（可按季节着色） |

### 异常与校验

- 国家代码须为 **两字母 A–Z**（大小写不敏感）；否则 `getSeason`、`getCountryFlagEmoji` 等会抛出 **`InvalidArgumentException`**。
- `isValidCode()` 仅校验格式是否为两字母字母，**不校验**是否为真实 ISO 国家码。

### 扩展 CountrySeason

`resolveSeasonNamesForLocale()` 和 `seasonToNameZh()` 已改为 `protected static` — 可通过继承扩展 locale 数据，无需 fork 源码。

### `setDefaultCountryCode()` 即时校验

`SeasonService::setDefaultCountryCode()` 在设置无效代码时立刻抛出 `InvalidArgumentException`，不再延迟到后续调用才报错。

## 测试

```bash
composer test       # PHPUnit（覆盖率报告仅在 CI 生成，本地可加 -- --no-coverage）
composer analyse    # PHPStan 静态分析
```

## 要求

- PHP >= 8.0
- 扩展 **mbstring**（旗帜 Emoji 依赖 `mb_chr`）
- 无需 Composer：可直接 `require src/bootstrap.php`（见「原生 PHP」一节）
- 可选：`workerman/webman-framework`（webman 插件）、`illuminate/support`（Laravel）、`topthink/framework`（ThinkPHP）、`hyperf/framework`（Hyperf）

## 开源不易，欢迎支持

| 微信 | 支付宝 |
|:---:|:---:|
| <img src="./docs/weixinpay.png" alt="微信" width="130" height="130" /> | <img src="./docs/alipay.png" alt="支付宝" width="130" height="130" /> |

---

## License

MIT
