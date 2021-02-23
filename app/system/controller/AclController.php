<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\AclRedis;
use app\system\redis\RoleRedis;
use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;
use think\facade\Db;

class AclController extends BaseController
{
    use OriginListsModel, ListsModel, AddModel, GetModel, EditModel, DeleteModel;

    protected string $model = 'acl';
    protected array $add_validate = [
        'name' => 'require|array',
        'key' => 'require',
        'write' => 'array',
        'read' => 'array'
    ];
    protected array $edit_validate = [
        'name' => 'requireIf:switch,0|array',
        'key' => 'requireIf:switch,0',
        'write' => 'array',
        'read' => 'array'
    ];

    public function addBeforeHooks(): bool
    {
        $this->before();
        return true;
    }

    public function addAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    public function editBeforeHooks(): bool
    {
        if (!$this->edit_switch) {
            $this->before();
        }
        return true;
    }

    public function editAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    private function before(): void
    {
        $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
        $this->post['write'] = implode(',', (array)$this->post['write']);
        $this->post['read'] = implode(',', (array)$this->post['read']);
    }

    public function deleteAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    private function clearRedis(): void
    {
        AclRedis::create()->clear();
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

        $exists = Db::name($this->model)
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
