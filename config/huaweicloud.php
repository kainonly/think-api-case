<?php
return [
    'accessKeyId' => env('huaweicloud.id'),
    'accessKeySecret' => env('huaweicloud.secret'),
    'obs' => [
        'endpoint' => env('huaweicloud.obs_endpoint'),
        'bucket' => env('huaweicloud.obs_bucket')
    ]
];