<?php
declare (strict_types=1);

namespace app\system\redis;

use Exception;
use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class AdminRedis extends RedisModel
{
    protected string $key = 'system:admin';

    /**
     * 清除缓存
     */
    public function clear(): void
    {
        $this->redis->del([$this->getKey()]);
    }

    /**
     * 获取用户缓存
     * @param string $username
     * @return array
     * @throws Exception
     */
    public function get(string $username): array
    {
        if (!$this->redis->exists($this->getKey())) {
            $this->update();
        }

        $raws = $this->redis->hGet($this->getKey(), $username);
        return !empty($raws) ? json_decode($raws, true) : [];
    }

    /**
     * 缓存管理员刷新
     * @throws Exception
     */
    private function update(): void
    {
        $query = Db::name('admin_mix')
            ->where('status', '=', 1)
            ->field(['id', 'role', 'username', 'password', 'resource', 'acl', 'permission'])
            ->select();

        if ($query->isEmpty()) {
            return;
        }

        $lists = [];
        foreach ($query->toArray() as $value) {
            $value['role'] = explode(',', $value['role']);
            $value['resource'] = !empty($value['resource']) ? explode(',', $value['resource']) : [];
            $value['acl'] = !empty($value['acl']) ? explode(',', $value['acl']) : [];
            $value['permission'] = !empty($value['permission']) ? explode(',', $value['permission']) : [];
            $lists[$value['username']] = json_encode($value);
        }
        $this->redis->hmset($this->getKey(), $lists);
    }
}
