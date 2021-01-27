<?php
declare (strict_types=1);

namespace app\index\controller;

use think\Response;
use think\support\facade\Cos;

class CosController
{
    /**
     * 中转上传
     * @return Response
     */
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

    public function delete(): Response
    {
        Cos::delete([
            '20210114/0137d901-fbdb-46a3-90d6-d23b3d27faff.jpg',
            '20210114/2b82dc90-09ec-4157-8c52-879681ff774e.jpg'
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
        return json(Cos::generatePostPresigned([
            ['content-length-range', 0, 1073741824]
        ]));
    }
}