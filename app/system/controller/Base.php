<?php

namespace app\system\controller;

use think\bit\CurdController;

class Base extends CurdController
{
    protected $middleware = ['cors', 'json', 'post', 'system.auth', 'system.rbac'];

    protected function initialize()
    {
        if ($this->request->isPost()) {
            $this->post = $this->request->post();
        }
    }
}
