<?php

namespace app\system\redis;

use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class Acl extends RedisModel
{
    protected $key = 'system:acl';
    private $rows = [];

    /**
     * 清除缓存
     */
    public function clear()
    {
        $this->redis->del([$this->key]);
    }

    /**
     * @param string $key 访问控制键
     * @param int $policy 控制策略
     * @return array
     * @throws \Exception
     */
    public function get(string $key, int $policy)
    {
        if (!$this->redis->exists($this->key)) {
            $this->update($key);
        } else {
            $this->rows = json_decode($this->redis->hget($this->key, $key), true);
        }

        switch ($policy) {
            case 0:
                return explode(',', $this->rows['read']);
            case 1:
                return array_merge(
                    explode(',', $this->rows['read']),
                    explode(',', $this->rows['write'])
                );
            default:
                return [];
        }
    }

    /**
     * 更新缓存
     * @param string $key 访问控制键
     * @throws \Exception
     */
    private function update(string $key)
    {
        $lists = Db::name('acl')
            ->where('status', '=', 1)
            ->field(['key', 'write', 'read'])
            ->select();

        if ($lists->isEmpty()) {
            return;
        }

        $this->redis->pipeline(function (Pipeline $pipeline) use ($key, $lists) {
            foreach ($lists->toArray() as $index => $value) {
                $pipeline->hset($this->key, $value['key'], json_encode([
                    'write' => $value['write'],
                    'read' => $value['read']
                ]));
                if ($key == $value['key']) {
                    $this->rows = [
                        'write' => $value['write'],
                        'read' => $value['read']
                    ];
                }
            }
        });
    }
}
