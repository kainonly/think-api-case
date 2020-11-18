<?php
return [
    'accessKeyId' => env('aliyun.id'),
    'accessKeySecret' => env('aliyun.secret'),
    'oss' => [
        'endpoint' => env('aliyun.oss_endpoint'),
        'extranet' => env('aliyun.oss_extranet'),
        'bucket' => env('aliyun.oss_bucket')
    ]
];