<?php
declare (strict_types=1);

namespace app\system\redis;

use Exception;
use think\facade\Db;
use think\redis\RedisModel;

class ResourceRedis extends RedisModel
{
    protected $key = 'system:resource';
    private $data = [];

    /**
     * 清除缓存
     */
    public function clear(): void
    {
        $this->redis->del([$this->getKey()]);
    }

    /**
     * 获取资源
     * @return array
     * @throws Exception
     */
    public function get(): array
    {
        if (!$this->redis->exists($this->getKey())) {
            $this->update();
        } else {
            $raws = $this->redis->get($this->getKey());
            $this->data = json_decode($raws, true);
        }
        return $this->data;
    }

    /**
     * 刷新资源
     * @throws Exception
     */
    private function update(): void
    {
        $lists = Db::name('resource')
            ->where('status', '=', 1)
            ->withoutField(['id', 'sort', 'status', 'create_time', 'update_time'])
            ->order('sort')
            ->select();

        if ($lists->isEmpty()) {
            return;
        }

        $this->redis->set($this->getKey(), $lists->toJson());
        $this->data = $lists->toArray();
    }
}
