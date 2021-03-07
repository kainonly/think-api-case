<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\AdminRedis;
use Exception;
use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;
use think\bit\lifecycle\AddAfterHooks;
use think\bit\lifecycle\AddBeforeHooks;
use think\bit\lifecycle\DeleteAfterHooks;
use think\bit\lifecycle\DeleteBeforeHooks;
use think\bit\lifecycle\EditAfterHooks;
use think\bit\lifecycle\EditBeforeHooks;
use think\bit\lifecycle\GetCustom;
use think\facade\Db;
use think\support\facade\Context;
use think\support\facade\Hash;

class AdminController extends BaseController
{
    use GetModel, OriginListsModel, ListsModel, AddModel, EditModel, DeleteModel;

    private const without_field = [
        'password', 'update_time', 'create_time'
    ];

    protected string $model = 'admin_mix';
    protected string $add_model = 'admin';
    protected string $edit_model = 'admin';
    protected string $delete_model = 'admin';
    protected array $get_without_field = self::without_field;
    protected array $origin_lists_without_field = self::without_field;
    protected array $lists_without_field = self::without_field;
    protected array $add_validate = [
        'username' => [
            'require',
            'between:4,20'
        ],
        'password' => [
            'require',
            'between:12,20',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&-+])(?=.*[0-9])[\w|@$!%*?&-+]+$/'
        ],
        'role' => ['require', 'array'],
        'resource' => ['array'],
        'permission' => ['array']
    ];
    protected array $edit_validate = [
        'role' => ['requireIf:switch,0', 'array'],
        'resource' => ['array'],
        'permission' => ['array']
    ];

    private array $role = [];
    private array $resource = [];

    /**
     * 自定义单条数据返回
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function getCustomReturn(array $data): array
    {
        $username = Context::get('auth')->user;
        $rows = Db::name('admin')
            ->where('username', '=', $username)
            ->where('status', '=', 1)
            ->find();
        if ($rows['id'] === $this->post['id']) {
            $data['self'] = true;
        }
        return [
            'error' => 0,
            'data' => $data,
            'rows' => $rows
        ];
    }

    public function addBeforeHooks(): bool
    {
        $this->role = $this->post['role'];
        unset($this->post['role']);
        $this->resource = $this->post['resource'];
        unset($this->post['resource']);
        $this->post['password'] = Hash::create($this->post['password']);
        $this->post['permission'] = implode(',', $this->post['permission']);
        return true;
    }

    public function addAfterHooks($id): bool
    {
        Db::name('admin_role_rel')->insertAll(array_map(static fn($v) => [
            'admin_id' => $id,
            'role_key' => $v
        ], $this->role));
        if (!empty($this->resource)) {
            Db::name('admin_resource_rel')->insertAll(array_map(static fn($v) => [
                'admin_id' => $id,
                'resource_key' => $v
            ], $this->resource));
        }
        $this->clearRedis();
        return true;
    }

    /**
     * 修改前置处理
     * @return boolean
     * @throws Exception
     */
    public function editBeforeHooks(): bool
    {
        $username = Context::get('auth')->user;
        $data = Db::name('admin_basic')
            ->where('username', '=', $username)
            ->where('status', '=', 1)
            ->find();
        if ($data['id'] === $this->post['id']) {
            $this->edit_before_result = [
                'error' => 2,
                'msg' => 'Detected as currently logged in user'
            ];
            return false;
        }
        if (!$this->edit_switch) {
            $this->role = $this->post['role'];
            unset($this->post['role']);
            if (!empty($this->post['password'])) {
                validate([
                    'between:12,20',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&-+])(?=.*[0-9])[\w|@$!%*?&-+]+$/'
                ])->check($this->post);
                $this->post['password'] = Hash::create($this->post['password']);
            } else {
                unset($this->post['password']);
            }
            $this->post['permission'] = implode(',', $this->post['permission']);
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function editAfterHooks(): bool
    {
        if (!$this->edit_switch) {
            if (!empty($this->role)) {
                Db::name('admin_role_rel')
                    ->where('admin_id', '=', $this->post['id'])
                    ->delete();
                Db::name('admin_role_rel')->insert(array_map(static fn($v) => [
                    'admin_id' => $this->post['id'],
                    'role_key' => $v
                ], $this->role));
            }
            if (!empty($this->resource)) {
                Db::name('admin_resource_rel')
                    ->where('admin_id', '=', $this->post['id'])
                    ->delete();
                Db::name('admin_resource_rel')->insert(array_map(static fn($v) => [
                    'admin_id' => $this->post['id'],
                    'resource_key' => $v
                ], $this->resource));
            }
        }
        $this->clearRedis();
        return true;
    }

    /**
     * 删除前置处理
     * @return boolean
     * @throws Exception
     */
    public function deleteBeforeHooks(): bool
    {
        $username = Context::get('auth')->user;
        $result = Db::name($this->delete_model)
            ->where('username', '=', $username)
            ->where('status', '=', 1)
            ->find();

        if (in_array($result['id'], $this->post['id'], true)) {
            $this->delete_before_result = [
                'error' => 1,
                'msg' => 'error:self'
            ];
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    private function clearRedis(): void
    {
        AdminRedis::create()->clear();
    }

    /**
     * 验证用户是否存在
     * @return array
     */
    public function validedUsername(): array
    {
        if (empty($this->post['username'])) {
            return [
                'error' => 1,
                'msg' => 'require username'
            ];
        }

        $exists = Db::name('admin')
            ->where('username', '=', $this->post['username'])
            ->count();

        return [
            'error' => 0,
            'data' => [
                'exists' => !empty($exists)
            ]
        ];
    }
}
