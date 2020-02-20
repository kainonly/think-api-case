<?php
declare (strict_types=1);

namespace app\index\controller;

use app\common\BaseController;
use think\App;

class IndexController extends BaseController
{
    public function index()
    {
        return json([
            'version' => app()->version(),
        ]);
    }
}
