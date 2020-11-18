<?php
return [
    'accessKeyId' => env('aliyun.id'),
    'accessKeySecret' => env('aliyun.secret'),
    'oss' => [
        'endpoint' => env('aliyun.oss_endpoint'),
        'bucket' => env('aliyun.oss_bucket')
    ]
];