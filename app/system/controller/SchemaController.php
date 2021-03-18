<?php
declare(strict_types=1);

namespace app\system\controller;

use think\bit\common\AddModel;
use think\bit\common\DeleteModel;
use think\bit\common\EditModel;
use think\bit\common\GetModel;
use think\bit\common\ListsModel;
use think\bit\common\OriginListsModel;
use think\facade\Db;

class SchemaController extends BaseController
{
    use ListsModel, OriginListsModel, GetModel, AddModel, EditModel, DeleteModel;

    protected string $model = 'schema';
    protected array $add_validate = [
        'name' => 'require|array',
        'table' => 'require',
        'type' => 'require',
    ];
    protected array $edit_validate = [
        'name' => 'requireIf:switch,0|array',
        'table' => 'requireIf:switch,0',
        'type' => 'requireIf:switch,0',
    ];

    public function addBeforeHooks(): bool
    {
        $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
        return true;
    }

    public function addAfterHooks($id): bool
    {
        $now = time();
        $result = Db::name('column')->insertAll([
            [
                'schema' => $this->post['table'],
                'column' => 'id',
                'datatype' => 'system',
                'name' => json_encode([
                    'zh_cn' => '主键',
                    'en_us' => 'Primary key'
                ], JSON_UNESCAPED_UNICODE),
                'description' => '系统默认字段',
                'sort' => 0,
                'create_time' => $now,
                'update_time' => $now
            ],
            [
                'schema' => $this->post['table'],
                'column' => 'create_time',
                'datatype' => 'system',
                'name' => json_encode([
                    'zh_cn' => '创建时间',
                    'en_us' => 'Create Time'
                ], JSON_UNESCAPED_UNICODE),
                'description' => '系统默认字段',
                'sort' => 0,
                'create_time' => $now,
                'update_time' => $now
            ],
            [
                'schema' => $this->post['table'],
                'column' => 'update_time',
                'datatype' => 'system',
                'name' => json_encode([
                    'zh_cn' => '更新时间',
                    'en_us' => 'Update Time'
                ], JSON_UNESCAPED_UNICODE),
                'description' => '系统默认字段',
                'sort' => 0,
                'create_time' => $now,
                'update_time' => $now
            ]
        ]);
        return $result > 0;
    }

    public function editBeforeHooks(): bool
    {
        if (!$this->edit_switch) {
            $this->post['name'] = json_encode($this->post['name'], JSON_UNESCAPED_UNICODE);
        }
        return true;
    }

    public function validedTable(): array
    {
        if (empty($this->post['table'])) {
            return [
                'error' => 1,
                'msg' => '请求参数必须存在[table]值'
            ];
        }

        $exists = Db::name($this->model)
            ->where('table', '=', $this->post['table'])
            ->count();

        return [
            'error' => 0,
            'data' => [
                'exists' => !empty($exists)
            ]
        ];
    }
}