<?php
declare (strict_types=1);

namespace app\system\middleware;

use think\support\middleware\AuthVerify;

class SystemAuthVerify extends AuthVerify
{
    protected $scene = 'system';
}