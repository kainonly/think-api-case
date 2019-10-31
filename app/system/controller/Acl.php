<?php

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;
use think\bit\lifecycle\AddAfterHooks;
use think\bit\lifecycle\DeleteAfterHooks;
use think\bit\lifecycle\EditAfterHooks;
use think\facade\Db;

class Acl extends Base implements AddAfterHooks, EditAfterHooks, DeleteAfterHooks
{
    use OriginListsModel, ListsModel, AddModel, GetModel, EditModel, DeleteModel;
    protected $model = 'acl';

    /**
     * @param $pk
     * @return bool
     */
    public function __addAfterHooks($pk)
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
        (new \app\system\redis\Acl())->clear();
        (new \app\system\redis\Role())->clear();
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
