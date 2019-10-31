<?php

namespace app\system\redis;

use think\facade\Db;
use think\redis\RedisModel;

class Resource extends RedisModel
{
    protected $key = 'system:resource';
    private $rows = [];

    /**
     * 清除缓存
     */
    public function clear()
    {
        $this->redis->del([$this->key]);
    }

    /**
     * 获取资源
     * @return array
     * @throws \Exception
     */
    public function get()
    {
        if (!$this->redis->exists($this->key)) {
            $this->update();
        } else {
            $this->rows = json_decode($this->redis->get($this->key), true);
        }

        return $this->rows;
    }

    /**
     * 刷新资源
     * @throws \Exception
     */
    private function update()
    {
        $lists = Db::name('resource')
            ->where('status', '=', 1)
            ->withoutField(['id', 'sort', 'status', 'create_time', 'update_time'])
            ->order('sort')
            ->select();

        if ($lists->isEmpty()) {
            return;
        }

        $this->redis->set($this->key, $lists->toJson());
        $this->rows = $lists->toArray();
    }
}
