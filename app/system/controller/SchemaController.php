<?php
declare(strict_types=1);

namespace app\system\controller;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Exception;
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
        $this->post['description'] = json_encode((object)$this->post['description'], JSON_UNESCAPED_UNICODE);
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
                'description' => json_encode([
                    'zh_cn' => '系统默认字段',
                    'en_us' => ''
                ], JSON_UNESCAPED_UNICODE),
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
                'description' => json_encode([
                    'zh_cn' => '系统默认字段',
                    'en_us' => ''
                ], JSON_UNESCAPED_UNICODE),
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
                'description' => json_encode([
                    'zh_cn' => '系统默认字段',
                    'en_us' => ''
                ], JSON_UNESCAPED_UNICODE),
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
            $this->post['description'] = json_encode((object)$this->post['description'], JSON_UNESCAPED_UNICODE);
        }
        return true;
    }

    /**
     * 发布数据表
     * @return array
     * @throws Exception
     */
    public function publish(): array
    {
        validate([
            'table' => 'require',
        ]);
        $schema = get_schema_manager();
        $name = get_table_name($this->post['table']);
        if ($schema->tablesExist($name)) {
            $version = time();
            $schema->renameTable($name, $name . '_' . $version);
            Db::name('schema_history')->insert([
                'schema' => $this->post['table'],
                'remark' => $this->post['remark'] ?? '',
                'version' => $version
            ]);
        }
        $lists = Db::name('column')
            ->where('schema', '=', $this->post['table'])
            ->where('status', '=', 1)
            ->order('sort')
            ->select();
        $table = new Table($name);
        foreach ($lists as $value) {
            $column = $value['column'];
            $datatype = $value['datatype'];
            $extra = json_decode($value['extra']);
            if ($column === 'id') {
                $table->addColumn('id', Types::BIGINT, [
                    'unsigned' => true,
                    'autoincrement' => true
                ]);
                continue;
            }
            if ($column === 'create_time') {
                $table->addColumn('create_time', Types::BIGINT, [
                    'unsigned' => true,
                    'default' => 0
                ]);
                continue;
            }
            if ($column === 'update_time') {
                $table->addColumn('update_time', Types::BIGINT, [
                    'unsigned' => true,
                    'default' => 0
                ]);
                continue;
            }
            $type = null;
            $option = [];
            if (!$extra->required) {
                $option['notnull'] = false;
            }
            if (!$extra->default) {
                $option['default'] = $extra->default;
            }
            switch ($datatype) {
                case 'string':
                    $type = Types::TEXT;
                    if (!$extra->is_text) {
                        $type = Types::STRING;
                        $option['length'] = 200;
                    }
                    break;
                case 'i18n':
                    $type = Types::JSON;
                    $option = [
                        'default' => '{}'
                    ];
                    break;
                case 'richtext':
                    $type = Types::TEXT;
                    break;
                case 'number':
                    $type = Types::BIGINT;
                    if ($extra->is_sort) {
                        $type = Types::SMALLINT;
                        $option['unsigned'] = true;
                    }
                    break;
                case 'status':
                    $type = Types::BOOLEAN;
                    $option['unsigned'] = true;
                    break;
                case 'datetime':
                case 'date':
                    $type = Types::BIGINT;
                    $option['unsigned'] = true;
                    break;
                case 'picture':
                case 'video':
                    $type = Types::JSON;
                    break;
                case 'assoc':
                case 'enmu':
                    $type = Types::STRING;
                    $option['length'] = 200;
                    break;
            }
            $table->addColumn($column, $type, $option);
        }
        $table->setPrimaryKey(['id']);
        $schema->createTable($table);
        return [
            'error' => 0,
            'msg' => 'ok'
        ];
    }

    /**
     * 发布历史
     * @return array
     * @throws Exception
     */
    public function history(): array
    {
        validate([
            'table' => 'require'
        ])->check($this->post);

        $lists = Db::name('schema_history')
            ->where('schema', '=', $this->post['table'])
            ->order('version', 'desc')
            ->select();

        return [
            'error' => 0,
            'data' => $lists
        ];
    }

    /**
     * 表结构详情
     * @return array
     * @throws Exception
     */
    public function table(): array
    {
        validate([
            'table' => 'require'
        ])->check($this->post);

        $schema = get_schema_manager();
        $name = get_table_name($this->post['table']);
        $table = $schema->listTableDetails($name);

        return [
            'error' => 0,
            'data' => [
                'columns' => array_values(
                    array_map(static fn($v) => [
                        'name' => $v->getName(),
                        'type' => $v->getType()->getName(),
                        'length' => $v->getLength(),
                        'autoincrement' => $v->getAutoincrement(),
                        'unsigned' => $v->getUnsigned(),
                        'notnull' => $v->getNotnull(),
                        'default' => $v->getDefault(),
                        'comment' => $v->getComment()
                    ], $table->getColumns())
                ),
                'indexs' => array_values(
                    array_map(static fn($v) => [
                        'name' => $v->getName(),
                        'columns' => $v->getColumns(),
                        'unique' => $v->isUnique(),
                        'primary' => $v->isPrimary()
                    ], $table->getIndexes())
                )
            ]
        ];
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