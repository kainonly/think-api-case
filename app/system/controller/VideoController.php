<?php
declare(strict_types=1);

namespace app\system\controller;

use Exception;
use stdClass;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;
use think\facade\Db;
use think\support\facade\Cos;
use think\support\facade\Obs;
use think\support\facade\Oss;

class VideoController extends BaseController
{
    use OriginListsModel, ListsModel, EditModel, DeleteModel;

    protected string $model = 'picture';
    private array $objects;

    public function bulkAdd(): array
    {
        validate([
            'type_id' => 'require',
            'data' => 'require|array',
        ])->check($this->post);
        $data = [];

        $now = time();
        foreach ($this->post['data'] as $value) {
            $data[] = [
                'type_id' => $this->post['type_id'],
                'name' => $value['name'],
                'url' => $value['url'],
                'create_time' => $now,
                'update_time' => $now
            ];
        }
        Db::name($this->model)->insertAll($data);
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }

    public function bulkEdit(): array
    {
        validate([
            'type_id' => 'require',
            'ids' => 'require|array',
        ])->check($this->post);

        Db::transaction(function () {
            foreach ($this->post['ids'] as $value) {
                Db::name($this->model)
                    ->where('id', '=', $value)
                    ->update(['type_id' => $this->post['type_id']]);
            }
        });
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteBeforeHooks(): bool
    {
        $this->objects = Db::name($this->model)
            ->whereIn('id', $this->post['id'])
            ->select()
            ->map(fn($v) => $v['url'])
            ->toArray();
        return true;
    }

    public function deleteAfterHook(): bool
    {
        switch (config('filesystem.object_store')) {
            case 'aliyun':
                Oss::delete($this->objects);
                break;
            case 'huaweicloud':
                Obs::delete($this->objects);
                break;
            case 'qcloud':
                Cos::delete($this->objects);
                break;
        }
        return true;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function count(): array
    {
        $total = Db::name($this->model)->count();
        $values = Db::name($this->model)
            ->group(['type_id'])
            ->field(['type_id', 'count(*) as size'])
            ->select();

        return [
            'error' => 0,
            'data' => [
                'total' => $total,
                'values' => $values
            ]
        ];
    }
}