<?php

namespace app\system\validate;

use think\Validate;

class Acl extends Validate
{
    protected $rule = [
        'key' => 'require',
        'name' => 'require',
    ];

    protected $scene = [
        'add' => ['key', 'name'],
        'edit' => ['key', 'name'],
    ];
}