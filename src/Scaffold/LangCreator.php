<?php

namespace Encore\Admin\Helpers\Scaffold;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class LangCreator
{
    protected $fields = [];

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * 生成语言包.
     *
     * @param  string  $controller
     * @param  string  $title
     * @return string
     */
    public function create(string $controller, ?string $title)
    {
        $controller = str_replace('Controller', '', class_basename($controller));

        $filename = $this->getLangPath($controller);
        if (is_file($filename)) {
            return;
        }

        $title = $title ?: $controller;

        $content = [
            'labels' => [
                $controller => $title,
                Str::slug($controller) => $title,
            ],
            'fields'  => [],
            'options' => [],
        ];
        foreach ($this->fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            $content['fields'][$field['name']] = $field['translation'] ?: $field['name'];
        }

        $files = app('files');
        if ($files->put($filename, static::exportArrayPhp($content))) {
            $files->chmod($filename, 0777);

            return $filename;
        }
    }

    /**
     * 获取语言包路径.
     *
     * @param  string  $controller
     * @return string
     */
    protected function getLangPath(string $controller)
    {
        $path = rtrim(app()->langPath(), '/').'/'.App::getLocale();

        return $path.'/'.Str::slug($controller).'.php';
    }

    public static function exportArray(array &$array, $level = 1)
    {
        $start = '[';
        $end = ']';

        $txt = "$start\n";

        foreach ($array as $k => &$v) {
            if (is_array($v)) {
                $pre = is_string($k) ? "'$k' => " : "$k => ";

                $txt .= str_repeat(' ', $level * 4).$pre.static::exportArray($v, $level + 1).",\n";

                continue;
            }
            $t = $v;

            if ($v === true) {
                $t = 'true';
            } elseif ($v === false) {
                $t = 'false';
            } elseif ($v === null) {
                $t = 'null';
            } elseif (is_string($v)) {
                $v = str_replace("'", "\\'", $v);
                $t = "'$v'";
            }

            $pre = is_string($k) ? "'$k' => " : "$k => ";

            $txt .= str_repeat(' ', $level * 4)."{$pre}{$t},\n";
        }

        return $txt.str_repeat(' ', ($level - 1) * 4).$end;
    }

    /**
     * @param  array  $array
     * @return string
     */
    public static function exportArrayPhp(array $array)
    {
        return "<?php \nreturn ".static::exportArray($array).";\n";
    }
}
