<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckLogin;
use App\Models\Bureau;
use App\Models\ScaleAnswer;
use App\Models\ScaleOrder;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScaleController extends Controller
{
    public function __construct()
    {
        $this->CL = new CheckLogin();
        $this->scalemodel = new ScaleAnswer();
    }
    public function index($id = null)
    {
        try {
            if ($id == null) {
                $scale_orders = ScaleOrder::all();
                if (count($scale_orders) >= 1) {
                    return response()->json(['status' => 200, 'message' => '查詢' . ScaleOrder::count() . '個量表主表成功', 'result' => $scale_orders, 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '查詢量表主表失敗', 'result' => [], 'success' => false], 202);
                }
            } else {
                $scale_order = ScaleOrder::find($id);
                $scaledetails = $scale_order->scaledetails;
                if ($scaledetails != null) {
                    $json_decode_scaledetails = array();
                    // #將DB的option欄位解json後重包
                    foreach ($scaledetails as $scaledetail) {
                        $question = $scaledetail->question;
                        $option =  json_decode($scaledetail->option);
                        $input_type = $scaledetail->input_type;
                        $tips = $scaledetail->tips;
                        #配發key-value
                        $json_decode_key_values['question'] = $question;
                        $json_decode_key_values['option'] = $option;
                        $json_decode_key_values['input_type'] = $input_type;
                        $json_decode_key_values['tips'] = $tips;
                        #append(push)進陣列裡
                        array_push($json_decode_scaledetails, $json_decode_key_values);
                    }
                    return  response()->json(['status' => 200, 'message' => '查詢<' . $scale_order->name . '>量表細項成功', 'result' => $json_decode_scaledetails, 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '查詢量表細項失敗', 'result' => [], 'success' => false], 202);
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '取得量表失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function save(Request $request, $scale_order_id)
    {
        $scale_order = ScaleOrder::find($scale_order_id);
        if ($scale_order != null) {
            try {
                $answer = $request->input('answer');
                //可以判斷傳入json長度
                // $temp = json_decode($answer, true);
                // return sizeof($temp);
                if ($answer != null) {
                    $token = $request->header('token');
                    $social_worker_id = $this->CL->decodeToken($token);
                    $user_social_worker = User::findOrFail($social_worker_id);
                    //社工ID
                    $older_id = $request->validate(['older_id' => 'int'])['older_id'];
                    $user_older = User::findOrFail($older_id);
                    if ($user_older->bureau_id == $user_social_worker->bureau_id) {
                        $SA = ScaleAnswer::create(['answer' => $answer, 'scale_order_id' => $scale_order->id, 'social_worker_id' => $social_worker_id]);
                        $user_older->scaleAns()->save($SA);
                        // belongsto 怎麼 save
                        // $scale_order->scaledetails()->save($SA);
                        return response()->json(['status' => 200, 'message' => $user_older->name . '提交量表' . $scale_order->name . '成功', 'success' => true], 200);
                    }
                    return response()->json(['status' => 400, 'message' => '長者與社工在不同單位', 'success' => false], 400);
                } else {
                    return response()->json(['status' => 400, 'message' => '儲存量表失敗=>資料欄位不符合要求', 'success' => false], 400);
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => 202, 'message' => '儲存量表失敗=>' . $th->getMessage(), 'success' => false], 202);
            }
        } else {
            return response()->json(['status' => 400, 'message' => '儲存量表失敗=>沒有這個量表', 'success' => false], 400);
        }
    }
    public function getScaleAnwsers(Request $request, $ans_id = null, $user_id = null)
    {
        if ($user_id == null) {
            $token = $request->header('token');
            $user_id = $this->CL->decodeToken($token);
            //test
            // $user_id = 22;
            $user = User::findOrFail($user_id);
        } else {
            $user = User::findOrFail($user_id);
        }
        $scaleanwsers = $user->scaleAns;
        // return $scaleanwsers;

        if (count($scaleanwsers) >= 1) {
            if ($ans_id == null) {
                try {
                    $scaleanwsers = $this->wrapScaleAns($scaleanwsers);
                    return response()->json(['status' => 200, 'message' => '取得' . $user->name . '量表紀錄', 'result' => $scaleanwsers, 'success' => true], 200);
                } catch (Exception $e) {
                    return response()->json(['status' => 400, 'message' => '取得量表紀錄失敗=>' . $e->getMessage(), 'result' => [], 'success' => false], 400);
                }
            } else {
                try {
                    $scaleanwser = $scaleanwsers->find($ans_id);
                    $scaledetails = ScaleOrder::findOrFail($scaleanwser->scale_order_id)->scaledetails;
                    $ans = json_decode($scaleanwser->answer, true);
                    //重新建一個答案陣列
                    $arr_ans = array();
                    foreach ($ans as $key => $value) {
                        $arr_ans[$key - 1] = $value;
                    }

                    foreach ($scaledetails as $idx => $item) {
                        $option = json_decode($item->option);
                        $item['option'] = $option;
                        $item['ans'] = $arr_ans[$idx];
                    }
                    return response()->json(['status' => 200, 'message' => '取得' . $user->name . '單一量表紀錄', 'result' => $scaledetails, 'success' => true], 200);
                } catch (Exception $e) {
                    return response()->json(['status' => 400, 'message' => '取得單一量表紀錄失敗=>' . $e->getMessage(), 'result' => [], 'success' => false], 400);
                }
            }
        } else {
            return response()->json(['status' => 200, 'message' =>  $user->name . '還沒有填寫過的量表', 'result' => [], 'success' => true], 200);
        }
    }
    protected function wrapScaleAns($scaleanwsers)
    {
        foreach ($scaleanwsers as $item) {
            unset($item['answer']);
            $item['scale_order_name'] = $item->scaleorder->name;
            $item['social_worker'] = User::find($item->social_worker_id)->name;
            unset($item['scaleorder']);
            unset($item['social_worker_id']);
        }
        return $scaleanwsers;
    }
    //admin
    public function getAllScaleAnswer(Request $request, $id = null)
    {
        try {
            $arr_roles = $this->getRoles($request);
            if ($id == null) {
                if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                    $AllScaleAnswer = ScaleAnswer::get();
                    if (count($AllScaleAnswer) >= 1) {
                        //查全部的人的量表
                        $AllScaleAnswer = $this->wrapScaleAns($AllScaleAnswer);
                        foreach ($AllScaleAnswer as $item) {
                            $item['user_name'] = $item->user->name;
                            unset($item['user']);
                        }
                    } else {
                        return response()->json(['status' => 202, 'message' => '衛生局查詢所有量表紀錄失敗', 'result' => [], 'success' => false], 202);
                    }
                } else if (in_array('director_admin', $arr_roles)) {
                    //查同衛生所下的量表
                    $token = $request->header('token');
                    $id = $this->CL->decodeToken($token);
                    $bureau_id = User::findOrFail($id)->bureau_id;
                    // return $this->scalemodel->getScaleAnswer(0, $bureau_id);

                    //從所長底下找user(也就是說看是哪個衛生所的就找衛生所底下的user)
                    $bureau = Bureau::find($bureau_id);
                    $users = $bureau->users;
                    if (count($users) >= 1) {
                        $AllScaleAnswer = array();
                        foreach ($users as $user) {
                            $ScaleAnswer = $this->wrapScaleAns($user->scaleAns);
                            if (count($ScaleAnswer) < 1) {
                                continue;
                            }
                            foreach ($ScaleAnswer as $item) {
                                $item['user_name'] = $item->user->name;
                                unset($item['user']);
                                array_push($AllScaleAnswer, $item);
                            }
                        }
                    } else {
                        return response()->json(['status' => 202, 'message' => '衛生所查詢所有量表紀錄失敗', 'result' => [], 'success' => false], 202);
                    }
                }
                return response()->json(['status' => 200, 'message' => '查詢所有量表紀錄成功', 'result' => $AllScaleAnswer, 'success' => true], 200);
            } else {
                $scale_name = ScaleOrder::findOrFail($id)->name;
                if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                    $OneScaleAnswer = ScaleAnswer::where('scale_order_id', $id)->get();
                    if (count($OneScaleAnswer) >= 1) {
                        $OneScaleAnswer = $this->wrapScaleAns($OneScaleAnswer);
                        foreach ($OneScaleAnswer as $item) {
                            $item['user_name'] = $item->user->name;
                            unset($item['user']);
                        }
                    } else {
                        return response()->json(['status' => 202, 'message' => '查詢單一量表紀錄失敗', 'result' => [], 'success' => false], 202);
                    }
                } else if (in_array('director_admin', $arr_roles)) {
                    //!衛生所還無法查"特定"的量表紀錄!(ex:鎖定主量表1)
                    //查同衛生所下的量表
                    $token = $request->header('token');
                    $uid = $this->CL->decodeToken($token);
                    $bureau_id = User::findOrFail($uid)->bureau_id;
                    //從所長底下找user(也就是說看是哪個衛生所的就找衛生所底下的user)
                    $bureau = Bureau::find($bureau_id);
                    $users = $bureau->users;
                    if (count($users) >= 1) {
                        $OneScaleAnswer = array();
                        foreach ($users as $user) {
                            $ScaleAnswer = $this->wrapScaleAns($user->scaleAns)->where('scale_order_id', $id)->flatten();

                            if (count($ScaleAnswer) < 1) {
                                continue;
                            }
                            foreach ($ScaleAnswer as $item) {
                                $item['user_name'] = $item->user->name;
                                unset($item['user']);
                            }
                            array_push($OneScaleAnswer, $ScaleAnswer);
                        }
                    } else {
                        return response()->json(['status' => 202, 'message' => '查詢單一' . $scale_name . '量表紀錄失敗', 'result' => [], 'success' => false], 202);
                    }
                }
                return response()->json(['status' => 200, 'message' => '查詢單一' . $scale_name . '量表成功', 'result' => $OneScaleAnswer, 'success' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢量表紀錄失敗=>' . $th->getMessage(), 'result' => [], 'success' => false], 400);
        }
    }
    public function getUserScaleAnswers(Request $request, $id = null, $ans_id = null)
    {
        try {
            $arr_roles = $this->getRoles($request);
            if ($ans_id == null) {
                if ($id != null) {
                    if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                        $response = $this->getScaleAnwsers($request, null, $id)->getData()->result;
                        if ($response != null) {
                            return response()->json(['status' => 200, 'message' => '查詢特定使用者(' . User::findOrFail($id)->name . ')量表紀錄成功', 'result' => $response, 'success' => true], 200);
                        }
                        return response()->json(['status' => 202, 'message' => '查詢特定使用者量表紀錄失敗=>目前' . User::findOrFail($id)->name . '還沒有紀錄', 'result' => [], 'success' => 'true'], 202);
                    } else {
                        $token = $request->header('token');
                        $uid = $this->CL->decodeToken($token);
                        $bureau = User::findOrFail($uid)->bureau;
                        $user = $bureau->users->find($id);

                        if ($user != null) {
                            $response = $this->getScaleAnwsers($request, null, $id)->getData()->result;
                            if ($response != null) {
                                return response()->json(['status' => 200, 'message' => '查詢特定使用者(' . $user->name . ')量表紀錄成功', 'result' => $response, 'success' => true], 200);
                            }
                            return response()->json(['status' => 202, 'message' => '查詢特定使用者量表紀錄失敗=>目前' . $user->name . '還沒有紀錄', 'result' => [], 'success' => true], 202);
                        }
                        return response()->json(['status' => 400, 'message' => '查詢特定使用者量表紀錄失敗=>' . $bureau->name . '裡沒有這個人', 'result' => [], 'success' => false], 400);
                    }
                }
                return response()->json(['status' => 400, 'message' => '未填入要找的user id', 'result' => [], 'success' => 'false'], 400);
            } else {
                if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                    $response = $this->getScaleAnwsers($request, $ans_id, $id)->getData()->result;
                    if ($response != null) {
                        return response()->json(['status' => 200, 'message' => '查詢特定使用者(' . User::findOrFail($id)->name . ')量表紀錄+答案成功', 'result' => $response, 'success' => true], 200);
                    }
                    return response()->json(['status' => 202, 'message' => '查詢特定使用者量表紀錄+答案失敗=>目前' . User::findOrFail($id)->name . '還沒有紀錄', 'result' => [], 'success' => 'true'], 202);
                } else {
                    $token = $request->header('token');
                    $uid = $this->CL->decodeToken($token);
                    $bureau = User::findOrFail($uid)->bureau;
                    $user = $bureau->users->find($id);

                    if ($user != null) {
                        $response = $this->getScaleAnwsers($request, null, $id)->getData()->result;
                        if ($response != null) {
                            return response()->json(['status' => 200, 'message' => '查詢特定使用者(' . $user->name . ')量表紀錄成功', 'result' => $response, 'success' => true], 200);
                        }
                        return response()->json(['status' => 202, 'message' => '查詢特定使用者量表紀錄失敗=>目前' . $user->name . '還沒有紀錄', 'result' => [], 'success' => true], 202);
                    }
                    return response()->json(['status' => 400, 'message' => '查詢特定使用者量表紀錄失敗=>' . $bureau->name . '裡沒有這個人', 'result' => [], 'success' => false], 400);
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢特定使用者量表紀錄失敗>' . $th->getMessage(), 'result' => [], 'success' => false], 400);
        }
    }
    protected function getRoles(Request $request)
    {
        //決定bureau_id
        $token = $request->header('token');
        $uid = $this->CL->decodeToken($token);
        $uuser = User::find($uid);
        $roles = $uuser->roles;
        $arr_roles = [];
        foreach ($roles as $role) {
            array_push($arr_roles, $role->name);
        }
        return $arr_roles;
    }
}
