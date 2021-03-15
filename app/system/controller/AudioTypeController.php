<?php
declare(strict_types=1);

namespace app\system\controller;

use app\common\logic\TypeLib;

class AudioTypeController extends BaseController
{
    use TypeLib;

    protected string $model = 'audio_type';
}