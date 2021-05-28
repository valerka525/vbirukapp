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

Route::group(['prefix' => getShopifyConfig('prefix'), 'middleware' => ['itp', 'web', 'auth.shopify', 'billable']], function () {

    Route::get('/', 'App\ThemesController@home')->name('home');
    Route::get('/theme/{themeId}/{themeName}', 'App\ThemesController@makeBackup')->name('makeBackup');
    Route::get('/delete/{backup}', 'App\ThemesController@themeDelete')->name('themeDelete');
    Route::get('/restore/{backup}', 'App\ThemesController@restoreBackup')->name('restoreBackup');
    Route::post('/scheduler/add', 'App\ThemesController@addSchedule')->name('addSchedule');
    Route::get('/scheduler/delete/{id}', 'App\ThemesController@deleteSchedule')->name('deleteSchedule');


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
