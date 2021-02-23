<?php
declare (strict_types=1);

namespace app\system\redis;

use Exception;
use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class RoleRedis extends RedisModel
{
    protected string $key = 'system:role';

    /**
     * 清除缓存
     */
    public function clear(): void
    {
        $this->redis->del([$this->getKey()]);
    }

    /**
     * @param array $keys 权限组键
     * @param string $type 权限类型
     * @return array
     * @throws Exception
     */
    public function get(array $keys, string $type): array
    {
        if (!$this->redis->exists($this->getKey())) {
            $this->update();
        }
        $raws = $this->redis->hmget($this->getKey(), $keys);
        $lists = [];
        foreach ($raws as $value) {
            $data = json_decode($value, true);
            array_push($lists, ...$data[$type]);
        }
        return $lists;
    }

    /**
     * 刷新权限组缓存
     * @throws  Exception
     */
    private function update(): void
    {
        $query = Db::name('role_mix')
            ->where('status', '=', 1)
            ->field(['key', 'acl', 'resource', 'permission'])
            ->select();

        if ($query->isEmpty()) {
            return;
        }
        $lists = [];
        foreach ($query->toArray() as $value) {
            $lists[$value->key] = json_encode([
                'acl' => !empty($value->acl) ? explode(',', $value->acl) : [],
                'resource' => !empty($value->resource) ? explode(',', $value->resource) : [],
                'permission' => !empty($value->permission) ? explode(',', $value->permission) : []
            ]);
        }
        $this->redis->hmset($this->getKey(), $lists);
    }
}
