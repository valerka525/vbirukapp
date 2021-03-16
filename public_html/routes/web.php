<?php

use Illuminate\Support\Facades\Route;
use function Osiset\ShopifyApp\getShopifyConfig;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => getShopifyConfig('prefix'), 'middleware' => ['itp', 'web']], function () {

    Route::get('/','App\HomeController@index')
            ->middleware(['auth.shopify', 'billable'])
            ->name(getShopifyConfig('route_names.home'));

    /*
     * JWT Token Check for some ajax action via auth.token middleware
     *
    Route::post('/some-path', 'App\SomeAjaxControllerController@update')
        ->middleware(['auth.token', 'billable']);
    */
});

Route::group(['prefix' => 'script-tag', 'middleware' => ['web']], function () {
    Route::get('/test', 'Script\TestController@index');
});
