<?php
declare (strict_types=1);

namespace app\system\controller;

use think\bit\CurdController;

class BaseController extends CurdController
{
    protected $middleware = ['cors', 'json', 'post', 'system.auth', 'system.rbac'];

    protected function initialize()
    {
        if ($this->request->isPost()) {
            $this->post = $this->request->post();
        }
    }
}
