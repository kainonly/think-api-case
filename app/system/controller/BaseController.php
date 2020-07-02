<?php
declare (strict_types=1);

namespace app\system\controller;

use think\bit\CurdController;

class BaseController extends CurdController
{
    protected array $middleware = ['system.auth', 'system.rbac'];

    protected function initialize(): void
    {
        if ($this->request->isPost()) {
            $this->post = $this->request->post() ?? [];
        }
    }
}
