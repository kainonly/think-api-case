<?php

namespace app\system\redis;

use think\facade\Db;
use Predis\Pipeline\Pipeline;
use think\redis\RedisModel;

class Admin extends RedisModel
{
    protected $key = 'system:admin';
    private $rows = [];

    /**
     * 清除缓存
     */
    public function clear()
    {
        $this->redis->del([$this->key]);
    }

    /**
     * 获取用户缓存
     * @param string $username
     * @return array
     * @throws \Exception
     */
    public function get(string $username)
    {
        if (!$this->redis->exists($this->key)) {
            $this->update($username);
        } else {
            $this->rows = json_decode($this->redis->hGet($this->key, $username), true);
        }
        return $this->rows;
    }

    /**
     * 缓存管理员刷新
     * @param string $username
     * @throws \Exception
     */
    private function update(string $username)
    {

        $lists = Db::name('admin')
            ->where('status', '=', 1)
            ->field(['id', 'role', 'username', 'password'])
            ->select();

        if ($lists->isEmpty()) {
            return;
        }

        $this->redis->pipeline(function (Pipeline $pipeline) use ($username, $lists) {
            foreach ($lists->toArray() as $key => $value) {
                $pipeline->hset($this->key, $value['username'], json_encode($value));
                if ($username == $value['username']) {
                    $this->rows = $value;
                }
            }
        });
    }
}
