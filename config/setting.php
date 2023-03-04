<?php

/**
 * その他設定
 */
return [
    // 強制的にhttpsを使用するか
    'force_https' => env('FORCE_HTTPS', false),

    // MicroCMSの設定
    'microcms' => [
        'domain' => env('MICROCMS_DOMAIN', 'xxx'),
        'api-key' => env('MICROCMS_API_KEY', 'xxx'),
    ]
];
