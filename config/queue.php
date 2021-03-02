<?php

use think\facade\Env;

return [
    // 驱动类型，可选择 sync(默认):同步执行 database:数据库驱动 redis:Redis驱动
    'default' => Env::get('queue.driver', 'redis'),
    'connections' => [
        'redis' => [
            // 驱动方式
            'type' => 'redis',
            // 队列名称
            'queue' => Env::get('app.name') . ':async',
            // 连接方式
            'persistent' => 'pconnect',
            // 服务器地址
            'host' => Env::get('redis.host', '127.0.0.1'),
            // 端口
            'port' => Env::get('redis.port', 6379),
            // 密码
            'password' => Env::get('redis.password', null),
            // 数据库号
            'select' => (int)Env::get('redis.db', 0),
            // 超时
            'timeout' => 60,
        ],
    ]
];