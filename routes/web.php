<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/main', function () {
//     return view('main');
// });

/**
 * 設備状況一覧
 */
//初期処理
Route::get('/main', 'MainController@index');
//グラフ変更処理
Route::get('/changeGraph', 'MainController@index');

/**
 * 管理
 */
//初期処理
Route::get('/setting', 'SettingController@index');
//更新処理
Route::post('/update-setting', 'SettingController@updateSettingData');
//バックアップ処理
Route::get('/get-backup', 'SettingController@getBackupData');

/**
 * 個別監視
 */
//初期処理
 Route::get('/each-graph', 'EachGraphController@index');
