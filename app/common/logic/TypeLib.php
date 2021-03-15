<?php
declare(strict_types=1);

namespace app\common\logic;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\OriginListsModel;
use think\facade\Db;

trait TypeLib
{
    use OriginListsModel, AddModel, EditModel, DeleteModel;

    protected function apply(): void
    {
        $this->origin_lists_orders = [
            'sort' => 'asc'
        ];
        $this->add_validate = [
            'name' => 'require'
        ];
        $this->edit_validate = [
            'name' => 'require'
        ];
    }

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