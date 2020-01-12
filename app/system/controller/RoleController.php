<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\AdminRedis;
use Exception;
use think\facade\Db;
use app\system\redis\RoleRedis;
use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;
use think\bit\lifecycle\AddAfterHooks;
use think\bit\lifecycle\AddBeforeHooks;
use think\bit\lifecycle\DeleteAfterHooks;
use think\bit\lifecycle\EditAfterHooks;
use think\bit\lifecycle\EditBeforeHooks;

class RoleController extends BaseController implements AddBeforeHooks, AddAfterHooks, EditBeforeHooks, EditAfterHooks, DeleteAfterHooks
{
    use GetModel, OriginListsModel, ListsModel, AddModel, EditModel, DeleteModel;
    protected $model = 'role';
    protected $add_model = 'role_basic';
    protected $edit_model = 'role_basic';
    protected $delete_model = 'role_basic';
    private $resource = [];

    /**
     * @return bool
     */
    public function __addBeforeHooks(): bool
    {
        $this->resource = $this->post['resource'];
        unset(
            $this->post['resource'],
        );
        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function __addAfterHooks($id): bool
    {
        $resourceLists = [];
        foreach ($this->resource as $key => $value) {
            array_push($resourceLists, [
                'role_key' => $this->post['key'],
                'resource_key' => $value
            ]);
        }
        $result = Db::name('role_resource')->insertAll($resourceLists);
        if (!$result) {
            return false;
        }
        $this->clearRedis();
        return true;
    }

    /**
     * @return bool
     */
    public function __editBeforeHooks(): bool
    {
        if (!$this->edit_switch) {
            $this->resource = $this->post['resource'];
            unset(
                $this->post['resource'],
            );
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function __editAfterHooks(): bool
    {
        if (!$this->edit_switch) {
            $resourceLists = [];
            foreach ($this->resource as $key => $value) {
                array_push($resourceLists, [
                    'role_key' => $this->post['key'],
                    'resource_key' => $value
                ]);
            }
            Db::name('role_resource')
                ->where('role_key', '=', $this->post['key'])
                ->delete();
            $result = Db::name('role_resource')->insertAll($resourceLists);
            if (!$result) {
                return false;
            }
        }
        $this->clearRedis();
        return true;
    }

    /**
     * @return bool
     */
    public function __deleteAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    /**
     * 清除缓存
     */
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

        $result = Db::name('role_basic')
            ->where('key', '=', $this->post['key'])
            ->count();

        return [
            'error' => 0,
            'data' => !empty($result)
        ];
    }
}
