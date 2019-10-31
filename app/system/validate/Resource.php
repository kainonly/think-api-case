<?php

namespace app\system\validate;

use think\Validate;

class Resource extends Validate
{
    protected $rule = [
        'key' => 'require',
        'name' => 'require',
        'data' => 'require|array'
    ];

    protected $scene = [
        'add' => ['key', 'name'],
        'edit' => ['key', 'name'],
        'sort' => ['data']
    ];
}