<?php
declare(strict_types=1);

namespace app\system\controller;

use app\common\logic\MediaLib;

class VideoController extends BaseController
{
    use MediaLib;

    protected string $model = 'video';
}