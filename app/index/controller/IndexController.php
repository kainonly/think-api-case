<?php
declare (strict_types=1);

namespace app\index\controller;

use app\common\BaseController;
use think\App;
use think\Response;

class IndexController extends BaseController
{
    public function index(): Response
    {
        return json([
            'version' => app()->version(),
        ]);
    }
}
