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

    protected string $model = 'policy';
    protected array $origin_lists_without_field = [];
    protected array $origin_lists_orders = [];
    protected bool $add_auto_timestamp = false;

    /**
     * @param $pk
     * @return bool
     */
    public function addAfterHooks($pk): bool
    {
        $this->clearRedis();
        return true;
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
     * 清除缓存
     */
    private function clearRedis(): void
    {
        RoleRedis::create()->clear();
    }
}
