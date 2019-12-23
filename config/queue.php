<?php

use think\facade\Env;

return [
    // 驱动类型，可选择 sync(默认):同步执行 database:数据库驱动 redis:Redis驱动
    'default' => Env::get('queue.driver', 'redis'),
    'connections' => [
        'redis' => [
            'type' => 'redis',
            'persistent' => 'connect',
            'host' => Env::get('redis.host', '127.0.0.1'),
            'port' => Env::get('redis.port', 6379),
            'password' => Env::get('redis.password', null),
            'select' => (int)Env::get('redis.db', 0),
            'timeout' => 60,
            'queue' => 'mytest'
        ],
    ],
    'rabbitmq' => [
        'default' => [
            'hostname' => Env::get('rabbitmq.host', 'localhost'),
            'port' => Env::get('rabbitmq.port', 5672),
            'virualhost' => Env::get('rabbitmq.virualhost', '/'),
            'username' => Env::get('rabbitmq.username', 'guest'),
            'password' => Env::get('rabbitmq.password', 'guest'),
        ]
    ]
];