<?php
declare (strict_types=1);

namespace app\system\redis;

use Exception;
use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class AclRedis extends RedisModel
{
    protected string $key = 'system:acl';

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
            $this->update();
        }

        $raws = $this->redis->hget($this->getKey(), $key);
        $data = json_decode($raws, true);

        switch ($policy) {
            case 0:
                return $data['read'];
            case 1:
                return [
                    ...$data['read'],
                    ...$data['write']
                ];
            default:
                return [];
        }
    }

    /**
     * 更新缓存
     * @throws Exception
     */
    private function update(): void
    {
        $query = Db::name('acl')
            ->where('status', '=', 1)
            ->field(['key', 'write', 'read'])
            ->select();

        if ($query->isEmpty()) {
            return;
        }

        $lists = [];
        foreach ($query->toArray() as $value) {
            $lists[$value['key']] = json_encode([
                'write' => !empty($value['write']) ? explode(',', $value['write']) : [],
                'read' => !empty($value['read']) ? explode(',', $value['read']) : []
            ]);
        }

        $this->redis->hmset($this->getKey(), $lists);
    }
}
