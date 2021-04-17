<?php
declare(strict_types=1);

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;

class SchemaController extends BaseController
{
    use ListsModel, OriginListsModel, GetModel, AddModel, EditModel, DeleteModel;

    protected string $model = 'schema';
    protected array $add_validate = [
        'name' => 'require|array',
        'table' => 'require',
        'type' => 'require',
    ];
    protected array $edit_validate = [
        'name' => 'requireIf:switch,0|array',
        'table' => 'requireIf:switch,0',
        'type' => 'requireIf:switch,0',
    ];
}