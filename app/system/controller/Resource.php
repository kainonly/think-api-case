<?php

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\OriginListsModel;
use think\bit\lifecycle\AddAfterHooks;
use think\bit\lifecycle\DeleteAfterHooks;
use think\bit\lifecycle\DeleteBeforeHooks;
use think\bit\lifecycle\EditAfterHooks;
use think\facade\Db;

class Resource extends Base implements AddAfterHooks, EditAfterHooks, DeleteBeforeHooks, DeleteAfterHooks
{
    use OriginListsModel, GetModel, AddModel, DeleteModel, EditModel;

    protected $model = 'resource';
    protected $origin_lists_orders = ['sort'];

    /**
     * @param $id
     * @return bool
     */
    public function __addAfterHooks($id)
    {
        $this->clearRedis();
        return true;
    }

    /**
     * @return bool
     */
    public function __editAfterHooks()
    {
        $this->clearRedis();
        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function __deleteBeforeHooks()
    {
        $data = Db::name($this->model)
            ->whereIn('id', $this->post['id'])
            ->find();

        $result = Db::name($this->model)
            ->where('parent', '=', $data['key'])
            ->count();

        if (!empty($result)) {
            $this->delete_before_result = [
                'error' => 1,
                'msg' => 'error:has_child'
            ];
        }

        return empty($result);
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
     * 排序接口
     * @return array
     */
    public function sort()
    {
        if (empty($this->post['data'])) {
            return [
                'error' => 1,
                'msg' => 'error'
            ];
        }

        return Db::transaction(function () {
            foreach ($this->post['data'] as $value) {
                Db::name($this->model)->update($value);
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
    private function clearRedis()
    {
        \app\system\redis\Resource::create()->clear();
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

        $result = Db::name($this->model)
            ->where('key', '=', $this->post['key'])
            ->count();

        return [
            'error' => 0,
            'data' => !empty($result)
        ];
    }
}
