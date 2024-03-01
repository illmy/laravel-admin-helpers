<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Scaffold</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <form method="post" action="{{$action}}" id="scaffold" pjax-container>

            <div class="box-body">

                <div class="form-horizontal">

                <div class="form-group">

                    <label for="inputTableName" class="col-sm-1 control-label">Table name</label>

                    <div class="col-sm-4">
                        <input type="text" name="table_name" class="form-control" id="inputTableName" placeholder="table name" value="{{ old('table_name') }}">
                    </div>
                    <div class=" col-sm-2" style="margin-left: -15px;">
                        <select class="choose-exist-table"  name="exist-table">
                            <option value="0" selected>选择已有数据表</option>
                            @foreach($tables as $db => $tb)
                                <optgroup label="{!! $db !!}">
                                    @foreach($tb as $v)
                                        <option value="{{$db}}|{{$v}}">{{$v}}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <span class="help-block hide" id="table-name-help">
                        <i class="fa fa-info"></i>&nbsp; Table name can't be empty!
                    </span>

                </div>
                <div class="form-group">
                    <label for="inputModelName" class="col-sm-1 control-label">Model</label>

                    <div class="col-sm-4">
                        <input type="text" name="model_name" class="form-control" id="inputModelName" placeholder="model" value="{{ old('model_name', "App\\Models\\") }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputControllerName" class="col-sm-1 control-label">Controller</label>

                    <div class="col-sm-4">
                        <input type="text" name="controller_name" class="form-control" id="inputControllerName" placeholder="controller" value="{{ old('controller_name', "App\\Admin\\Controllers\\") }}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-1 col-sm-11">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"  value="migration" name="create[]" /> 创建表迁移
                            </label>
                            <label>
                                <input type="checkbox" checked value="model" name="create[]" /> 创建模型
                            </label>
                            <label>
                                <input type="checkbox" checked value="controller" name="create[]" /> 创建控制器
                            </label>
                            <label>
                                <input type="checkbox" checked value="lang" name="create[]" /> 创建翻译
                            </label>
                            <label>
                                <input type="checkbox" value="migrate" name="create[]" /> 运行迁移
                            </label>
                        </div>
                    </div>
                </div>

                </div>

                <hr />

                <h4>Fields</h4>

                <table class="table table-hover" id="table-fields">
                    <thead>
                        <tr>
                            <th style="width: 200px">字段名</th>
                            <th>翻译</th>
                            <th>类型</th>
                            <th>允许空值</th>
                            <th>索引</th>
                            <th>默认值</th>
                            <th>注释</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td>
                            <input type="text" name="fields[0][name]" class="form-control" placeholder="字段名" />
                        </td>
                        <td>
                            <input type="text" name="fields[0][translation]" class="form-control" placeholder="翻译" />
                        </td>
                        <td>
                            <select style="width: 200px" name="fields[0][type]">
                                @foreach($dbTypes as $type)
                                    <option value="{{ $type }}">{{$type}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="checkbox" name="fields[0][nullable]" /></td>
                        <td>
                            <select style="width: 150px" name="fields[0][key]">
                                {{--<option value="primary">Primary</option>--}}
                                <option value="" selected>NULL</option>
                                <option value="unique">Unique</option>
                                <option value="index">Index</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" placeholder="default value" name="fields[0][default]"></td>
                        <td><input type="text" class="form-control" placeholder="comment" name="fields[0][comment]"></td>
                        <td><a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i> remove</a></td>
                    </tr>
                    </tbody>
                </table>

                <hr style="margin-top: 0;"/>

                <div class='form-inline margin' style="width: 100%">


                    <div class='form-group'>
                        <button type="button" class="btn btn-sm btn-success" id="add-table-field"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add field</button>
                    </div>

                    <div class='form-group pull-right' style="margin-right: 20px; margin-top: 5px;">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked name="timestamps"> Created_at & Updated_at
                            </label>
                            &nbsp;&nbsp;
                            <label>
                                <input type="checkbox" name="soft_deletes"> Soft deletes
                            </label>

                        </div>
                    </div>

                    <div class="form-group pull-right" style="margin-right: 20px;">
                        <label for="inputPrimaryKey">Primary key</label>
                        <input type="text" name="primary_key" class="form-control" id="inputPrimaryKey" placeholder="Primary key" value="id" style="width: 100px;">
                    </div>

                </div>

                {{--<hr />--}}

                {{--<h4>Relations</h4>--}}

                {{--<table class="table table-hover" id="model-relations">--}}
                    {{--<tbody>--}}
                    {{--<tr>--}}
                        {{--<th style="width: 200px">Relation name</th>--}}
                        {{--<th>Type</th>--}}
                        {{--<th>Related model</th>--}}
                        {{--<th>forignKey</th>--}}
                        {{--<th>OtherKey</th>--}}
                        {{--<th>With Pivot</th>--}}
                        {{--<th>Action</th>--}}
                    {{--</tr>--}}
                    {{--</tbody>--}}
                {{--</table>--}}

                {{--<hr style="margin-top: 0;"/>--}}

                {{--<div class='form-inline margin' style="width: 100%">--}}

                    {{--<div class='form-group'>--}}
                        {{--<button type="button" class="btn btn-sm btn-success" id="add-model-relation"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add relation</button>--}}
                    {{--</div>--}}

                {{--</div>--}}

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right">submit</button>
            </div>

            {{ csrf_field() }}

            <!-- /.box-footer -->
        </form>

    </div>

</div>

<template id="table-field-tpl">
    <tr>
        <td>
            <input type="text" value="{name}" name="fields[__index__][name]" class="form-control" placeholder="字段名" />
        </td>
        <td>
            <input type="text" value="{translation}" name="fields[__index__][translation]" class="form-control" placeholder="翻译" />
        </td>
        <td>
            <select style="width: 200px" name="fields[__index__][type]">
                @foreach($dbTypes as $type)
                    <option value="{{ $type }}">{{$type}}</option>
                @endforeach
            </select>
        </td>
        <td><input {nullable} type="checkbox" name="fields[__index__][nullable]" /></td>
        <td>
            <select style="width: 150px" name="fields[__index__][key]">
                <option value="" selected>NULL</option>
                <option value="unique">Unique</option>
                <option value="index">Index</option>
            </select>
        </td>
        <td><input value="{default}"  type="text" class="form-control" placeholder="default value" name="fields[__index__][default]"></td>
        <td><input value="{comment}" type="text" class="form-control" placeholder="comment" name="fields[__index__][comment]"></td>
        <td><a class="btn btn-sm btn-danger table-field-remove"><i class="fa fa-trash"></i> remove</a></td>
    </tr>
</template>

<template id="model-relation-tpl">
    <tr>
        <td><input type="text" class="form-control" placeholder="relation name" value=""></td>
        <td>
            <select style="width: 150px">
                <option value="HasOne" selected>HasOne</option>
                <option value="BelongsTo">BelongsTo</option>
                <option value="HasMany">HasMany</option>
                <option value="BelongsToMany">BelongsToMany</option>
            </select>
        </td>
        <td><input type="text" class="form-control" placeholder="related model"></td>
        <td><input type="text" class="form-control" placeholder="default value"></td>
        <td><input type="text" class="form-control" placeholder="default value"></td>
        <td><input type="checkbox" /></td>
        <td><a class="btn btn-sm btn-danger model-relation-remove"><i class="fa fa-trash"></i> remove</a></td>
    </tr>
</template>

<script>

$(function () {
    var $model = $('#inputModelName'),
        $controller = $('#inputControllerName'),
        $repository = $('#inputRepositoryName'),
        $table = $('#inputTableName'),
        $fieldsBody = $('#table-fields tbody'),
        tpl = $('#table-field-tpl').html(),
        modelNamespace = 'App\\Models\\',
        namespaceBase = '{{ str_replace( '\\', '\\\\', $namespaceBase ) }}',
        repositoryNamespace = namespaceBase + '\\Repositories\\',
        controllerNamespace = namespaceBase + '\\Controllers\\',
        dataTypeMap = {!! json_encode($dataTypeMap) !!};
    
    var withSingularName = function (table) {
        $.ajax('{{ url(request()->path()) }}?singular=' + table, {
            success: function (data) {
                writeController(data.value);
                writeModel(data.value);
            }
        });
    };
    $('input[type=checkbox]').iCheck({checkboxClass:'icheckbox_minimal-blue'});
    $('select').select2();

    $('.choose-exist-table').on('change', function () {
        var val = $(this).val(), tb, db;
        if (val == '0') {
            $table.val('');
            getTR().remove();
            return;
        }
        val = val.split('|');
        db = val[0];
        tb = val[1];

        $table.val(tb);

        withSingularName(tb);

        $.ajax({
            url: '{{ admin_url("helpers/scaffold/table") }}',
            method: 'post',
            data: {db: db, tb: tb, _token: LA.token},
            success: function (res) {
                if (!res.list) return;
                var i, list = res.list, $id = $('#inputPrimaryKey'), updated, created, soft;

                getTR().remove();
                for (i in list) {
                    if (list[i].id) {
                        $id.val(i);
                        continue;
                    }
                    if (i == 'updated_at') {
                        updated = list[i];
                        continue;
                    }
                    if (i == 'created_at') {
                        created = list[i];
                        continue;
                    }
                    if (i == 'deleted_at') {
                        soft = list[i];
                        continue;
                    }

                    var c = replace(list[i].comment, '"', '');
                    addField({
                        name: i,
                        lang: c,
                        type: list[i].type,
                        default: replace(list[i].default, '"', ''),
                        comment: c,
                        nullable: list[i].nullable != 'NO',
                    });
                }

                addTimestamps(updated, created);
                addSoftdelete(soft);
            }
        });

    });

    function getTR() {
        return $('#table-fields tbody tr');
    }

    function writeController(val) {
        val = ucfirst(toHump(toLine(val)));
        $controller.val(val ? (controllerNamespace + val + 'Controller') : controllerNamespace);
    }
    function writeModel(val) {
        $model.val(modelNamespace + ucfirst(ucfirst(toHump(toLine(val)))));
    }

    function addTimestamps(updated, created) {
        if (updated && created) {
            return;
        }
        $('[name="timestamps"]').prop('checked', false);

        var c;
        if (updated) {
            c = replace(updated.comment, '"', '');
            addField({
                name: 'updated_at',
                lang: c,
                type: updated.type,
                default: replace(updated.default, '"', ''),
                comment: c,
                nullable: updated.nullable != 'NO',
            });
        }
        if (created) {
            c = replace(created.comment, '"', '');
            addField({
                name: 'created_at',
                lang: c,
                type: created.type,
                default: replace(created.default, '"', ''),
                comment: c,
                nullable: created.nullable != 'NO',
            });
        }
    }

    function addSoftdelete(soft) {
        if (soft) {
            $('[name="soft_deletes"]').prop('checked', true);
        }
    }

    function replace(str, search, replace) {
        if (typeof str !== 'string') {
            return str;
        }
        return str.replace(search, replace);
    }

    function addField(val) {
        val = val || {};

        var idx = getTR().length,
            $field = $(
                tpl
                    .replace(/__index__/g, idx)
                    .replace(/{name}/g, val.name || '')
                    .replace(/{translation}/g, val.lang || '')
                    .replace(/{default}/g, val.default || '')
                    .replace(/{comment}/g, val.comment || '')
                    .replace(/{nullable}/g, val.nullable ? 'checked' : '')
            ),
            i;

        $fieldsBody.append($field);
        $('select').select2();

        // 选中字段类型
        for (i in dataTypeMap) {
            if (val.type == i) {
                $field.find('[name="fields['+ idx +'][type]"]')
                    .val(dataTypeMap[i])
                    .trigger("change");
            }
        }

    }

    $('#add-table-field').click(function (event) {
        $('#table-fields tbody').append($('#table-field-tpl').html().replace(/__index__/g, $('#table-fields tr').length - 1));
        $('select').select2();
        $('input[type=checkbox]').iCheck({checkboxClass:'icheckbox_minimal-blue'});
    });

    $('#table-fields').on('click', '.table-field-remove', function(event) {
        $(event.target).closest('tr').remove();
    });

    $('#add-model-relation').click(function (event) {
        $('#model-relations tbody').append($('#model-relation-tpl').html().replace(/__index__/g, $('#model-relations tr').length - 1));
        $('select').select2();
        $('input[type=checkbox]').iCheck({checkboxClass:'icheckbox_minimal-blue'});

        relation_count++;
    });

    $('#model-relations').on('click', '.model-relation-remove', function(event) {
        $(event.target).closest('tr').remove();
    });

    // 下划线转换驼峰
    function toHump(name) {
        return name.replace(/\_(\w)/g, function (all, letter) {
            return letter.toUpperCase();
        });
    }

    // 驼峰转换下划线
    function toLine(name) {
        return name.replace(/([A-Z])/g,"_$1").toLowerCase();
    }

    function ucfirst(str) {
        var reg = /\b(\w)|\s(\w)/g;
        return str.replace(reg,function(m){
            return m.toUpperCase()
        });
    }

    $('#scaffold').on('submit', function (event) {

        //event.preventDefault();

        if ($('#inputTableName').val() == '') {
            $('#inputTableName').closest('.form-group').addClass('has-error');
            $('#table-name-help').removeClass('hide');

            return false;
        }

        return true;
    });
});

</script>