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

class AdminController extends BaseController implements
    GetCustom, AddBeforeHooks, AddAfterHooks, EditBeforeHooks, EditAfterHooks, DeleteBeforeHooks, DeleteAfterHooks
{
    use GetModel, OriginListsModel, ListsModel, AddModel, EditModel, DeleteModel;
    protected $model = 'admin';
    protected $add_model = 'admin_basic';
    protected $edit_model = 'admin_basic';
    protected $delete_model = 'admin_basic';
    protected $get_without_field = [
        'password', 'update_time', 'create_time'
    ];
    protected $origin_lists_without_field = [
        'password', 'update_time', 'create_time'
    ];
    protected $lists_without_field = [
        'password', 'update_time', 'create_time'
    ];
    private $role;

    /**
     * 自定义单条数据返回
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function __getCustomReturn(array $data): array
    {
        $username = Context::get('auth')->user;
        $rows = Db::name('admin_basic')
            ->where('username', '=', $username)
            ->where('status', '=', 1)
            ->find();
        if ($rows['id'] == $this->post['id']) {
            $data['self'] = true;
        }
        return [
            'error' => 0,
            'data' => $data,
            'rows' => $rows
        ];
    }

    /**
     * 新增前置处理
     * @return boolean
     */
    public function __addBeforeHooks(): bool
    {
        $this->role = $this->post['role'];
        $this->post['password'] = Hash::create($this->post['password']);
        unset(
            $this->post['role'],
        );
        return true;
    }

    public function __addAfterHooks($id): bool
    {
        $result = Db::name('admin_role')->insert([
            'admin_id' => $id,
            'role_key' => $this->role
        ]);
        if (!$result) {
            $this->add_after_result = [
                'error' => 1,
                'msg' => 'role assoc wrong'
            ];
            return false;
        }
        $this->clearRedis();
        return true;
    }

    /**
     * 修改前置处理
     * @return boolean
     * @throws Exception
     */
    public function __editBeforeHooks(): bool
    {
        try {
            $username = Context::get('auth')->user;
            $rows = Db::name('admin_basic')
                ->where('username', '=', $username)
                ->where('status', '=', 1)
                ->find();
            if ($rows['id'] == $this->post['id']) {
                $this->edit_before_result = [
                    'error' => 1,
                    'msg' => 'error:self'
                ];
                return false;
            }
            if (!$this->edit_switch) {
                $this->role = $this->post['role'];
                unset(
                    $this->post['role'],
                );
                if (!empty($this->post['password'])) {
                    validate([
                        'password' => 'length:8,18',
                    ])->check($this->post);
                    $this->post['password'] = Hash::create($this->post['password']);
                } else {
                    unset($this->post['password']);
                }
            }
            return true;
        } catch (Exception $e) {
            $this->edit_before_result = [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
            return false;
        }
    }

    public function __editAfterHooks(): bool
    {
        try {
            if (!$this->edit_switch) {
                Db::name('admin_role')
                    ->where('admin_id', '=', $this->post['id'])
                    ->update([
                        'role_key' => $this->role
                    ]);
            }
            $this->clearRedis();
            return true;
        } catch (Exception $e) {
            $this->edit_after_result = [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
            return false;
        }
    }

    /**
     * 删除前置处理
     * @return boolean
     */
    public function __deleteBeforeHooks(): bool
    {
        try {
            $username = Context::get('auth')->user;
            $result = Db::name($this->delete_model)
                ->where('username', '=', $username)
                ->where('status', '=', 1)
                ->find();

            if (in_array($result['id'], $this->post['id'])) {
                $this->delete_before_result = [
                    'error' => 1,
                    'msg' => 'error:self'
                ];
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->delete_before_result = [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function __deleteAfterHooks(): bool
    {
        $this->clearRedis();
        return true;
    }

    private function clearRedis()
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
                'msg' => 'error:require_username'
            ];
        }

        $result = Db::name('admin_basic')
            ->where('username', '=', $this->post['username'])
            ->count();

        return [
            'error' => 0,
            'data' => !empty($result)
        ];
    }
}
