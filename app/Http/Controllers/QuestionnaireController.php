<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckLogin;
use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function __construct()
    {
        $this->CL = new CheckLogin();
    }
    public function index()
    {
        try {
            $questionnaires = Questionnaire::get();
            if (count($questionnaires) >= 1) {
                $json_decode_questionnaires = array();
                // #將DB的option欄位解json後重包
                foreach ($questionnaires as $questionnaire) {
                    $question = $questionnaire->question;
                    $option =  json_decode($questionnaire->option);
                    #配發key-value
                    $json_decode_key_values['question'] = $question;
                    $json_decode_key_values['option'] = $option;
                    #append(push)進陣列裡
                    array_push($json_decode_questionnaires, $json_decode_key_values);
                }
                return response()->json(['status' => 200, 'message' => '查詢問卷成功', 'result' => $json_decode_questionnaires, 'success' => true], 200);
            } else {
                return response()->json(['status' => 202, 'message' => '查詢問卷失敗', 'result' => [], 'success' => false], 202);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage(), 'success' => false], 400);
        }
    }
    public function save(Request $req)
    {
        $answer = $req->input('answer');
        if ($answer != null) {
            try {
                $token = $req->header('token');
                $id = $this->CL->decodeToken($token);
                // $id = 14;
                $user = User::findOrFail($id);
                $questionnaire = $user->questionnaireAns;

                //判斷DB是否有該長者的問卷
                if ($questionnaire != null) {
                    //有的話就編輯
                    $update = $questionnaire->update(['answer' => $answer]);
                    if ($update) {
                        return response()->json(['status' => 200, 'message' => $user->name . '儲存問卷成功', 'success' => true], 200);
                    } else {
                        return response()->json(['status' => 202, 'message' => '儲存問卷失敗', 'success' => false], 202);
                    }
                } else {
                    //沒有的話就新增
                    $QA = QuestionnaireAnswer::create(['answer' => $answer]);
                    $user->questionnaireAns()->save($QA);
                    return response()->json(['status' => 200, 'message' => '新增問卷成功', 'success' => true], 200);
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => 202, 'message' => '儲存問卷失敗=>' . $th->getMessage(), 'success' => false], 202);
            }
        } else {
            return response()->json(['status' => 400, 'message' => '儲存問卷失敗=>資料欄位不符合要求', 'success' => false], 400);
        }
    }
    public function getQuestionnaireAnwser(Request $request)
    {
        try {
            $token = $request->header('token');
            // $id = $this->CL->decodeToken($token);
            $id = 12;
            $user = User::findOrFail($id);
            $questionnaire = $user->questionnaireAns;

            //判斷DB是否有該長者的問卷
            if ($questionnaire != null) {
                //有的話就回傳問卷+答案
                $answers = json_decode($questionnaire->answer);
                $arr_ans = array();
                //把答案放進陣列裡
                //總共22題,先補長(null)
                $arr_pad_ans = array_pad($arr_ans, 22, null);
                foreach ($answers as $key => $option) {
                    $arr_pad_ans[$key - 1] = $option;
                }

                $questionnaires = Questionnaire::get();
                if ($questionnaires != null) {
                    $json_decode_questionnaires = array();
                    // #將DB的option欄位解json後重包
                    foreach ($questionnaires as $idx => $questionnaire) {
                        $question = $questionnaire->question;
                        $option =  json_decode($questionnaire->option);
                        #配發key-value
                        $json_decode_key_values['question'] = $question;
                        $json_decode_key_values['option'] = $option;
                        $json_decode_key_values['answer'] = $arr_pad_ans[$idx];
                        #append(push)進陣列裡
                        array_push($json_decode_questionnaires, $json_decode_key_values);
                    }
                    return response()->json(['status' => 200, 'message' => '取得問卷+答案成功', 'result' => $json_decode_questionnaires, 'success' => true], 200);
                }
                return response()->json(['status' => 202, 'message' => '查詢問卷失敗', 'result' => [], 'success' => true], 202);
            } else {
                //沒有的話就回傳問卷
                return $this->index();
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '取得問卷+答案失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
}
