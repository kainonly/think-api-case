<?php
declare (strict_types=1);

namespace app\system\redis;

use Exception;
use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class AdminRedis extends RedisModel
{
    protected $key = 'system:admin';

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

        $query = Db::name('admin')
            ->where('status', '=', 1)
            ->field(['id', 'role', 'username', 'password'])
            ->select();

        if ($query->isEmpty()) {
            return;
        }

        $this->redis->pipeline(function (Pipeline $pipeline) use ($query) {
            foreach ($query->toArray() as $value) {
                $pipeline->hset($this->getKey(), $value['username'], json_encode($value));
            }
        });
    }
}
