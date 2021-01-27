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

    public function delete(): Response
    {
        Oss::delete([
            '20201224/5607e2c0-d5e4-4f18-9ca8-e7f5055e57cf.jpg',
            '20201224/f9f09284-d2a1-488f-a1c5-6a4dc49977ec.jpg'
        ]);
        return json([
            'status' => 'ok'
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