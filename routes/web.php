<?php

use App\Http\Controllers\BureauController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScaleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


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

Route::get('/test', function () {
    return response('test route', 200, ['header' => 'test route']);
});
Route::get('/', function () {
    return response('首頁(衛教資訊 最新消息...)', 200);
});

//login & logout & register
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']);
Route::post('/register', [UserController::class, 'register']);
//self-profile
Route::get('/getProfile', [UserController::class, 'getProfile'])->middleware('hasroles:cheif_admin');
Route::put('/updateProfile', [UserController::class, 'updateProfile']);
//questionnaire
Route::get('/getQuestionnaire', [QuestionnaireController::class, 'index']); //admin(目前沒有用)
Route::post('/saveQuestionnaire', [QuestionnaireController::class, 'save']);    //保存
Route::get('/getQuestionnaireAnwser', [QuestionnaireController::class, 'getQuestionnaireAnwser']);  //取得問卷+答案
//scale
Route::get('/getScale/{id?}', [ScaleController::class, 'index']);   //取得量表問題
Route::post('/getScale/{id?}/save', [ScaleController::class, 'save']);  //送出量表
Route::get('/getScaleAnwsers', [ScaleController::class, 'getScaleAnwsers']);    //取得個人量表紀錄
Route::get('/getAllScaleAnswer/{id?}', [ScaleController::class, 'getAllScaleAnswer']);  //admin
Route::get('/getUserScaleAnswer/user/{id?}', [ScaleController::class, 'getUserScaleAnswer']);  //admin

//admin(衛生局)
Route::resource('/userProfile', UserController::class)->except(['create', 'edit'])->middleware('hasroles:cheif_admin,bureau_admin,director_admin');    //使用者檔案
// Route::resource('/userProfile', UserController::class)->except(['create', 'edit']);    //使用者檔案
Route::delete('/userProfile/{id}/disable', [UserController::class, 'disable']); //禁用使用者
Route::post('/userProfile/{id}/recovery', [UserController::class, 'recovery']); //恢復使用者
Route::get('searchUser', [UserController::class, 'search']); //搜尋使用者

Route::resource('/news', NewsController::class)->except(['create', 'edit']);  //最新消息
Route::get('/getAllNews', [NewsController::class, 'getAllNews']);   //取得所有消息
Route::delete('/news/{id}/invisible', [NewsController::class, 'invisible']);    //隱藏消息
Route::post('/news/{id}/recovery', [NewsController::class, 'recovery']);    //文章可見

Route::get('/getBureaus', [BureauController::class, 'getBureaus']);   //取得所有單位
Route::get('/getRoles', [RoleController::class, 'getRoles']);   //取得所有角色
//衛生局可調動衛生所的人
//衛生所可調動社工
