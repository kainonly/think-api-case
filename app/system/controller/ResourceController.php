<?php
declare (strict_types=1);

namespace app\system\controller;

use Exception;
use think\facade\Db;
use app\system\redis\ResourceRedis;
use app\system\redis\RoleRedis;
use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\OriginListsModel;

class ResourceController extends BaseController
{
    use OriginListsModel, GetModel, AddModel, DeleteModel, EditModel;

    protected string $model = 'resource';
    protected array $origin_lists_orders = ['sort'];
    protected array $add_validate = [
        'key' => 'require',
        'name' => 'require|array'
    ];
    protected array $edit_validate = [
        'key' => 'requireIf:switch,0',
        'name' => 'requireIf:switch,0|array'
    ];

    /**
     * @var string
     */
    private string $key;

    public function addBeforeHooks(): bool
    {
        $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
        return true;
    }

    public function addAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function editBeforeHooks(): bool
    {
        if (!$this->edit_switch) {
            $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
            $data = Db::name($this->model)
                ->where('id', '=', $this->post['id'])
                ->find();
            $this->key = $data['key'];
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function editAfterHooks(): bool
    {
        if (!$this->edit_switch && !empty($this->key)) {
            Db::name($this->model)
                ->where('parent', '=', $this->key)
                ->update([
                    'parent' => $this->post['key']
                ]);
        }
        $this->clearRedis();
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteBeforeHooks(): bool
    {
        $data = Db::name($this->model)
            ->whereIn('id', $this->post['id'])
            ->find();

        $exists = Db::name($this->model)
            ->where('parent', '=', $data['key'])
            ->count();

        if (!empty($exists)) {
            $this->delete_before_result = [
                'error' => 1,
                'msg' => 'not exist'
            ];
        }

        return empty($exists);
    }

    /**
     * @return bool
     */
    public function deleteAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    /**
     * 排序接口
     * @return array
     */
    public function sort(): array
    {
        validate([
            'data' => 'require|array',
        ])->check($this->post);

        return Db::transaction(function () {
            foreach ($this->post['data'] as $value) {
                Db::name($this->model)
                    ->where('id', '=', $value['id'])
                    ->update([
                        'sort' => $value['sort']
                    ]);
            }
            $this->clearRedis();
            return true;
        }) ? [
            'error' => 0,
            'msg' => 'success'
        ] : [
            'error' => 1,
            'msg' => 'error'
        ];
    }

    /**
     * 清除缓存
     */
    private function clearRedis(): void
    {
        ResourceRedis::create()->clear();
        RoleRedis::create()->clear();
    }

    /**
     * 验证访问控制键是否存在
     * @return array
     */
    public function validedKey(): array
    {
        if (empty($this->post['key'])) {
            return [
                'error' => 1,
                'msg' => 'error:require_key'
            ];
        }

        $result = Db::name($this->model)
            ->where('key', '=', $this->post['key'])
            ->count();

        return [
            'error' => 0,
            'data' => [
                'exists' => !empty($result)
            ]
        ];
    }
}
