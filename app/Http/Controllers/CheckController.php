<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckController extends Controller
{
    public function checkPhone(Request $request)
    {
        $input = $request->all();
        $rules = ['phone' => ['required', 'size:10', 'string', 'regex:/^09[0-9]{8}$/', Rule::unique('users', 'phone')]];
        // $rules = ['phone' => ['required', 'size:10', 'string', 'regex:/^0[0-9]{1}-[0-9]{7}$/', Rule::unique('users', 'phone')]];
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => '驗證失敗', 'result' => $validator->errors()->first('phone')], 400);
        } else {
            return response()->json(['status' => 200, 'message' => '驗證成功'], 200);
        }
    }
    public function checkID(Request $request)
    {
        $input = $request->all();
        $rules = ['ID_num' => ['required', 'size:10', 'string', 'regex:/^[A-Z]{1}[1-2]{1}[0-9]{8}$/', Rule::unique('users', 'ID_num')]];
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => '驗證失敗', 'result' => $validator->errors()->first('ID_num')], 400);
        } else {
            return response()->json(['status' => 200, 'message' => '驗證成功'], 200);
        }
    }
    public function checkEmail(Request $request)
    {
        $input = $request->all();
        $rules = ['email' => ['required', 'email', 'string', Rule::unique('users', 'email')]];
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => '驗證失敗', 'result' => $validator->errors()->first('email')], 400);
        } else {
            return response()->json(['status' => 200, 'message' => '驗證成功'], 200);
        }
    }
}
