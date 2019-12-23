<?php

use think\facade\Env;

return [
    // 驱动类型，可选择 sync(默认):同步执行 database:数据库驱动 redis:Redis驱动
    'default' => Env::get('queue.driver', 'redis'),
    'connections' => [
        'redis' => [
            'type' => 'redis',
            'persistent' => 'connect',
            'host' => ''
        ],
        'amqp' => [
            'type' => 'amqp',
            'hostname' => ''
        ]
    ]
];