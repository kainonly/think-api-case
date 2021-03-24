<?php
declare (strict_types=1);

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;

class ProductController extends BaseController
{
    use OriginListsModel, ListsModel, AddModel, GetModel, EditModel, DeleteModel;

    protected string $model = 'product';
    protected array $add_validate = [
        'name' => 'require',
    ];
    protected array $edit_validate = [
        'name' => 'requireIf:switch,0',
    ];
}
