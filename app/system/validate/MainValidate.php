<?php

namespace app\system\validate;

use think\Validate;

class MainValidate extends Validate
{
    protected $rule = [
        'username' => 'require|length:4,20',
        'password' => 'require|length:8,18',
        'old_password' => 'length:8,18',
        'new_password' => 'requireWith:old_password|length:8,18'
    ];

    protected $scene = [
        'login' => ['username', 'password'],
        'update' => ['old_password', 'new_password']
    ];
}