<?php

namespace app\index\controller;

use app\common\BaseController;
use think\extra\contract\CipherInterface;

class Index extends BaseController
{
    public function index(CipherInterface $cipher)
    {
        $context = $cipher->encrypt(['name' => 'kain']);
        dump($context);
    }
}
