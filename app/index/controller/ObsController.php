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

    public function delete(): Response
    {
        Obs::delete([
            '20201225/17d2e5cb-6d54-4a83-9830-c8e52f129b4b.jpg',
            '20201225/783bcee3-5f0e-4641-8c8b-84ea717b2451.jpg'
        ]);
        return json([
            'status' => 'ok'
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