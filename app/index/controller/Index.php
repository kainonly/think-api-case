<?php

namespace app\index\controller;

use app\common\BaseController;
use think\App;
use think\Container;
use think\extra\contract\UtilsInterface;
use think\extra\service\UtilsService;

class Index extends BaseController
{
    public function index()
    {
        $app = new App();
        $app->register(UtilsService::class);
        $utils = $app->get(UtilsInterface::class);
        dump($utils->uuid());
    }
}
