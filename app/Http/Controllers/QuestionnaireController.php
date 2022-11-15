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
        //admin(目前沒有用)
        try {
            $questionnaires = Questionnaire::get();
            if (count($questionnaires) >= 1) {
                $json_decode_questionnaires = array();
                // #將DB的option欄位解json後重包
                foreach ($questionnaires as $questionnaire) {
                    $question = $questionnaire->question;
                    $option =  json_decode($questionnaire->option);
                    $input_type = $questionnaire->input_type;
                    $tips = $questionnaire->tips;
                    #配發key-value
                    $json_decode_key_values['question'] = $question;
                    $json_decode_key_values['option'] = $option;
                    $json_decode_key_values['input_type'] = $input_type;
                    $json_decode_key_values['tips'] = $tips;
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
                $social_worker_id = $this->CL->decodeToken($token);
                $user_social_worker = User::findOrFail($social_worker_id);
                //長者ID
                $older_id = $req->validate(['older_id' => 'int|required'])['older_id'];
                $user_older = User::findOrFail($older_id);
                //判斷社工與長者是否在同局
                if ($user_social_worker->bureau_id == $user_older->bureau_id) {
                    $questionnaire = $user_older->questionnaireAns;

                    //判斷DB是否有該長者的問卷
                    if ($questionnaire != null) {
                        //有的話就編輯
                        $update = $questionnaire->update(['answer' => $answer]);
                        if ($update) {
                            return response()->json(['status' => 200, 'message' => $user_older->name . '儲存問卷成功', 'success' => true], 200);
                        } else {
                            return response()->json(['status' => 202, 'message' => '儲存問卷失敗', 'success' => false], 202);
                        }
                    } else {
                        //沒有的話就新增
                        $QA = QuestionnaireAnswer::create(['answer' => $answer, 'social_worker_id' => $social_worker_id]);
                        $user_older->questionnaireAns()->save($QA);
                        return response()->json(['status' => 200, 'message' => '新增問卷成功', 'success' => true], 200);
                    }
                } else {
                    return response()->json(['status' => 400, 'message' => '長者與社工在不同單位', 'success' => false], 400);
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => 202, 'message' => '儲存問卷失敗=>' . $th->getMessage(), 'success' => false], 202);
            }
        } else {
            return response()->json(['status' => 400, 'message' => '儲存問卷失敗=>資料欄位不符合要求', 'success' => false], 400);
        }
    }
    public function getQuestionnaireAnwser(Request $request, $older_id = null)
    {
        try {
            if ($older_id == null) {
                $token = $request->header('token');
                $id = $this->CL->decodeToken($token);
                $user = User::findOrFail($id);
            } else {
                $user = User::withTrashed()->find($older_id);
            }

            $questionnaire = $user->questionnaireAns;

            //判斷DB是否有該長者的問卷
            if ($questionnaire != null) {
                //有的話就回傳問卷+答案
                $answers = json_decode($questionnaire->answer);
                $arr_ans = array();
                //把答案放進陣列裡
                //總共28題,先補長(null)
                $arr_pad_ans = array_pad($arr_ans, 28, null);
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
                        $input_type = $questionnaire->input_type;
                        $tips = $questionnaire->tips;
                        #配發key-value
                        $json_decode_key_values['question'] = $question;
                        $json_decode_key_values['option'] = $option;
                        $json_decode_key_values['input_type'] = $input_type;
                        $json_decode_key_values['tips'] = $tips;
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
    public function getUserQuestionnarireAnswers(Request $request, $older_id = null)
    {
        if ($older_id != null) {
            try {
                $arr_roles = $this->getRoles($request);
                if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                    $QA = $this->getQuestionnaireAnwser($request, $older_id)->getData()->result;
                    if ($QA != null) {
                        return response()->json(['status' => 200, 'message' => '查詢特定使用者(' . User::withTrashed()->find($older_id)->name . ')問卷紀錄成功', 'result' => $QA, 'success' => true], 200);
                    }
                    return response()->json(['status' => 202, 'message' => '查詢特定使用者問卷紀錄失敗=>目前' . User::withTrashed()->find($older_id)->name . '還沒有紀錄', 'result' => [], 'success' => 'true'], 202);
                } else if (in_array('director_admin', $arr_roles)) {
                    $token = $request->header('token');
                    $director_id = $this->CL->decodeToken($token);
                    $bureau = User::findOrFail($director_id)->bureau;
                    $user = $bureau->users->find($older_id);

                    if ($user != null) {
                        $QA = $this->getQuestionnaireAnwser($request, $older_id)->getData()->result;
                        if ($QA != null) {
                            return response()->json(['status' => 200, 'message' => '查詢特定使用者(' . User::withTrashed()->find($older_id)->name . ')問卷紀錄成功', 'result' => $QA, 'success' => true], 200);
                        }
                        return response()->json(['status' => 202, 'message' => '查詢特定使用者問卷紀錄失敗=>目前' . User::withTrashed()->find($older_id)->name . '還沒有紀錄', 'result' => [], 'success' => 'true'], 202);
                    }
                    return response()->json(['status' => 400, 'message' => '查詢特定使用者問卷紀錄失敗=>' . $bureau->name . '裡沒有這個人', 'result' => [], 'success' => false], 400);
                } else if (in_array('director_user', $arr_roles)) {
                    $token = $request->header('token');
                    $social_worker_id = $this->CL->decodeToken($token);
                    $older = User::withTrashed()->findOrFail($older_id);

                    if ($older->social_worker_id == $social_worker_id) {
                        $QA = $this->getQuestionnaireAnwser($request, $older_id)->getData()->result;
                        if ($QA != null) {
                            return response()->json(['status' => 200, 'message' => '查詢特定使用者(' . $older->name . ')問卷紀錄成功', 'result' => $QA, 'success' => true], 200);
                        }
                        return response()->json(['status' => 202, 'message' => '查詢特定使用者問卷紀錄失敗=>目前' . $older->name . '還沒有紀錄', 'result' => [], 'success' => true], 202);
                    }
                    return response()->json(['status' => 400, 'message' => '查詢特定使用者問卷紀錄失敗=>沒有這個人', 'result' => [], 'success' => false], 400);
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return response()->json(['status' => 400, 'message' => '未填入要找的user id', 'result' => [], 'success' => 'false'], 400);
    }
    //function
    protected function getRoles(Request $request)
    {
        $token = $request->header('token');
        $id = $this->CL->decodeToken($token);
        $user = User::find($id);
        $roles = $user->roles;
        $arr_roles = [];
        foreach ($roles as $r) {
            array_push($arr_roles, $r->name);
        }
        return $arr_roles;
    }
}
