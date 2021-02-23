<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\AdminRedis;
use think\facade\Db;
use app\system\redis\RoleRedis;
use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;

class RoleController extends BaseController
{
    use GetModel, OriginListsModel, ListsModel, AddModel, EditModel, DeleteModel;

    protected string $model = 'role_mix';
    protected string $add_model = 'role';
    protected string $edit_model = 'role';
    protected string $delete_model = 'role';
    protected array $add_validate = [
        'name' => 'require|array',
        'key' => 'require',
        'resource' => 'require|array'
    ];
    protected array $edit_validate = [
        'name' => 'requireIf:switch,false|array',
        'key' => 'requireIf:switch,false',
        'resource' => 'requireIf:switch,false|array'
    ];
    private array $resource = [];

    public function addBeforeHooks(): bool
    {
        $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
        $this->resource = $this->post['resource'];
        unset($this->post['resource']);
        $this->post['permission'] = implode(',', $this->post['permission']);
        return true;
    }

    public function addAfterHooks(): bool
    {
        $resource = [];
        foreach ($this->resource as $key => $value) {
            $resource[] = [
                'role_key' => $this->post['key'],
                'resource_key' => $value
            ];
        }
        $result = Db::name('role_resource_rel')->insertAll($resource);
        if (!$result) {
            return false;
        }
        $this->clearRedis();
        return true;
    }

    public function editBeforeHooks(): bool
    {
        if (!$this->edit_switch) {
            $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
            $this->resource = $this->post['resource'];
            unset($this->post['resource']);
            $this->post['permission'] = implode(',', $this->post['permission']);
        }
        return true;
    }

    public function editAfterHooks(): bool
    {
        if (!$this->edit_switch) {
            $resource = [];
            foreach ($this->resource as $key => $value) {
                $resource[] = [
                    'role_key' => $this->post['key'],
                    'resource_key' => $value
                ];
            }
            Db::name('role_resource_rel')
                ->where('role_key', '=', $this->post['key'])
                ->delete();
            $result = Db::name('role_resource')->insertAll($resource);
            if (!$result) {
                return false;
            }
        }
        $this->clearRedis();
        return true;
    }

    public function deleteAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    private function clearRedis(): void
    {
        RoleRedis::create()->clear();
        AdminRedis::create()->clear();
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

        $exists = Db::name('role')
            ->where('key', '=', $this->post['key'])
            ->count();

        return [
            'error' => 0,
            'data' => [
                'exists' => !empty($exists)
            ]
        ];
    }
}
