<?php

namespace app\system\controller;

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
use think\facade\Db;

class Role extends Base implements AddBeforeHooks, AddAfterHooks, EditBeforeHooks, EditAfterHooks, DeleteAfterHooks
{
    use GetModel, OriginListsModel, ListsModel, AddModel, EditModel, DeleteModel;
    protected $model = 'role';
    protected $add_model = 'role_basic';
    protected $edit_model = 'role_basic';
    protected $delete_model = 'role_basic';
    private $resource = [];

    public function __addBeforeHooks()
    {
        $this->resource = $this->post['resource'];
        unset($this->post['resource']);
        return true;
    }

    /**
     * @param $id
     */
    public function __addAfterHooks($id)
    {
        $data = [];
        foreach ($this->resource as $key => $value) {
            array_push($data, [
                'role_key' => $this->post['key'],
                'resource_key' => $value
            ]);
        }
        $result = Db::name('role_resource')->insertAll($data);
        $this->clearRedis();
        return $result;
    }

    /**
     * @return bool
     */
    public function __editBeforeHooks()
    {
        if (!$this->edit_switch) {
            $this->resource = $this->post['resource'];
            unset($this->post['resource']);
        }
        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function __editAfterHooks()
    {
        if (!$this->edit_switch) {
            $data = [];
            foreach ($this->resource as $key => $value) {
                array_push($data, [
                    'role_key' => $this->post['key'],
                    'resource_key' => $value
                ]);
            }
            $delete = Db::name('role_resource')
                ->where('role_key', '=', $this->post['key'])
                ->delete();
            if (!$delete) {
                return false;
            }
            $result = Db::name('role_resource')->insertAll($data);
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
    public function __deleteAfterHooks()
    {
        $this->clearRedis();
        return true;
    }

    /**
     * 清除缓存
     */
    private function clearRedis()
    {
        \app\system\redis\Role::create()->clear();
    }

    /**
     * 验证访问控制键是否存在
     * @return array
     */
    public function validedKey()
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
