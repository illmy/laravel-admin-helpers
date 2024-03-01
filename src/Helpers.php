<?php

namespace Encore\Admin\Helpers;

use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Extension;

class Helpers extends Extension
{
    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        if (env('APP_ENV') != 'dev') {
            return;
        }
        static::registerRoutes();

        Admin::extend('helpers', __CLASS__);
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    public static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('helpers/scaffold', 'Encore\Admin\Helpers\Controllers\ScaffoldController@index');
            $router->post('helpers/scaffold/table', 'Encore\Admin\Helpers\Controllers\ScaffoldController@table');
            $router->post('helpers/scaffold', 'Encore\Admin\Helpers\Controllers\ScaffoldController@store');
        });
    }

    public static function import()
    {
        $lastOrder = Menu::max('order') ?: 0;

        $root = [
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'Helpers',
            'icon'      => 'fa-gears',
            'uri'       => '',
        ];

        $root = Menu::create($root);

        $menus = [
            [
                'title'     => 'Scaffold',
                'icon'      => 'fa-keyboard-o',
                'uri'       => 'helpers/scaffold',
            ],
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $root->id;
            $menu['order'] = $lastOrder++;

            Menu::create($menu);
        }

        parent::createPermission('Admin helpers', 'ext.helpers', 'helpers/*');
    }
}
