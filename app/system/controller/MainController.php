<?php
declare (strict_types=1);

namespace app\system\controller;

use Exception;
use think\facade\Db;
use app\system\redis\AdminRedis;
use app\system\redis\ResourceRedis;
use app\system\redis\RoleRedis;
use think\redis\library\Lock;
use think\support\facade\Context;
use think\support\facade\Cos;
use think\support\facade\Hash;
use think\support\traits\Auth;

class MainController extends BaseController
{
    use Auth;

    protected array $middleware = [
        'system.auth' => [
            'except' => ['login', 'logout', 'verify']
        ],
        'system.spy' => [
            'only' => ['information', 'update']
        ],
    ];

    /**
     * 登录
     * @return array
     * @throws Exception
     */
    public function login(): array
    {
        validate([
            'username' => 'require|length:4,20',
            'password' => 'require|length:12,20',
        ])->check($this->post);
        $locker = Lock::create();
        $ip = get_client_ip();
        // TODO: GET ISP
        if (!$locker->check('ip:' . $ip)) {
            $locker->lock('ip:' . $ip);

            // Logger IP登录锁定
            return [
                'error' => 2,
                'msg' => '当前尝试登录失败次数上限，请稍后再试'
            ];
        }
        $user = $this->post['username'];
        $data = AdminRedis::create()->get($user);
        if (empty($data)) {
            $locker->inc('ip:' . $ip);

            // Logger 用户不存在或冻结
            return [
                'error' => 1,
                'msg' => '当前用户不存在或已被冻结'
            ];
        }
        $userKey = 'admin:';
        if (!$locker->check($userKey . $user)) {
            $locker->lock($userKey . $user);

            // Logger 用户登录失败上限
            return [
                'error' => 2,
                'msg' => '当前用户登录失败次数以上限，请稍后再试'
            ];
        }
        if (!Hash::check($this->post['password'], $data['password'])) {
            $locker->inc($userKey . $user);

            // Logger 用户登录密码不正确
            return [
                'error' => 1,
                'msg' => '当前用户认证不成功'
            ];
        }
        $locker->remove('ip:' . $ip);
        $locker->remove($userKey . $user);

        // Logger ISP
        return $this->create('system', [
            'user' => $data['username'],
        ]);
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
     * @throws Exception
     */
    public function resource(): array
    {
        $router = ResourceRedis::create()->get();
        $user = Context::get('auth')->user;
        $data = AdminRedis::create()->get($user);
        $resourceData = [
            ...RoleRedis::create()->get($data['role'], 'resource'),
            ...$data['resource']
        ];
        $routerRole = array_unique($resourceData);
        $lists = array_filter($router,
            static fn($v) => in_array($v['key'], $routerRole, true),
            ARRAY_FILTER_USE_BOTH
        );
        return [
            'error' => 0,
            'data' => array_values($lists)
        ];
    }

    /**
     * 获取个人信息
     * @return array
     * @throws Exception
     */
    public function information(): array
    {
        $user = Context::get('auth')->user;
        $data = Db::name('admin')
            ->where('username', '=', $user)
            ->find();

        return [
            'error' => 0,
            'data' => [
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'realname' => $data['realname'],
                'avatar' => $data['avatar']
            ]
        ];
    }

    /**
     * 更新个人信息
     * @return array
     * @throws Exception
     */
    public function update(): array
    {
        validate([
            'old_password' => [
                'between:12,20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&-+])(?=.*[0-9])[\w|@$!%*?&-+]+$/'
            ],
            'new_password' => [
                'requireWith:old_password',
                'between:12,20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&-+])(?=.*[0-9])[\w|@$!%*?&-+]+$/'
            ],
        ])->check($this->post);

        $user = Context::get('auth')->user;
        $data = Db::name('admin')
            ->where('username', '=', $user)
            ->find();

        if (!empty($this->post['old_password'])) {
            if (!Hash::check($this->post['old_password'], $data['password'])) {
                return [
                    'error' => 2,
                    'msg' => 'password verification failed'
                ];
            }
            $this->post['password'] = Hash::create($this->post['new_password']);
        }

        unset($this->post['old_password'], $this->post['new_password']);
        Db::name('admin')
            ->where('username', '=', $user)
            ->update($this->post);
        AdminRedis::create()->clear();
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }

    /**
     * 对象存储签名
     * @return array
     * @throws Exception
     */
    public function presigned(): array
    {
        return Cos::generatePostPresigned([
            ['content-length-range', 0, 104857600]
        ]);
    }

    /**
     * 对象存储删除对象
     * @return array
     */
    public function objectDelete(): array
    {
        validate([
            'keys' => 'require|array'
        ])->check($this->post);
        Cos::delete($this->post['keys']);
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }
}
