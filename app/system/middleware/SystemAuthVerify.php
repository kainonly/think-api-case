<?php
declare (strict_types=1);

namespace app\system\middleware;

use app\system\redis\AdminRedis;
use stdClass;
use think\support\middleware\AuthVerify;

class SystemAuthVerify extends AuthVerify
{
    protected $scene = 'system';

    protected function hook(stdClass $symbol): bool
    {
        $data = AdminRedis::create()->get($symbol->username);
        if (empty($data)) {
            $this->hookResult = [
                'error' => 1,
                'msg' => 'freeze'
            ];
            return false;
        }
        return true;
    }
}