<?php
declare(strict_types=1);

namespace app\system\controller;

use think\bit\common\ListsModel;

class RequestLog extends BaseController
{
    use ListsModel;

    protected string $model = 'request_log';
    protected array $middleware = [
        'system.auth', 'system.rbac'
    ];
    protected array $lists_orders = ['time' => 'desc'];
}