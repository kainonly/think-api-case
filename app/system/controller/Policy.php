<?php

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\OriginListsModel;
use think\bit\lifecycle\AddAfterHooks;
use think\bit\lifecycle\DeleteAfterHooks;

class Policy extends Base implements AddAfterHooks, DeleteAfterHooks
{
    use OriginListsModel, AddModel, DeleteModel;
    protected $model = 'policy';
    protected $origin_lists_without_field = [];
    protected $origin_lists_orders = [];
    protected $add_auto_timestamp = false;

    /**
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
}
