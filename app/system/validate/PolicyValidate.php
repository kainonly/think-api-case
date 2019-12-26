<?php

namespace app\system\validate;

use think\Validate;

class PolicyValidate extends Validate
{
    protected $rule = [
        'resource_key' => 'require',
        'acl_key' => 'require',
        'policy' => 'require'
    ];

    protected $scene = [
        'add' => ['resource_key', 'acl_key', 'policy'],
    ];
}