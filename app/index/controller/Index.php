<?php

namespace app\index\controller;

use app\common\BaseController;
use simplify\amqp\AMQPManager;
use think\App;
use think\support\facade\AMQP;
use think\support\facade\Redis;

class Index extends BaseController
{
    public function index()
    {
        return json([
            'version' => app()->version()
        ]);
    }
}
