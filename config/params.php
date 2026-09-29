<?php

declare(strict_types=1);

/**
 * Yii3 参数（composer.json 的 extra.config-plugin 自动合并）。
 *
 * 读取路径：erikwang2013/season.default_country_code ；应用内覆盖（config/params.php）：
 *
 *     return ['erikwang2013/season' => ['default_country_code' => 'AU']];
 */
return [
    'erikwang2013/season' => require __DIR__ . '/country_season.php',
];
