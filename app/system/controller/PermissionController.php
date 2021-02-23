<?php
declare(strict_types=1);

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;
use think\facade\Db;

class PermissionController extends BaseController
{
    use OriginListsModel, ListsModel, GetModel, AddModel, EditModel, DeleteModel;

    protected string $model = 'permission';
    protected array $add_validate = [
        'name' => 'require|array',
        'key' => 'require',
    ];
    protected array $edit_validate = [
        'name' => 'requireIf:switch,0|array',
        'key' => 'requireIf:switch,0',
    ];

    public function addBeforeHooks(): bool
    {
        $this->before();
        return true;
    }

    public function editBeforeHooks(): bool
    {
        if (!$this->edit_switch) {
            $this->before();
        }
        return true;
    }

    private function before(): void
    {
        $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
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
                'msg' => 'require key'
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