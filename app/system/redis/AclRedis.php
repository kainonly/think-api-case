<?php
declare (strict_types=1);

namespace app\system\redis;

use Exception;
use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class AclRedis extends RedisModel
{
    protected $key = 'system:acl';
    private $data = [];

    /**
     * 清除缓存
     */
    public function clear(): void
    {
        $this->redis->del([$this->getKey()]);
    }

    /**
     * @param string $key 访问控制键
     * @param int $policy 控制策略
     * @return array
     * @throws Exception
     */
    public function get(string $key, int $policy): array
    {
        if (!$this->redis->exists($this->getKey())) {
            $this->update($key);
        } else {
            $raws = $this->redis->hget($this->getKey(), $key);
            $this->data = !empty($raws) ? json_decode($raws, true) : [];
        }
        switch ($policy) {
            case 0:
                return explode(',', $this->data['read']);
            case 1:
                return array_merge(
                    explode(',', $this->data['read']),
                    explode(',', $this->data['write'])
                );
            default:
                return [];
        }
    }

    /**
     * 更新缓存
     * @param string $key 访问控制键
     * @throws Exception
     */
    private function update(string $key): void
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
                $pipeline->hset($this->getKey(), $value['key'], json_encode([
                    'write' => $value['write'],
                    'read' => $value['read']
                ]));
                if ($key == $value['key']) {
                    $this->data = [
                        'write' => $value['write'],
                        'read' => $value['read']
                    ];
                }
            }
        });
    }
}
