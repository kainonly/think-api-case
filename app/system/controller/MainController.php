<?php
declare (strict_types=1);

namespace app\system\controller;

use Exception;
use think\facade\Db;
use app\system\redis\AdminRedis;
use app\system\redis\ResourceRedis;
use app\system\redis\RoleRedis;
use think\facade\Filesystem;
use think\facade\Request;
use think\redis\library\Lock;
use think\support\facade\Context;
use think\support\facade\Cos;
use think\support\facade\Hash;
use think\support\facade\Obs;
use think\support\facade\Oss;
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
            'mode' => 'require|number',
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
        $notAdmin = $this->post['mode'] !== 0;
        $data = $notAdmin ? $this->fetchUserData($user) : AdminRedis::create()->get($user);
        if (empty($data)) {
            $locker->inc('ip:' . $ip);
            // Logger 用户不存在或冻结
            return [
                'error' => 1,
                'msg' => '当前用户不存在或已被冻结'
            ];
        }
        $userKey = $notAdmin ? $this->fetchUserKey() : 'admin';
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
     * 其他模式下用户返回数据
     * @param string $user
     * @return array
     */
    protected function fetchUserData(string $user): array
    {
        return [];
    }

    /**
     * 其他模式下用户锁定
     * @return string
     */
    protected function fetchUserKey(): string
    {
        return 'user:';
    }

    /**
     * 认证返回，自定义标识变量
     * @param array $data 用户数据
     * @param int $mode 登录模式代码
     * @return array
     * @throws Exception
     */
    protected function loginAfter(array $data, int $mode): array
    {
        return $this->create('system', [
            'user' => $data['username'],
            'mode' => $mode
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
        if ($symbol['mode'] !== 0) {
            $data = $this->fetchUserData($symbol['user']);
        } else {
            $data = AdminRedis::create()->get($symbol['user']);
        }

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
        $notAdmin = Context::get('auth')->mode !== 0;
        $data = $notAdmin ? $this->fetchUserData($user) : AdminRedis::create()->get($user);
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
        if (Context::get('auth')->mode !== 0) {
            $data = $this->fetchInformation($user);
        } else {
            $data = Db::name('admin')
                ->where('username', '=', $user)
                ->find();
        }

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
     * 其他模式下用户数据信息
     * @param string $user
     * @return array
     */
    protected function fetchInformation(string $user): array
    {
        return [];
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
        $notAdmin = Context::get('auth')->mode !== 0;

        if ($notAdmin) {
            $data = $this->fetchInformation($user);
        } else {
            $data = Db::name('admin')
                ->where('username', '=', $user)
                ->find();
        }

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

        if ($notAdmin) {
            $this->setUpdate($user);
        } else {
            Db::name('admin')
                ->where('username', '=', $user)
                ->update($this->post);
            AdminRedis::create()->clear();
        }

        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }

    /**
     * 其他模式用户更新
     * @param string $user
     */
    protected function setUpdate(string $user): void
    {
        // TODO: 自定义
    }

    /**
     * 文件上传
     * @param string $name
     * @return array
     * @throws Exception
     */
    public function uploads($name = 'image'): array
    {
        $saveName = null;
        switch (config('filesystem.object_store')) {
            case 'aliyun':
                $saveName = Oss::put($name);
                break;
            case 'huaweicloud':
                $saveName = Obs::put($name);
                break;
            case 'qcloud':
                $saveName = Cos::put($name);
                break;
            default:
                $file = Request::file($name);
                $saveName = date('Ymd') . '/' .
                    uuid()->toString() . '.' .
                    $file->getOriginalExtension();
                Filesystem::disk('public')->putFileAs(
                    date('Ymd'),
                    $file,
                    uuid()->toString() . '.' . $file->getOriginalExtension()
                );
        }
        if (empty($saveName)) {
            return [
                'error' => 1,
                'msg' => '上传尚未完成'
            ];
        }

        return [
            'error' => 0,
            'data' => [
                'save_name' => $saveName,
            ]
        ];
    }

    /**
     * 对象存储签名
     * @return array
     * @throws Exception
     */
    public function presigned(): array
    {
        switch (config('filesystem.object_store')) {
            case 'aliyun':
                return Oss::generatePostPresigned($this->presignedConditions());
            case 'huaweicloud':
                return Obs::generatePostPresigned($this->presignedConditions());
            case 'qcloud':
                return Cos::generatePostPresigned($this->presignedConditions());
        }
//        return Cos::generatePostPresigned([
//            ['content-length-range', 0, 104857600]
//        ]);
        return [];
    }

    /**
     * 设定对象存储 Policy
     * @return array
     */
    protected function presignedConditions(): array
    {
        return [];
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

        switch (config('filesystem.object_store')) {
            case 'aliyun':
                Oss::delete($this->post['keys']);
                break;
            case 'huaweicloud':
                Obs::delete($this->post['keys']);
                break;
            case 'qcloud':
                Cos::delete($this->post['keys']);
                break;
        }
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }
}
