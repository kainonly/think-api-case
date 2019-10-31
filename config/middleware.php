<?php
// 中间件配置
return [
    // 别名或分组
    'alias' => [
        'cors' => \think\support\middleware\Cors::class,
        'json' => \think\support\middleware\JsonResponse::class,
        'post' => \think\support\middleware\FilterPostRequest::class,
        'system.auth' => \app\system\middleware\SystemAuthVerify::class,
        'system.rbac' => \app\system\middleware\SystemRbacVerify::class
    ],
    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [],
];
