<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$canRoles)
    {
        $this->CR = new CheckLogin();
        $token = $request->header('token');
        $id = $this->CR->decodeToken($token);
        $user = User::find($id);
        $roles = $user->roles;

        $arr_roles = [];
        foreach ($roles as $role) {
            array_push($arr_roles, $role->name);
        }
        $role_intersect = array_intersect($arr_roles, $canRoles);
        if (count($role_intersect) > 0) {
            return $next($request);
        } else {
            return response()->json(['status' => 403, 'message' => '權限不足', 'result' => [], 'success' => false], 403);
        }
    }
}
