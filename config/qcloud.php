<?php
return [
    'app_id' => env('qcloud.app_id'),
    'secret_id' => env('qcloud.secret_id'),
    'secret_key' => env('qcloud.secret_key'),
    'cos' => [
        'bucket' => env('qcloud.cos_bucket'),
        'region' => env('qcloud.cos_region')
    ],
    'api' => [
        'url' => env('qcloud.api'),
        'appkey' => env('qcloud.api_appkey'),
        'appsecret' => env('qcloud.api_appsecret')
    ]
];