<?php

use App\Http\Controllers\BureauController;
use App\Http\Controllers\CheckController;
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
    return response('首頁(衛教資訊 最新消息...)(這裡測試區要首頁請加上/news)', 200);
});

//login & logout & register
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']);
Route::post('/register', [UserController::class, 'register']);
//self-profile
Route::get('/getProfile', [UserController::class, 'getProfile']);
Route::put('/updateProfile', [UserController::class, 'updateProfile']);
Route::put('/updatePassword', [UserController::class, 'updatePassword']);
//questionnaire
Route::get('/getQuestionnaire', [QuestionnaireController::class, 'index']); //admin(目前沒有用)
Route::post('/saveQuestionnaire', [QuestionnaireController::class, 'save']);    //保存 (for 社工)
Route::get('/getQuestionnaireAnwser', [QuestionnaireController::class, 'getQuestionnaireAnwser']);  //取得問卷+答案
Route::get('/getQuestionnaireAnwser/user/{older_id}', [QuestionnaireController::class, 'getUserQuestionnarireAnswers']);  //取得問卷+答案(for 除了長者以外的)
//scale(for長者)
Route::get('/getScaleAnwsers/{ans_id?}', [ScaleController::class, 'getScaleAnwsers']);    //取得個人量表紀錄(可送入ans_id查看詳細答案)
//scale(for社工)
Route::get('/getScale/{id?}', [ScaleController::class, 'index']);   //取得量表(可送入id查看詳細題目)
Route::post('/getScale/{id?}/save', [ScaleController::class, 'save']);  //送出量表  (由社工這邊幫長者填寫older_id須放在body送出)
//scale(for衛生們)
Route::get('/getAllScaleAnswer/{id?}', [ScaleController::class, 'getAllScaleAnswer']);  //admin 取得所有量表紀錄(可送入id去查指定量表)
Route::get('/getUserScaleAnswers/user/{id?}', [ScaleController::class, 'getUserScaleAnswers']);  //admin 取得長者所有量表紀錄(送入id去查指定長者)
Route::get('/getUserScaleAnswers/user/{id?}/scaleAns/{ans_id?}', [ScaleController::class, 'getUserScaleAnswers']);  //admin 取得長者所有量表紀錄(送入id去查指定長者)

Route::get('/getUserScaleAnswers/u/{id?}/scaleAns/{ans_id?}', [ScaleController::class, 'getUserScaleAnswers']);  //臨時(跟上面的一樣)

//admin(衛生局/衛生所)
Route::resource('/userProfile', UserController::class)->except(['create', 'edit'])->middleware('hasroles:cheif_admin,bureau_admin,director_admin');    //使用者檔案
Route::delete('/userProfile/{id}/disable', [UserController::class, 'disable']); //禁用使用者
Route::post('/userProfile/{id}/recovery', [UserController::class, 'recovery']); //恢復使用者
Route::get('searchUser', [UserController::class, 'search']); //搜尋使用者

Route::resource('/news', NewsController::class)->except(['create', 'edit']);  //最新消息(middleware設定:除了index可以讓user存取以外其他都要擋住。 另外/news可以拿首頁(/)取代了，user可以不用getNews了)
Route::get('/getAllNews', [NewsController::class, 'getAllNews']);   //取得所有消息
Route::delete('/news/{id}/invisible', [NewsController::class, 'invisible']);    //隱藏消息
Route::post('/news/{id}/recovery', [NewsController::class, 'recovery']);    //文章可見

Route::get('/getAllBureaus', [BureauController::class, 'getAllBureaus']);   //取得所有單位(新增角色是要賦予單位)
Route::get('/getAllRoles', [RoleController::class, 'getAllRoles']);   //取得所有角色(新增角色是要賦予權限)
//衛生局可調動(編輯)衛生所的人
//衛生所可調動(編輯)社工

//衛生局可分配社工至長者
//衛生所可分配所內社工至長者
//可從社工分配、也可從長者分配

//The controller method may be used to prefix each action in the group with a given controller.
Route::controller(UserController::class)->group(function () {
    //The prefix method may be used to prefix each route in the group with a given URI.
    Route::prefix('/socialworker_older/getSocialworkers')->group(function () {
        //從社工去分配長者
        Route::get('/available', 'getAvailableSocialworkers');                  //1.找出要配置的社工
        Route::get('/{id}/olders', 'getOldersOnSocialworker');                  //2.找出要(可)被管理的長者  (須把社工id帶入才知有哪些長者)
        Route::post('/{id}/addOlder', 'addOlderToSocialworker');                //3.將長者存進社工(social_worker_id)
        Route::get('/{id}/manage/olders', 'getOldersWithSocialworker');         //4.取得社工管理的長者
        Route::delete('/{id}/delOlder', 'delOlderFromSocialworker');            //(5.)刪除社工管理的長者
    }); // Matches The "/socialworker_older/getSocialworkers/..." URL

    Route::prefix('/socialworker_older/getOlders')->group(function () {
        //從長者去分配社工
        Route::get('/available', 'getAvailableOlders');                         //1.找出要配置的長者
        Route::get('/{id}/socialworkers', 'getSocialworkersOnOlder');           //2.找出要(可)管理的社工  (須把長者id帶入才知有哪些社工)
        Route::post('/{id}/addSocialworker', 'addSocialworkerToOlder');         //3.將社工存進長者(social_worker_id)
        Route::get('/{id}/manage/socialworker', 'getSocialworkerWithOlder');    //4.取得管理長者的社工
        Route::delete('/{id}/delSocialworker', 'delSocialworkerFromOlder');     //(5.)刪除管理長者的社工
    });
}); // Matches The "[UserController::class]" controller
//社工
Route::get('/getOlders', [UserController::class, 'getOlders']);

Route::post('/testUploadImg', [NewsController::class, 'uploadImg']);    //測試用上傳路徑

Route::controller(CheckController::class)->group(function () {
    Route::get('/checkPhone', 'checkPhone');
    Route::get('/checkID', 'checkID');
    Route::get('/checkEmail', 'checkEmail');
});
