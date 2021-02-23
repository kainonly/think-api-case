<?php
declare (strict_types=1);

namespace app\system\middleware;

use app\system\redis\AdminRedis;
use Closure;
use Exception;
use think\Request;
use think\Response;
use think\helper\Str;
use app\system\redis\AclRedis;
use app\system\redis\RoleRedis;
use think\support\facade\Context;

class SystemRbacVerify
{
    /**
     * 排除前缀
     * @var array|string[]
     */
    private array $except_prefix = [
        'valided'
    ];

    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        $controller = Str::snake($request->controller(), '_');
        $action = $request->action();
        foreach ($this->except_prefix as $value) {
            if (strpos($action, $value) !== false) {
                return $next($request);
            }
        }
        $user = AdminRedis::create()->get(Context::get('auth')->user);
        $acl = [
            ...RoleRedis::create()->get($user['role'], 'acl'),
            ...$user['acl']
        ];
        $activePolicy = null;
        foreach ($acl as $value) {
            [$aclKey, $policy] = explode(':', $value);
            if ($controller === $aclKey) {
                $activePolicy = $policy;
                if ($policy === 1) {
                    break;
                }
            }
        }
        if ($activePolicy === null) {
            return json([
                'error' => 1,
                'msg' => 'rbac invalid, policy is empty'
            ]);
        }
        $lists = AclRedis::create()->get($controller, (int)$activePolicy);
        if (empty($lists)) {
            return json([
                'error' => 1,
                'msg' => 'rbac invalid, acl is empty'
            ]);
        }
        if (!in_array($action, $lists, true)) {
            return json([
                'error' => 1,
                'msg' => 'rbac invalid, access denied'
            ]);
        }
        return $next($request);
    }
}
