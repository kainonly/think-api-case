<?php
declare(strict_types=1);

namespace app\common\jobs;

use think\facade\Db;
use think\queue\Job;

class LoggerJob
{
    public function fire(Job $job, array $data): void
    {
        if ($job->attempts() > 3) {
            $job->delete();
        }
        switch ($data['channel']) {
            case 'request':
                $result = Db::name('request_log')->insert($data['values']);
                break;
            case 'login':
                $result = Db::name('login_log')->insert($data['values']);
                break;
        }
        if (!empty($result)) {
            $job->delete();
        }
    }
}