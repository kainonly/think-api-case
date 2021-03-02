<?php
// 全局中间件定义文件
return [
    think\support\middleware\Cors::class,
    think\support\middleware\JsonResponse::class,
    think\support\middleware\FilterPostRequest::class,
];
