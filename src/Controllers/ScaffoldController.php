<?php

namespace Encore\Admin\Helpers\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Helpers\Scaffold\ControllerCreator;
use Encore\Admin\Helpers\Scaffold\LangCreator;
use Encore\Admin\Helpers\Scaffold\MigrationCreator;
use Encore\Admin\Helpers\Scaffold\ModelCreator;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class ScaffoldController extends Controller
{
    public static $dbTypes = [
        'string', 'integer', 'text', 'float', 'double', 'decimal', 'boolean', 'date', 'time',
        'dateTime', 'timestamp', 'char', 'mediumText', 'longText', 'tinyInteger', 'smallInteger',
        'mediumInteger', 'bigInteger', 'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger',
        'unsignedInteger', 'unsignedBigInteger', 'enum', 'json', 'jsonb', 'dateTimeTz', 'timeTz',
        'timestampTz', 'nullableTimestamps', 'binary', 'ipAddress', 'macAddress',
    ];

    public static $dataTypeMap = [
        'int'                => 'integer',
        'int@unsigned'       => 'unsignedInteger',
        'tinyint'            => 'tinyInteger',
        'tinyint@unsigned'   => 'unsignedTinyInteger',
        'smallint'           => 'smallInteger',
        'smallint@unsigned'  => 'unsignedSmallInteger',
        'mediumint'          => 'mediumInteger',
        'mediumint@unsigned' => 'unsignedMediumInteger',
        'bigint'             => 'bigInteger',
        'bigint@unsigned'    => 'unsignedBigInteger',

        'date'      => 'date',
        'time'      => 'time',
        'datetime'  => 'dateTime',
        'timestamp' => 'timestamp',

        'enum'   => 'enum',
        'json'   => 'json',
        'binary' => 'binary',

        'float'   => 'float',
        'double'  => 'double',
        'decimal' => 'decimal',

        'varchar'    => 'string',
        'char'       => 'char',
        'text'       => 'text',
        'mediumtext' => 'mediumText',
        'longtext'   => 'longText',
    ];

    public function index()
    {
        if ($tableName = request('singular')) {
            return $this->singular($tableName);
        }
        return Admin::content(function (Content $content) {
            $content->header('脚手架');

            $dbTypes = [
                'string', 'integer', 'text', 'float', 'double', 'decimal', 'boolean', 'date', 'time',
                'dateTime', 'timestamp', 'char', 'mediumText', 'longText', 'tinyInteger', 'smallInteger',
                'mediumInteger', 'bigInteger', 'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger',
                'unsignedInteger', 'unsignedBigInteger', 'enum', 'json', 'jsonb', 'dateTimeTz', 'timeTz',
                'timestampTz', 'nullableTimestamps', 'binary', 'ipAddress', 'macAddress',
            ];
            $tables = collect($this->getDatabaseColumns())->map(function ($v) {
                return array_keys($v);
            })->toArray();
            $action = URL::current();
            $namespaceBase = 'App\\'.implode('\\', array_map(function ($name) {
                return Str::studly($name);
            }, explode(DIRECTORY_SEPARATOR, substr(config('admin.directory'), strlen(app_path().DIRECTORY_SEPARATOR)))));
            $dataTypeMap = static::$dataTypeMap;

            $content->row(view('laravel-admin-helpers::scaffold', compact('dbTypes', 'action', 'tables', 'namespaceBase', 'dataTypeMap')));
        });
    }

    public function store(Request $request)
    {
        $paths = [];
        $message = '';
        $creates = (array) $request->get('create');
        $table = Str::slug($request->get('table_name'), '_');
        $controller = $request->get('controller_name');
        $model = $request->get('model_name');

        try {

            // 1. Create model.
            if (in_array('model', $creates)) {
                $modelCreator = new ModelCreator($request->get('table_name'), $model);

                $paths['model'] = $modelCreator->create(
                    $request->get('primary_key'),
                    $request->get('timestamps') == 'on',
                    $request->get('soft_deletes') == 'on'
                );
            }

            // 2. Create controller.
            if (in_array('controller', $creates)) {
                $paths['controller'] = (new ControllerCreator($controller))
                    ->create($request->get('model_name'), $request->get('fields'));
            }

            if (in_array('lang', $creates)) {
                $paths['lang'] = (new LangCreator($request->get('fields')))
                    ->create($controller, $request->get('translate_title'));
            }

            // 3. Create migration.
            if (in_array('migration', $creates)) {
                $migrationName = 'create_'.$table.'_table';

                $paths['migration'] = (new MigrationCreator(app('files')))->buildBluePrint(
                    $request->get('fields'),
                    $request->get('primary_key', 'id'),
                    $request->get('timestamps') == 'on',
                    $request->get('soft_deletes') == 'on'
                )->create($migrationName, database_path('migrations'), $request->get('table_name'));
            }

            // 4. Run migrate.
            if (in_array('migrate', $request->get('create'))) {
                Artisan::call('migrate');
                $message = Artisan::output();
            }
        } catch (\Exception $exception) {

            // Delete generated files if exception thrown.
            app('files')->delete($paths);

            return $this->backWithException($exception);
        }

        return $this->backWithSuccess($paths, $message);
    }

    /**
     * @return array
     */
    public function table()
    {
        $db = addslashes(\request('db'));
        $table = \request('tb');
        if (! $table || ! $db) {
            return ['status' => 1, 'list' => []];
        }

        $tables = collect($this->getDatabaseColumns($db, $table))
            ->filter(function ($v, $k) use ($db) {
                return $k == $db;
            })->map(function ($v) use ($table) {
                return Arr::get($v, $table);
            })
            ->filter()
            ->first();

        return ['status' => 1, 'list' => $tables];
    }

    protected function backWithException(\Exception $exception)
    {
        $error = new MessageBag([
            'title'   => 'Error',
            'message' => $exception->getMessage(),
        ]);

        return back()->withInput()->with(compact('error'));
    }

    protected function backWithSuccess($paths, $message)
    {
        $messages = [];

        foreach ($paths as $name => $path) {
            $messages[] = ucfirst($name).": $path";
        }

        $messages[] = "<br />$message";

        $success = new MessageBag([
            'title'   => 'Success',
            'message' => implode('<br />', $messages),
        ]);

        return back()->with(compact('success'));
    }

    protected function singular($tableName)
    {
        return [
            'status' => 1,
            'value'  => Str::singular($tableName),
        ];
    }

    /**
     * @return array
     */
    protected function getDatabaseColumns($db = null, $tb = null)
    {
        $databases = Arr::where(config('database.connections', []), function ($value) {
            $supports = ['mysql'];

            return in_array(strtolower(Arr::get($value, 'driver')), $supports);
        });

        $data = [];

        try {
            foreach ($databases as $connectName => $value) {
                if ($db && $db != $value['database']) {
                    continue;
                }

                $sql = sprintf('SELECT * FROM information_schema.columns WHERE table_schema = "%s"', $value['database']);

                if ($tb) {
                    $p = Arr::get($value, 'prefix');

                    $sql .= " AND TABLE_NAME = '{$p}{$tb}'";
                }

                $sql .= ' ORDER BY `ORDINAL_POSITION` ASC';

                $tmp = DB::connection($connectName)->select($sql);

                $collection = collect($tmp)->map(function ($v) use ($value) {
                    if (! $p = Arr::get($value, 'prefix')) {
                        return (array) $v;
                    }
                    $v = (array) $v;

                    $v['TABLE_NAME'] = Str::replaceFirst($p, '', $v['TABLE_NAME']);

                    return $v;
                });

                $data[$value['database']] = $collection->groupBy('TABLE_NAME')->map(function ($v) {
                    return collect($v)->keyBy('COLUMN_NAME')->map(function ($v) {
                        $v['COLUMN_TYPE'] = strtolower($v['COLUMN_TYPE']);
                        $v['DATA_TYPE'] = strtolower($v['DATA_TYPE']);

                        if (Str::contains($v['COLUMN_TYPE'], 'unsigned')) {
                            $v['DATA_TYPE'] .= '@unsigned';
                        }

                        return [
                            'type'     => $v['DATA_TYPE'],
                            'default'  => $v['COLUMN_DEFAULT'],
                            'nullable' => $v['IS_NULLABLE'],
                            'key'      => $v['COLUMN_KEY'],
                            'id'       => $v['COLUMN_KEY'] === 'PRI',
                            'comment'  => $v['COLUMN_COMMENT'],
                        ];
                    })->toArray();
                })->toArray();
            }
        } catch (\Throwable $e) {
        }

        return $data;
    }
}
