<?php

namespace app\index\controller;

use app\common\BaseController;
use app\index\job\JobTest;
use think\App;
use think\facade\Queue;

class Index extends BaseController
{
    public function index()
    {
        Queue::later(1000, JobTest::class, md5((string)'sd'));
//        return json([
//            'version' => app()->version()
//        ]);
    }
}
