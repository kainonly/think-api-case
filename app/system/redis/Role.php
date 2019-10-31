<?php

namespace app\system\redis;

use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class Role extends RedisModel
{
    protected $key = 'system:role';
    private $rows = [];

    /**
     * 清除缓存
     */
    public function clear()
    {
        $this->redis->del([$this->key]);
    }

    /**
     * @param string $key 权限组键
     * @param string $type 权限类型
     * @return array
     * @throws \Exception
     */
    public function get(string $key, string $type)
    {
        if (!$this->redis->exists($this->key)) {
            $this->update($key);
        } else {
            $this->rows = json_decode($this->redis->hget($this->key, $key), true);
        }
        return explode(',', $this->rows[$type]);
    }

    /**
     * 刷新权限组缓存
     * @throws  \Exception
     */
    private function update(string $key)
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
                    $this->rows = [
                        'acl' => $value['acl'],
                        'resource' => $value['resource']
                    ];
                }
            }
        });
    }
}
