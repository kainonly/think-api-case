<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Response;
use think\support\facade\Cos;

class CosController
{
    public function upload(): Response
    {
        $saveName = Cos::put('file');
        return json([
            'error' => 0,
            'data' => [
                'save_name' => $saveName,
            ]
        ]);
    }
}