<?php

namespace app\index\controller;

use app\common\BaseController;
use simplify\amqp\AMQPManager;
use think\App;
use think\support\facade\AMQP;

class Index extends BaseController
{
    public function index()
    {
        AMQP::channel(function (AMQPManager $manager) {
            $manager->exchange('abc')->setDeclare('direct');
        });
//        return json([
//            'version' => app()->version()
//        ]);
    }
}
