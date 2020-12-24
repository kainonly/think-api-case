<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Response;
use think\support\facade\Oss;

class OssController
{
    public function sign(): Response
    {
        return json(Oss::generatePostPresigned([
            ["content-length-range", 0, 1073741824]
        ]));
    }
}