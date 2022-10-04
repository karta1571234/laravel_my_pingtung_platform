<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getAllRoles()
    {
        try {
            $roles = Role::all();
            return response()->json(['status' => 200, 'message' => '取得所有角色成功', 'result' => $roles, 'success' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '取得所有角色失敗' . $th->getMessage(), 'result' => $roles, 'success' => true], 400);
        }
    }
}
