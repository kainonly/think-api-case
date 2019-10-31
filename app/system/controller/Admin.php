<?php

namespace app\system\controller;

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

class Admin extends Base implements
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
     * @throws \Exception
     */
    public function __getCustomReturn(array $data)
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
    public function __addBeforeHooks()
    {
        $this->role = $this->post['role'];
        unset($this->post['role']);
        $this->post['password'] = Hash::create($this->post['password']);
        return true;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function __addAfterHooks($id)
    {
        $result = Db::name('admin_role')->insert([
            'admin_id' => $id,
            'role_key' => $this->role
        ]);

        $this->clearRedis();
        return $result;
    }

    /**
     * 修改前置处理
     * @return boolean
     * @throws \Exception
     */
    public function __editBeforeHooks()
    {
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
            if (!empty($this->post['role'])) {
                $this->role = $this->post['role'];
                unset($this->post['role']);
            }

            if (!empty($this->post['password'])) {
                $validate = validate($this->model);
                if (!$validate->scene('password')->check($this->post)) {
                    $this->edit_before_result = [
                        'error' => 1,
                        'msg' => 'error:password'
                    ];
                    return false;
                }
                $this->post['password'] = Hash::create($this->post['password']);
            } else {
                unset($this->post['password']);
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function __editAfterHooks()
    {
        if (!$this->edit_switch) {
            Db::name('admin_role')
                ->where('admin_id', '=', $this->post['id'])
                ->update([
                    'role_key' => $this->role
                ]);
        }

        $this->clearRedis();
        return true;
    }

    /**
     * 删除前置处理
     * @return boolean
     * @throws \Exception
     */
    public function __deleteBeforeHooks()
    {
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
        \app\system\redis\Admin::create()->clear();
    }

    /**
     * 验证用户名是否存在
     * @return array
     */
    public function validedUsername()
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
