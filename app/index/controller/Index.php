<?php

namespace app\index\controller;

use app\common\BaseController;
use think\App;
use think\extra\contract\HashInterface;
use think\extra\service\HashService;

class Index extends BaseController
{
    public function index()
    {
        $app = new App();
        $app->config->set([
            'driver' => 'argon2id',
            'bcrypt' => [
                'rounds' => env('BCRYPT_ROUNDS', 10),
            ],
            'argon' => [
                'memory' => 1024,
                'threads' => 2,
                'time' => 2,
            ],
        ], 'hashing');
        dump($app->config);
        $app->register(HashService::class);
        $hash = $app->get(HashInterface::class);
        $data = $hash->create('cxc');
        dump($data);
    }
}
