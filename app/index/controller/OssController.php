<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Response;
use think\support\facade\Oss;

class OssController
{
    /**
     * 中转上传
     * @return Response
     */
    public function upload(): Response
    {
        $saveName = Oss::put('file');
        return json([
            'error' => 0,
            'data' => [
                'save_name' => $saveName,
            ]
        ]);
    }

    /**
     * 获取签名web上传
     * @return Response
     */
    public function sign(): Response
    {
        return json(Oss::generatePostPresigned([
            ['content-length-range', 0, 1073741824]
        ]));
    }
}