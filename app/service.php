<?php
return [
    think\extra\service\ContextService::class,
    think\extra\service\HashService::class,
    think\extra\service\CipherService::class,
    think\extra\service\TokenService::class,
    think\extra\service\UtilsService::class,
    think\redis\RedisService::class,
    think\amqp\AMQPService::class,
    think\elastic\ElasticService::class,
    think\aliyun\extra\OssService::class,
    think\huaweicloud\extra\ObsService::class,
    think\qcloud\extra\CosService::class
];
