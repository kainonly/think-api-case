<?php

namespace app\index\job;

use think\queue\Job;

class JobTest
{
    public function fire(Job $job, $data)
    {
        var_dump($data);
        //如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
        $job->delete();
    }

    public function failed($data)
    {
        // ...任务达到最大重试次数后，失败了
    }
}