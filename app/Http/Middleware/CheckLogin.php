<?php

namespace App\Http\Middleware;

use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Illuminate\Support\Facades\Route;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        switch ($request->path()) {
            case '/':
                $this->news = new NewsController();
                $news = $this->news->index();
                if ($news->status() == 200) {
                    return $news;
                } else {
                    return $news;
                }
                break;
            case 'login':
                $this->user = new UserController();
                $response = $this->user->login($request);

                if ($response->status() == 200) {
                    $user = Auth::user();
                    $token = $this->genToken($user->id);
                    $roles = $user->roles;
                    return response()->json(['status' => 200, 'message' => '登入成功', 'result' => $user, 'roles' => $roles, 'token' => $token, 'success' => true], 200);
                } else {
                    return $response;
                }
                break;
            case 'register':
                $this->user = new UserController();
                $response = $this->user->register($request);

                if ($response->status() == 200) {
                    return response()->json(['status' => 200, 'message' => '註冊成功',  'success' => true], 200);
                } else {
                    return $response;
                }
                break;

            default:
                //401=>未登入(認證錯誤)
                //403=>權限不足(授權錯誤)
                try {
                    // return $request->header('token');
                    if ($this->checkToken($request)) {
                        return $next($request);
                    }
                    return response()->json(['status' => 401, 'message' => 'token錯誤', 'success' => false], 401);
                } catch (\Throwable $th) {
                    return response()->json(['status' => 401, 'message' => '目前未再登入狀態' . $th->getMessage(), 'success' => false], 401);
                }
                break;
        }
    }
    private function genToken($id)
    {
        $secret_key = "YOUR_SECRET_KEY";
        $issuer_claim = "http://localhost/laravel/my_pingtung_platform";
        $audience_claim = "http://localhost/laravel/my_pingtung_platform";
        $issuedat_claim = time(); // issued at
        $expire_claim = $issuedat_claim + 3600;
        $payload = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $id,
            )
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return $jwt;
    }
    public function checkToken($request)
    {
        $token = $request->header('token');
        $secret_key = "YOUR_SECRET_KEY";
        try {
            JWT::decode($token, new Key($secret_key, 'HS256'));
            return true;
        } catch (Exception $e) {
            return false;
            // return response()->json(['status' => 401, 'message' => 'token錯誤=>' . $e->getMessage(), 'success' => false], 401);
            //不會停在這
        }
    }
    public function decodeToken($token)
    {
        $secret_key = "YOUR_SECRET_KEY";
        try {
            $payload = JWT::decode($token, new Key($secret_key, 'HS256'));
            $id = $payload->data->id;
            return $id;
        } catch (Exception $e) {
            return response()->json(['status' => 401, 'message' => 'token錯誤=>' . $e->getMessage(), 'success' => false], 401);
        }
    }
}
