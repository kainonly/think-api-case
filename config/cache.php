<?php

use think\facade\Env;

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => Env::get('cache.driver', 'redis'),

    // 缓存连接方式配置
    'stores' => [
        'file' => [
            // 驱动方式
            'type' => 'File',
            // 缓存保存目录
            'path' => '',
            // 缓存前缀
            'prefix' => '',
            // 缓存有效期 0表示永久缓存
            'expire' => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize' => [],
        ],
        // redis 缓存驱动
        'redis' => [
            // 驱动方式
            'type' => 'redis',
            // 服务器地址
            'host' => Env::get('redis.host', '127.0.0.1'),
            // 端口
            'port' => Env::get('redis.port', 6379),
            // 密码
            'password' => Env::get('redis.password', null),
            // 数据库号
            'select' => (int)Env::get('redis.db', 0),
            'timeout' => 2.0
        ],
        // 更多的缓存连接
    ],
];
