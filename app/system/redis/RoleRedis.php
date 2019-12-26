<?php
declare (strict_types=1);

namespace app\system\redis;

use Exception;
use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class RoleRedis extends RedisModel
{
    protected $key = 'system:role';
    private $data = [];

    /**
     * 清除缓存
     */
    public function clear(): void
    {
        $this->redis->del([$this->key]);
    }

    /**
     * @param string $key 权限组键
     * @param string $type 权限类型
     * @return array
     * @throws Exception
     */
    public function get(string $key, string $type): array
    {
        if (!$this->redis->exists($this->key)) {
            $this->update($key);
        } else {
            $raws = $this->redis->hget($this->key, $key);
            if (!empty($raws)) {
                $this->data = json_decode($raws, true);
            } else {
                return [];
            }
        }
        return explode(',', $this->data[$type]);
    }

    /**
     * 刷新权限组缓存
     * @throws  Exception
     */
    private function update(string $key): void
    {

        $lists = Db::name('role')
            ->where('status', '=', 1)
            ->field(['key', 'acl', 'resource'])
            ->select();

        if ($lists->isEmpty()) {
            return;
        }

        $this->redis->pipeline(function (Pipeline $pipeline) use ($key, $lists) {
            foreach ($lists->toArray() as $key => $value) {
                $pipeline->hset($this->key, $value['key'], json_encode([
                    'acl' => $value['acl'],
                    'resource' => $value['resource']
                ]));
                if ($key == $value['key']) {
                    $this->data = [
                        'acl' => $value['acl'],
                        'resource' => $value['resource']
                    ];
                }
            }
        });
    }
}
