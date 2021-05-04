<?php
declare (strict_types=1);

namespace app\index\controller;

use app\common\BaseController;
use think\App;
use think\facade\Filesystem;
use think\facade\Request;
use think\qcloud\extra\ApiGatewayInterface;
use think\Response;

class IndexController extends BaseController
{
    public function index(): Response
    {
        return json([
            'framework' => 'thinkphp',
            'version' => app()->version(),
        ]);
    }

    public function upload(): Response
    {
        $file = Request::file('file');
        $fileName = date('Ymd') . '/' .
            uuid()->toString() . '.' .
            $file->getOriginalExtension();
        Filesystem::disk('public')->putFileAs(
            date('Ymd'),
            $file,
            uuid()->toString() . '.' . $file->getOriginalExtension()
        );
        return json([
            'error' => 0,
            'data' => [
                'save_name' => $fileName,
            ]
        ]);
    }

    public function ip(ApiGatewayInterface $apiGateway): void
    {
        $response = $apiGateway->request('ip2region', '/ip2region', [
            'ip' => '36.101.180.191'
        ]);
        if ($response->getStatusCode() === 200) {
            $contexts = $response->getBody()->getContents();
            $body = json_decode($contexts, true);
            dump($body);
        }
    }
}
