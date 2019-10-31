<?php

namespace app\system\controller;

use think\facade\Db;
use think\facade\Request;
use think\helper\Arr;
use think\support\facade\Context;
use think\support\facade\Hash;
use think\support\traits\Auth;

class Main extends Base
{
    use Auth;

    protected $middleware = ['cors', 'json', 'post',
        'system.auth' => [
            'except' => ['login', 'logout', 'verify']
        ],
        'system.rbac' => [
            'only' => ['uploads']
        ]
    ];

    /**
     * 登录
     * @return array
     */
    public function login()
    {
        try {
            $validate = new \app\system\validate\Main;
            if (!$validate->scene('login')->check($this->post)) {
                return [
                    'error' => 1,
                    'msg' => $validate->getError()
                ];
            }

            $raws = \app\system\redis\Admin::create()
                ->get($this->post['username']);

            if (empty($raws)) {
                return [
                    'error' => 1,
                    'msg' => 'error:status'
                ];
            }

            if (!Hash::check($this->post['password'], $raws['password'])) {
                return [
                    'error' => 1,
                    'msg' => 'error:password_incorrect'
                ];
            }

            return $this->__create('system', [
                'user' => $raws['username'],
                'role' => explode(',', $raws['role'])
            ]);

        } catch (\Exception $e) {
            return [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 登出
     * @return array
     */
    public function logout()
    {
        return $this->__destory('system');
    }

    /**
     * Token 验证
     * @return array
     */
    public function verify()
    {
        return $this->__verify('system');
    }

    /**
     * 获取资源控制数据
     * @return array
     */
    public function resource()
    {
        try {
            $router = (new \app\system\redis\Resource)->get();
            $role = [];
            foreach (Context::get('auth')->role as $hasRoleKey) {
                $resource = (new \app\system\redis\Role)->get($hasRoleKey, 'resource');
                array_push($role, ...$resource);
            }
            $routerRole = array_unique($role);
            $lists = Arr::where($router, function ($value) use ($routerRole) {
                return in_array($value['key'], $routerRole);
            });

            return [
                'error' => 0,
                'data' => array_values($lists)
            ];
        } catch (\Exception $e) {
            return [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 获取个人信息
     * @return array
     */
    public function information()
    {
        try {
            $data = Db::name('admin_basic')
                ->where('username', '=', Context::get('auth')->user)
                ->field(['email', 'phone', 'call', 'avatar'])
                ->find();

            return [
                'error' => 0,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 更新个人信息
     * @return array
     */
    public function update()
    {
        $validate = new \app\system\validate\Main;
        if (!$validate->scene('update')->check($this->post)) {
            return [
                'error' => 1,
                'msg' => $validate->getError()
            ];
        }

        try {
            $username = Context::get('auth')->user;
            $data = Db::name('admin_basic')
                ->where('username', '=', $username)
                ->find();

            if (!empty($this->post['old_password'])) {
                if (!Hash::check($this->post['old_password'], $data['password'])) {
                    return [
                        'error' => 1,
                        'msg' => 'error:password'
                    ];
                }

                $this->post['password'] = Hash::create($this->post['new_password']);
            }

            unset($this->post['old_password'], $this->post['new_password']);
            Db::name('admin_basic')
                ->where('username', '=', $username)
                ->update($this->post);

            \app\system\redis\Admin::create()->clear();
            return [
                'error' => 0,
                'msg' => 'ok'
            ];
        } catch (\Exception $e) {
            return [
                'error' => 1,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 文件上传
     */
    public function uploads()
    {
        $file = Request::file('image');
        $info = $file->move('./uploads');
        return $info ? [
            'error' => 0,
            'data' => [
                'save_name' => $file,
                'file_name' => $info->getFilename()
            ]
        ] : [
            'error' => 1
        ];
    }
}
