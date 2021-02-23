<?php
declare (strict_types=1);

namespace app\system\controller;

use app\system\redis\AdminRedis;
use app\system\redis\ResourceRedis;
use app\system\redis\RoleRedis;
use Exception;
use think\facade\Db;
use think\facade\Filesystem;
use think\facade\Request;
use think\helper\Arr;
use think\redis\library\UserLock;
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
        'system.rbac' => [
            'only' => ['uploads']
        ]
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

        $data = AdminRedis::create()->get($this->post['username']);
        if (empty($data)) {
            return [
                'error' => 1,
                'msg' => 'User does not exist or has been frozen'
            ];
        }
        $userLock = UserLock::create();
        if (!$userLock->check('admin:' . $this->post['username'])) {
            $userLock->lock('admin:' . $this->post['username']);
            return [
                'error' => 2,
                'msg' => 'You have failed to log in too many times, please try again later'
            ];
        }
        if (!Hash::check($this->post['password'], $data['password'])) {
            $userLock->inc('admin:' . $this->post['username']);
            return [
                'error' => 1,
                'msg' => 'User password verification is inconsistent'
            ];
        }
        $userLock->remove('admin:' . $this->post['username']);
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
        $userData = AdminRedis::create()->get(Context::get('auth')->user);
        $resourceData = [
            ...RoleRedis::create()->get($userData['role'], 'resource'),
            ...$userData['resource']
        ];
        $routerRole = array_unique($resourceData);
        $lists = Arr::where(
            $router,
            fn($v) => in_array($v['key'], $routerRole, true)
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
        $data = Db::name('admin')
            ->where('username', '=', Context::get('auth')->user)
            ->field(['email', 'phone', 'call', 'avatar'])
            ->find();

        return [
            'error' => 0,
            'data' => $data
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

        $username = Context::get('auth')->user;
        $data = Db::name('admin_basic')
            ->where('username', '=', $username)
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
        Db::name('admin_basic')
            ->where('username', '=', $username)
            ->update($this->post);

        AdminRedis::create()->clear();
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }

    /**
     * 文件上传
     * @param string $name
     * @return array
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
        return !empty($saveName) ? [
            'error' => 0,
            'data' => [
                'save_name' => $saveName,
            ]
        ] : [
            'error' => 1,
            'msg' => 'upload failed'
        ];
    }

    /**
     * 对象存储签名
     * @return array
     * @throws Exception
     */
    public function cosPresigned(): array
    {
        return Cos::generatePostPresigned([
            ['content-length-range', 0, 104857600]
        ]);
    }
}
