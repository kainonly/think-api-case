<?php
declare (strict_types=1);

namespace app\index\controller;

use think\extra\contract\JumpInterface;

class TestController
{
    public function index(JumpInterface $jump)
    {
        return $jump->success();
    }


}