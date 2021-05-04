<?php
declare (strict_types=1);

namespace app\system\middleware;

use app\common\jobs\Logger;
use Closure;
use think\facade\Queue;
use think\Request;
use think\Response;
use think\support\facade\Context;

/**
 * 请求监听
 * Class Spy
 * @package think\support\middleware
 */
class Spy
{
    public function handle(Request $request, Closure $next): Response
    {
        Queue::push(Logger::class, [
            'channel' => 'request',
            'values' => [
                'path' => $request->url(),
                'controller' => $request->controller(),
                'action' => $request->action(),
                'mode' => Context::get('auth')->mode ?? 0,
                'username' => Context::get('auth')->user ?? 'none',
                'body' => $request->getContent(),
                'time' => $request->time(),
            ]
        ]);
        return $next($request);
    }
}