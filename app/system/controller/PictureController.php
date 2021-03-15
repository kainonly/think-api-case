<?php
declare(strict_types=1);

namespace app\system\controller;

use app\common\logic\MediaLib;

class PictureController extends BaseController
{
    use MediaLib;

    protected string $model = 'picture';
}