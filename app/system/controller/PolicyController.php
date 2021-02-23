<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\RoleRedis;
use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\OriginListsModel;

class PolicyController extends BaseController
{
    use OriginListsModel, AddModel, DeleteModel;

    protected string $model = 'policy';
    protected array $origin_lists_without_field = [];
    protected array $origin_lists_orders = [];
    protected bool $add_auto_timestamp = false;
    protected array $add_validate = [
        'resource_key' => 'require',
        'acl_key' => 'require',
        'policy' => 'require'
    ];

    /**
     * @return bool
     */
    public function addAfterHooks(): bool
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
