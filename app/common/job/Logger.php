<?php
declare(strict_types=1);

namespace app\common\job;

use think\facade\Db;
use think\queue\Job;

class Logger
{
    public function fire(Job $job, array $data): void
    {
        $result = Db::name('logger')->insert($data);
        if (!empty($result)) {
            $job->delete();
        }
    }
}