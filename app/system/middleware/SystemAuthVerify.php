<?php
declare (strict_types=1);

namespace app\system\middleware;

use app\system\redis\AdminRedis;
use Exception;
use stdClass;
use think\support\middleware\AuthVerify;

class SystemAuthVerify extends AuthVerify
{
    protected string $scene = 'system';

    /**
     * @param stdClass $symbol
     * @return bool
     * @throws Exception
     */
    protected function hook(stdClass $symbol): bool
    {
        $data = AdminRedis::create()->get($symbol->user);
        if (empty($data)) {
            $this->hookResult = [
                'error' => 1,
                'msg' => '当前用户被冻结或已注销'
            ];
            return false;
        }
        return true;
    }
}