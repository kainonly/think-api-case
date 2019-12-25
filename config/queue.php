<?php

use think\facade\Env;

return [
    // 驱动类型，可选择 sync(默认):同步执行 database:数据库驱动 redis:Redis驱动
    'default' => Env::get('queue.driver', 'redis'),
    'connections' => [
        'redis' => [
            // 驱动方式
            'type' => 'redis',
            // 连接方式
            'persistent' => 'connect',
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
            // 队列名称
            'queue' => 'mytest'
        ],
    ],
    'rabbitmq' => [
        'default' => [
            // 服务器地址
            'hostname' => Env::get('rabbitmq.host', 'localhost'),
            // 端口号
            'port' => Env::get('rabbitmq.port', 5672),
            // 虚拟域
            'virualhost' => Env::get('rabbitmq.virualhost', '/'),
            // 用户名
            'username' => Env::get('rabbitmq.username', 'guest'),
            // 密码
            'password' => Env::get('rabbitmq.password', 'guest'),
        ]
    ]
];