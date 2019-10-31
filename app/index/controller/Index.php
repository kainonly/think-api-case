<?php

namespace app\index\controller;

use app\common\BaseController;
use think\App;
use think\support\facade\Utils;

class Index extends BaseController
{
    public function index()
    {
        dump(Utils::uuid());
    }
}
