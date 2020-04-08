<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('excelBatchAdd', function () {
        return view('excelfile');
    });
    $router->post('excelBatchAddFile', 'KeywordController@excelFileBatchAdd');
    $router->resource('keyword_list', 'KeywordController');

});
