<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Response;
use think\support\facade\Obs;

class ObsController
{
    /**
     * 中转上传
     * @return Response
     */
    public function upload(): Response
    {
        $saveName = Obs::put('file');
        return json([
            'error' => 0,
            'data' => [
                'save_name' => $saveName,
            ]
        ]);
    }

    /**
     * 获取签名web上传
     */
    public function sign(): Response
    {
        return json(Obs::generatePostPresigned([
            ['content-length-range', 0, 1073741824]
        ]));
    }
}