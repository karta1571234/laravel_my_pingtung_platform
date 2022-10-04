<?php

namespace App\Http\Controllers;

use App\Models\Bureau;
use Illuminate\Http\Request;

class BureauController extends Controller
{
    public function getAllBureaus()
    {
        try {
            $bureau = Bureau::get();
            return response()->json(['status' => 200, 'message' => '取得所有單位成功', 'result' => $bureau, 'success' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '取得所有單位失敗' . $th->getMessage(), 'result' => $bureau, 'success' => true], 400);
        }
    }
}
