<?php

namespace app\system\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'username' => 'require|length:4,20',
        'password' => 'require|length:8,18',
        'role' => 'require'
    ];

    protected $scene = [
        'add' => ['username', 'password', 'role'],
        'edit' => ['role'],
        'password' => ['password']
    ];
}