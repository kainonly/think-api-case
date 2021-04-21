<?php
declare(strict_types=1);

namespace app\system\controller;

use Exception;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\OriginListsModel;
use think\facade\Db;

class ColumnController extends BaseController
{
    use OriginListsModel, GetModel, EditModel;

    protected string $model = 'column';
    protected array $origin_lists_orders = [
        'sort'
    ];

    /**
     * 更新数据表字段
     * @return array
     * @throws Exception
     */
    public function update(): array
    {
        $data = [];
        validate([
            'schema' => 'require',
            'columns' => function ($value) use (&$data) {
                if (!is_array($value)) {
                    return false;
                }
                $now = time();
                foreach ($value as $k => $v) {
                    $check = validate([
                        'column' => 'require',
                        'datatype' => 'require',
                        'name' => 'require|array',
                    ], [], false, false)->check($v);
                    if (!$check) {
                        return false;
                    }
                    $data[] = [
                        'schema' => $this->post['schema'],
                        'column' => $v['column'],
                        'datatype' => $v['datatype'],
                        'name' => json_encode($v['name'], JSON_UNESCAPED_UNICODE),
                        'description' => json_encode((object)$v['description'], JSON_UNESCAPED_UNICODE),
                        'sort' => $v['sort'],
                        'extra' => json_encode((object)$v['extra']),
                        'create_time' => $now,
                        'update_time' => $now
                    ];
                }
                return true;
            }
        ])->check($this->post);
        return Db::transaction(function () use ($data) {
            Db::name('column')
                ->where('schema', '=', $this->post['schema'])
                ->delete();
            $result = Db::name('column')->insertAll($data);
            return $result > 0;
        }) ? [
            'error' => 0,
            'msg' => 'ok'
        ] : [
            'error' => 1,
            'msg' => 'failed'
        ];
    }
}