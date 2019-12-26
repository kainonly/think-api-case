<?php

namespace app\system\validate;

use think\Validate;

class RoleValidate extends Validate
{
    protected $rule = [
        'name' => 'require',
        'key' => 'require',
        'resource' => 'require|array'
    ];

    protected $scene = [
        'add' => ['name', 'key', 'resource'],
        'edit' => ['name', 'key', 'resource'],
    ];
}