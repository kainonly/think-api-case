<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\AdminRedis;
use app\system\redis\ResourceRedis;
use app\system\redis\RoleRedis;
use app\system\validate\Main;
use Exception;
use think\facade\Db;
use think\facade\Request;
use think\helper\Arr;
use think\support\facade\Context;
use think\support\facade\Hash;
use think\support\traits\Auth;

class MainController extends BaseController
{
    use Auth;

    protected array $middleware = [
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
    public function login(): array
    {
        try {
            (new Main)->scene('login')
                ->check($this->post);

            $raws = AdminRedis::create()->get($this->post['username']);

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

            return $this->create('system', [
                'user' => $raws['username'],
                'role' => explode(',', $raws['role'])
            ]);

        } catch (Exception $e) {
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
    public function logout(): array
    {
        return $this->destory('system');
    }

    /**
     * @param array $symbol
     * @return array
     * @throws Exception
     */
    protected function authHook(array $symbol): array
    {
        $data = AdminRedis::create()->get($symbol['user']);
        if (empty($data)) {
            return [
                'error' => 1,
                'msg' => 'freeze'
            ];
        }
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }

    /**
     * Token 验证
     * @return array
     */
    public function verify(): array
    {
        return $this->authVerify('system');
    }


    /**
     * 获取资源控制数据
     * @return array
     */
    public function resource(): array
    {
        try {
            $router = ResourceRedis::create()->get();
            $role = RoleRedis::create()
                ->get(Context::get('auth')->role, 'resource');
            $routerRole = array_unique($role);
            $lists = Arr::where($router, function ($value) use ($routerRole) {
                return in_array($value['key'], $routerRole, true);
            });

            return [
                'error' => 0,
                'data' => array_values($lists)
            ];
        } catch (Exception $e) {
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
    public function information(): array
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
        } catch (Exception $e) {
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
    public function update(): array
    {
        try {
            (new Main)->scene('update')
                ->check($this->post);

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

            AdminRedis::create()->clear();
            return [
                'error' => 0,
                'msg' => 'ok'
            ];
        } catch (Exception $e) {
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
