<?php
declare(strict_types=1);

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\OriginListsModel;
use think\facade\Db;

class VideoTypeController extends BaseController
{
    use OriginListsModel, AddModel, EditModel, DeleteModel;

    protected string $model = 'video_type';
    protected array $origin_lists_orders = [
        'sort' => 'asc'
    ];
    protected array $add_validate = [
        'name' => 'require'
    ];
    protected array $edit_validate = [
        'name' => 'require'
    ];

    public function sort(): array
    {
        validate([
            'data' => 'require|array',
        ])->check($this->post);

        Db::transaction(function () {
            foreach ($this->post['data'] as $value) {
                Db::name($this->model)
                    ->where('id', '=', $value['id'])
                    ->update(['sort' => $value['sort']]);
            }
        });

        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }
}