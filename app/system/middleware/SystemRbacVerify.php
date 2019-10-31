<?php

namespace app\system\middleware;

use app\system\redis\Acl;
use app\system\redis\Role;
use think\helper\Str;
use think\Request;
use think\support\facade\Context;

class SystemRbacVerify
{
    private $except_prefix = [
        'valided'
    ];

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, \Closure $next)
    {
        $controller = Str::lower($request->controller());
        $action = Str::lower($request->action());
        foreach ($this->except_prefix as $value) {
            if (strpos($action, $value) !== false) {
                return $next($request);
            }
        }
        $roleKey = Context::get('auth')->role;
        $roleLists = [];
        foreach ($roleKey as $value) {
            array_push($roleLists, ...(new Role)->get($value, 'acl'));
        }
        rsort($roleLists);
        $policy = null;
        foreach ($roleLists as $value) {
            $role = explode(':', $value);
            if (Str::lower($role[0]) == $controller) {
                $policy = $role[1];
                break;
            }
        }
        if (is_null($policy)) {
            return json([
                'error' => 1,
                'msg' => 'rbac invalid'
            ]);
        }
        $aclLists = array_map(function ($value) {
            return Str::lower($value);
        }, (new Acl)->get($controller, $policy));
        if (empty($aclLists)) {
            return json([
                'error' => 1,
                'msg' => 'rbac invalid'
            ]);
        }
        return in_array($action, $aclLists) ? $next($request) : json([
            'error' => 1,
            'msg' => 'rbac invalid'
        ]);
    }
}
