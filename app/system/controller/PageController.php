<?php
declare(strict_types=1);

namespace app\system\controller;

use think\bit\common\GetModel;
use think\facade\Db;

class PageController extends BaseController
{
    use GetModel;

    protected string $model = 'page';
    protected array $get_without_field = ['id', 'create_time', 'update_time'];

    public function update(): array
    {
        if (empty($this->post['key'])) {
            return [
                'error' => 1,
                'msg' => '请求参数必须存在[key]值'
            ];
        }
        $key = $this->post['key'];
        $columns = Db::name('column')
            ->where('schema', '=', $key)
            ->where('datatype', '<>', 'system')
            ->select();
        $rules = [];
        foreach ($columns as $value) {
            $value['extra'] = json_decode($value['extra'], true);
            if (in_array($value['datatype'], ['date', 'datetime'])) {
                $this->post[$value['column']] = strtotime($this->post[$value['column']]);
            }
            if ($value['extra']['required']) {
                $rules[$value['column']] = 'require';
            }
        }
        validate($rules)->check($this->post);
        $exists = Db::name('page')
            ->where('key', '=', $key)
            ->count();
        $now = time();
        $data = [];
        $data['title'] = json_encode($this->post['title'], JSON_UNESCAPED_UNICODE);
        $data['content'] = json_encode($this->post['content'], JSON_UNESCAPED_UNICODE);
        unset($this->post['key'], $this->post['i18n'], $this->post['title'], $this->post['content']);
        $data['extra'] = json_encode(!empty($this->post) ? (object)$this->post : (object)[]);
        $data['update_time'] = $now;
        if (!$exists) {
            $data['key'] = $key;
            $data['create_time'] = $now;
            Db::name('page')->insert($data);
        } else {
            Db::name('page')
                ->where('key', '=', $key)
                ->update($data);
        }
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }
}