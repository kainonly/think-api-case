<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\RoleRedis;
use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\OriginListsModel;
use think\bit\lifecycle\AddAfterHooks;
use think\bit\lifecycle\DeleteAfterHooks;

class PolicyController extends BaseController implements AddAfterHooks, DeleteAfterHooks
{
    use OriginListsModel, AddModel, DeleteModel;
    protected $model = 'policy';
    protected $origin_lists_without_field = [];
    protected $origin_lists_orders = [];
    protected $add_auto_timestamp = false;

    /**
     * @return bool
     */
    public function __addAfterHooks($pk): bool
    {
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
    }
}
