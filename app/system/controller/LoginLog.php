<?php
declare(strict_types=1);

namespace app\system\controller;

use think\bit\common\ListsModel;

class LoginLog extends BaseController
{
    use ListsModel;

    protected string $model = 'login_log';
    protected array $middleware = [
        'system.auth', 'system.rbac'
    ];
    protected array $lists_orders = ['time' => 'desc'];
}