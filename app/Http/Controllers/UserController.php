<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckLogin;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->CL = new CheckLogin();
        $this->UserModel = new User();
    }
    public function login(Request $request)
    {
        try {
            //輸入規則
            $datas = $request->validate([
                'account' => 'required|string|email',
                'password' => 'required|string|min:8'
            ]);
            //帳號&密碼
            $acc = $datas['account'];
            $pw = $datas['password'];
            $attempt = Auth::attempt([
                'email' => $acc,
                'password' => $pw
            ]);
            // $attempt = Auth::attempt([$datas]);
            if ($attempt) {
                //驗證成功
                return response()->json(['status' => 200, 'message' => '帳密驗證成功', 'success' => true], 200);
            } else {
                return response()->json(['status' => 401, 'message' => '帳密驗證失敗', 'success' => false], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '帳號或密碼不符合輸入規則=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function logout()
    {
        //待完成
        Auth::logout();
        return response('登出成功', 200);
    }
    public function register(Request $request)
    {
        //自己註冊預設長者，且bureau_id預設為0
        //如果是admin新增可選擇role
        //輸入規則
        try {
            $datas = $request->validate([
                'name' => 'required|string|min:3|max:20',
                'email' => ['required', 'string', 'email', Rule::unique('users')], //要唯一
                'password' => 'required',
                'ID_num' => 'required|string', //唯一
                'gender' => 'required|string',  //之後補上正規式(男,女,其他)
                'birth' => 'required|string|date', //日期格式
                'address_1' => 'required|string',
                'address_2' => 'required|string',
                'phone' => ['required', 'string', Rule::unique('users')],   //電話唯一
                'TEL' => 'required|string', //長度目前都還沒設定  //電話唯一
            ]);
            $password = Hash::make($datas['password']);
            $datas['password'] = $password;
            $user = User::create($datas);
            return response()->json(['status' => 200, 'message' => $user->name . '註冊成功',  'success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '註冊失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }
    public function getProfile(Request $request)
    {
        try {
            $token = $request->header('token');
            $id = $this->CL->decodeToken($token);
            //test
            // $id = 22;
            $user = User::findOrfail($id);
            return response()->json(['status' => 200, 'message' => '查詢使用者成功', 'result' => $user, 'success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '查詢使用者失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }
    public function updateProfile(Request $request)
    {
        $token = $request->header('token');
        $id = $this->CL->decodeToken($token);
        //test
        // $id = 22;
        $user = User::find($id);
        if ($user != null) {
            try {
                //輸入規則
                $datas = $request->validate([
                    'name' => 'required|string|min:3|max:20',
                    'email' => ['required', 'string', Rule::unique('users')->ignore($user->id)],   //email唯一，排除自己
                    'gender' => 'required|string',  //之後補上正規式(男,女,其他)
                    'birth' => 'required|string|date', //日期格式
                    'address_1' => 'required|string',
                    'address_2' => 'required|string',
                    'phone' => ['required', 'string', Rule::unique('users')->ignore($user->id)],   //電話唯一，排除自己
                    'TEL' => 'required|string',
                ]);
                $update = $user->update($datas);
                if ($update) {
                    return response()->json(['status' => 200, 'message' => $user->name . '更新成功', 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '更新失敗', 'success' => false], 202);
                }
            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => '資料欄位不符合要求=>' . $e->getMessage()], 400);
            }
        } else {
            return response()->json(['status' => 202, 'message' => '沒有這個人<更新失敗>', 'success' => false], 202);
        }
    }
    //admin userProfile
    public function index(Request $request)
    {
        try {
            $token = $request->header('token');
            $id = $this->CL->decodeToken($token);
            $user = User::find($id);

            $arr_roles = $this->getRoles($request);
            //看看哪個admin能取得哪些User
            if (in_array('cheif_admin', $arr_roles)) {
                return $this->UserModel->index();
            } else if (in_array('bureau_admin', $arr_roles)) {
                return $this->UserModel->index();
            } else if (in_array('director_admin', $arr_roles)) {
                $bureau = $user->bureau_id;
                $users = User::withTrashed()->where('bureau_id', $bureau)->orderBy('id')->get();
                foreach ($users as $user) {
                    $arr_roles = [];
                    foreach ($user->roles as $role) {
                        array_push($arr_roles, $role->name);
                    }
                    $user['role'] = $arr_roles;
                }
                if (count($users) >= 1) {
                    return response()->json(['status' => 200, 'message' => '取得' . $user->bureau->name . '所有使用者成功', 'result' => $users, 'success' => true], 200);
                }
                return response()->json(['status' => 202, 'message' => '取得' . $user->bureau->name . '所有使用者失敗', 'success' => false], 202);
            }
            // else {
            //社工看他底下的長者
            // }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => $th->getMessage(), 'success' => false]);
        }
    }
    public function show(Request $req, $id)
    {
        try {
            $token = $req->header('token');
            $uid = $this->CL->decodeToken($token);
            $user = User::find($uid);

            $arr_roles = $this->getRoles($req);
            //看看哪個admin能取得哪些User
            //主admin&局長可以取得全部的User
            //所長只能取得他底下的
            if (in_array('cheif_admin', $arr_roles)) {
                return $this->UserModel->show($id);
            } else if (in_array('bureau_admin', $arr_roles)) {
                return $this->UserModel->show($id);
            } else if (in_array('director_admin', $arr_roles)) {
                $bureau = $user->bureau_id;
                $profile = User::withTrashed()->where('bureau_id', $bureau)->find($id);
                if ($profile != null) {
                    return response()->json(['status' => 200, 'message' => '取得單一使用者成功', 'result' => $profile, 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '抓不到沒有這個人', 'success' => false], 202);
                }
            }
            // else {
            //社工看他底下的長者
            // }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => $th->getMessage(), 'success' => false]);
        }
    }
    public function store(Request $request)
    {
        //自己註冊預設長者，且bureau_id預設為0
        //如果是admin新增可選擇bureau_id & role
        //如果是director_admin新增不可選擇bureau_id & role_id只有社工(5)和長者(6)可選
        //輸入規則
        try {
            $datas = $request->validate([
                'name' => 'required|string|min:3|max:20',
                'email' => ['required', 'string', 'email', Rule::unique('users')], //要唯一
                'password' => 'required',
                'ID_num' => 'required|string', //唯一
                'gender' => 'required|string',  //之後補上正規式(男,女,其他)
                'birth' => 'required|string|date', //日期格式
                'address_1' => 'required|string',
                'address_2' => 'required|string',
                'phone' => ['required', 'string', Rule::unique('users')],   //電話唯一
                'TEL' => 'required|string', //長度目前都還沒設定  //電話唯一
            ]);
            //密碼加密
            $password = Hash::make($datas['password']);
            $datas['password'] = $password;

            //決定bureau_id
            $arr_roles = $this->getRoles($request);

            //看看哪個admin能決定bureau_id
            if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                $datas['bureau_id'] = $request->validate(['bureau_id' => 'int'])['bureau_id'];
                $datas['role_id'] = $request->validate(['role_id' => 'int'])['role_id'];
            } else if (in_array('director_admin', $arr_roles)) {
                $token = $request->header('token');
                $uid = $this->CL->decodeToken($token);
                $user = User::find($uid);

                $datas['bureau_id'] = $user->bureau_id;
                $datas['role_id'] = $request->validate(['role_id' => 'int'])['role_id'];
                if ($datas['role_id'] != 5 and $datas['role_id'] != 6) {
                    return response()->json(['status' => 400, 'message' => $datas['name'] . '新增失敗=>沒有權限',  'success' => false], 400);
                }
            }

            $user = User::create($datas);
            $role = Role::find($datas['role_id']);

            $user->roles()->save($role);

            return response()->json(['status' => 200, 'message' => $user->name . '新增成功',  'success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '新增失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }
    public function update(Request $request, $id)
    {
        $response = $this->show($request, $id);
        $status = $response->status();
        if ($status == 200) {
            // $id = $response->getData()->result->id;  //多此一舉
            $user = User::find($id);
            try {
                //輸入規則
                $datas = $request->validate([
                    'name' => 'required|string|min:3|max:20',
                    'email' => ['required', 'string', Rule::unique('users')->ignore($user->id)],   //email唯一，排除自己
                    'gender' => 'required|string',  //之後補上正規式(男,女,其他)
                    'birth' => 'required|string|date', //日期格式
                    'address_1' => 'required|string',
                    'address_2' => 'required|string',
                    'phone' => ['required', 'string', Rule::unique('users')->ignore($user->id)],   //電話唯一，排除自己
                    'TEL' => 'required|string',
                ]);

                //決定bureau_id
                $arr_roles = $this->getRoles($request);

                //看看哪個admin能決定bureau_id
                if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                    $datas['bureau_id'] = $request->validate(['bureau_id' => 'int'])['bureau_id'];
                    $datas['role_id'] = $request->validate(['role_id' => 'int'])['role_id'];
                    $user_role = UserRole::where('user_id', $id)->get();
                    $user_role[0]->update(['role_id' => $datas['role_id']]);
                    //commit:只能改到一個role，沒辦法新增其他role，要再想辦法。ex:在role欄位存成陣列之類的or刪掉重新增??
                }
                $update = $user->update($datas);

                if ($update) {
                    return response()->json(['status' => 200, 'message' => $user->name . '更新成功', 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '更新失敗', 'success' => false], 202);
                }
            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => '資料欄位不符合要求=>' . $e->getMessage()], 400);
            }
        } else {
            return $response;
        }
    }
    //還沒加上roles會誤刪之後補上
    //社工-長者也是
    public function destroy(Request $request, $id)
    {
        $response = $this->show($request, $id);
        $status = $response->status();
        if ($status == 200) {
            try {
                $user = User::withTrashed()->findOrFail($id);
                $forcedelete = $user->forcedelete();
                if ($forcedelete) {
                    return response()->json(['status' => 200, 'message' =>  '該名使用者(' . $user->name . ')已刪除', 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '不明原因<刪除失敗>', 'success' => false], 202);
                }
            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => '使用者刪除失敗=>' . $e->getMessage(), 'success' => false], 400);
            }
        } else {
            return $response;
        }
    }
    public function disable(Request $request, $id)
    {
        $response = $this->show($request, $id);
        $status = $response->status();
        if ($status == 200) {
            return $this->UserModel->disable($id);
        } else {
            return $response;
        }
    }
    public function recovery(Request $request, $id)
    {
        $response = $this->show($request, $id);
        $status = $response->status();
        if ($status == 200) {
            return $this->UserModel->recovery($id);
        } else {
            return $response;
        }
    }
    //社工-長者
    //from Socialworkers
    public function getAvailableSocialworkers(Request $request)
    {
        $arr_roles = $this->getRoles($request);
        $role = Role::find(5);    //社工role_id=5
        try {
            //看看誰能取得哪些社工
            if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                $users = $role->users;
            } else {
                $id = $this->CL->decodeToken($request->header('token'));
                $bureau_id = User::find($id)->bureau_id;
                $users = $role->users->where('bureau_id', $bureau_id)->flatten();   //flatten function can convert dict to arr
            }
            if (count($users) > 0) {
                return response()->json(['status' => 200, 'message' => '查詢可配置的社工成功', 'result' => $users, 'success' => false], 200);
            }
            return response()->json(['status' => 202, 'message' => '查詢可配置的社工失敗=>目前無可配置的社工', 'result' => [], 'success' => false], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢可配置的社工失敗=>' . $th->getMessage(), 'result' => [], 'success' => false], 400);
        }
    }
    public function getOldersOnSocialworker($social_worker_id)
    {
        try {
            $user_social_worker = User::findOrFail($social_worker_id);
            $bureau_id = $user_social_worker->bureau_id;

            $role = Role::find(6);    //長者role_id=6
            $users = $role->users;

            $user_older = $users->where('bureau_id', $bureau_id)->where('social_worker_id', 0)->flatten();
            if (count($user_older) > 0) {
                return response()->json(['status' => 200, 'message' =>  '查詢可管理的長者成功', 'result' => $user_older, 'success' => true], 200);
            }
            return response()->json(['status' => 202, 'message' => '查詢可管理的長者失敗=>目前無可管理的長者', 'result' => [], 'success' => false], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢可管理的長者失敗=>' . $th->getMessage(), 'result' => [], 'success' => false], 400);
        }
    }
    public function addOlderToSocialworker(Request $request, $social_worker_id)
    {

        try {
            $user_id = $request->validate(['user_id' => 'int']);
            $user_id = $user_id['user_id'];

            $user_older = User::findOrFail($user_id);
            $user_social_worker = User::findOrFail($social_worker_id);

            $arr_user_older_roles = [];
            foreach ($user_older->roles as $role) {
                array_push($arr_user_older_roles, $role->name);
            }
            $arr_user_social_worker_roles = [];
            foreach ($user_social_worker->roles as $role) {
                array_push($arr_user_social_worker_roles, $role->name);
            }

            if ((count($arr_user_older_roles) == 1 and count($arr_user_social_worker_roles) == 1) and
                (in_array('user', $arr_user_older_roles) and in_array('director_user', $arr_user_social_worker_roles))
            ) {
                if ($user_older->bureau_id == $user_social_worker->bureau_id) {
                    if ($user_older->social_worker_id == null) {
                        $update = $user_older->update(['social_worker_id' => $social_worker_id]);

                        if ($update) {
                            return response()->json(['status' => 200, 'message' => '社工(編號:' . $social_worker_id . ')新增' . $user_older->name . '長者管理成功', 'success' => true], 200);
                        } else {
                            return response()->json(['status' => 202, 'message' => '社工新增管理失敗', 'success' => false], 202);
                        }
                    }
                    return response()->json(['status' => 202, 'message' => '長者已有配置社工', 'success' => false], 202);
                }
                return response()->json(['status' => 400, 'message' => '社工與長者在不同單位', 'success' => false], 400);
            } else {
                return response()->json(['status' => 400, 'message' => '社工與長者角色不對', 'success' => false], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '社工新增管理失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function getOldersWithSocialworker($social_worker_id)
    {
        try {
            $role = Role::find(6);    //長者role_id=6
            $users_older = $role->users;

            $users = $users_older->where('social_worker_id', $social_worker_id)->flatten();
            if (count($users) > 0) {
                return response()->json(['status' => 200, 'message' =>  '查詢社工(編號:' . $social_worker_id . ')管理長者成功', 'result' => $users, 'success' => true], 200);
            }
            return response()->json(['status' => 202, 'message' =>  '查詢社工(編號:' . $social_worker_id . ')管理長者失敗=>目前還沒有長者', 'result' => $users, 'success' => true], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢社工(編號:' . $social_worker_id . ')管理長者失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function delOlderFromSocialworker(Request $request, $social_worker_id)
    {
        try {
            $user_id = $request->validate(['user_id' => 'int']);
            $user_id = $user_id['user_id'];

            $users_older = Role::find(6)->users;
            $user = $users_older->where('social_worker_id', $social_worker_id)->where('id', $user_id)->flatten();
            if (count($user) > 0) {
                $user = $user[0];
                $update = $user->update(['social_worker_id' => 0]);
                if ($update) {
                    return response()->json(['status' => 200, 'message' => '社工(編號:' . $social_worker_id . ')移除' . $user->name . '長者管理成功', 'success' => true], 200);
                }
                return response()->json(['status' => 202, 'message' => '社工移除管理失敗', 'success' => false], 202);
            }
            return response()->json(['status' => 202, 'message' => '社工未有配置該長者', 'success' => false], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '社工移除管理失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    //from Olders
    public function getAvailableOlders(Request $request)
    {
        $arr_roles = $this->getRoles($request);
        $role = Role::find(6);
        try {
            //看看誰能取得哪些長者
            if (in_array('cheif_admin', $arr_roles) or in_array('bureau_admin', $arr_roles)) {
                $users = $role->users->where('social_worker_id', 0)->flatten();
            } else {
                $id = $this->CL->decodeToken($request->header('token'));
                $bureau_id = User::find($id)->bureau_id;
                $users = $role->users->where('bureau_id', $bureau_id)->flatten();   //flatten function can convert dict to arr
            }
            if (count($users) > 0) {
                return response()->json(['status' => 200, 'message' => '查詢可配置的長者成功', 'result' => $users, 'success' => false], 200);
            }
            return response()->json(['status' => 202, 'message' => '查詢可配置的長者失敗=>目前無可配置的長者', 'result' => [], 'success' => false], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢可配置的長者失敗=>' . $th->getMessage(), 'result' => [], 'success' => false], 400);
        }
    }
    public function getSocialworkersOnOlder($older_id)
    {
        try {
            $user_older = User::findOrFail($older_id);
            $bureau_id = $user_older->bureau_id;

            $users = Role::find(5)->users;

            $users_social_worker = $users->where('bureau_id', $bureau_id)->flatten();
            if (count($users_social_worker) > 0) {
                return response()->json(['status' => 200, 'message' =>  '查詢可管理的社工成功', 'result' => $users_social_worker, 'success' => true], 200);
            }
            return response()->json(['status' => 202, 'message' => '查詢可管理的社工失敗=>目前無可管理的社工', 'result' => [], 'success' => false], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢可管理的社工失敗=>' . $th->getMessage(), 'result' => [], 'success' => false], 400);
        }
    }
    public function addSocialworkerToOlder(Request $request, $older_id)
    {
        try {
            $social_worker_id = $request->validate(['social_worker_id' => 'int']);
            $social_worker_id = $social_worker_id['social_worker_id'];

            $user_older = User::findOrFail($older_id);
            $user_social_worker = User::findOrFail($social_worker_id);

            $arr_user_older_roles = [];
            foreach ($user_older->roles as $role) {
                array_push($arr_user_older_roles, $role->name);
            }
            $arr_user_social_worker_roles = [];
            foreach ($user_social_worker->roles as $role) {
                array_push($arr_user_social_worker_roles, $role->name);
            }

            if ((count($arr_user_older_roles) == 1 and count($arr_user_social_worker_roles) == 1) and
                (in_array('user', $arr_user_older_roles) and in_array('director_user', $arr_user_social_worker_roles))
            ) {
                if ($user_older->bureau_id == $user_social_worker->bureau_id) {
                    if ($user_older->social_worker_id == null) {
                        $update = $user_older->update(['social_worker_id' => $social_worker_id]);

                        if ($update) {
                            return response()->json(['status' => 200, 'message' => '長者(編號:' . $older_id . ')新增' . $user_social_worker->name . '社工管理成功', 'success' => true], 200);
                        } else {
                            return response()->json(['status' => 202, 'message' => '長者新增管理失敗', 'success' => false], 202);
                        }
                    }
                    return response()->json(['status' => 400, 'message' => '長者已有配置社工', 'success' => false], 400);
                }
                return response()->json(['status' => 400, 'message' => '長者與社工在不同單位', 'success' => false], 400);
            }
            return response()->json(['status' => 400, 'message' => '長者與社工角色不對', 'success' => false], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '長者新增管理失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function getSocialworkerWithOlder($older_id)
    {
        try {
            $user_older = User::findOrFail($older_id);
            $social_worker_id = $user_older->social_worker_id;
            $user_social_worker = User::find($social_worker_id);
            if ($user_social_worker != null) {
                return response()->json(['status' => 200, 'message' =>  '查詢長者(編號:' . $older_id . ')社工管理成功', 'result' => $user_social_worker, 'success' => true], 200);
            }
            return response()->json(['status' => 202, 'message' =>  '查詢長者(編號:' . $older_id . ')社工管理失敗=>目前還沒有社工', 'result' => $user_social_worker, 'success' => true], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '查詢長者(編號:' . $older_id . ')社工管理失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function delSocialworkerFromOlder($older_id)
    {
        try {
            $user_older = User::findOrFail($older_id);
            $social_worker_id = $user_older->social_worker_id;
            if ($social_worker_id != null) {
                $update = $user_older->update(['social_worker_id' => 0]);
                if ($update) {
                    return response()->json(['status' => 200, 'message' => '長者(編號:' . $older_id . ')移除' . User::find($social_worker_id)->name . '社工管理成功', 'success' => true], 200);
                }
                return response()->json(['status' => 202, 'message' => '長者移除管理失敗', 'success' => false], 202);
            }
            return response()->json(['status' => 202, 'message' => '長者未有配置社工', 'success' => false], 202);
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '長者移除管理失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function getOlders(Request $request)
    {
        try {
            $token = $request->header('token');
            $id = $this->CL->decodeToken($token);
            $users_older = Role::find(6)->users->where('social_worker_id', $id)->flatten();

            if (count($users_older) > 0) {
                return response()->json(['status' => 200, 'message' =>  '社工查詢管理長者成功', 'result' => $users_older, 'success' => true], 200);
            }
            return response()->json(['status' => 202, 'message' =>  '社工查詢管理長者失敗=>目前還沒有長者', 'result' => $users_older, 'success' => true], 202);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '社工查詢管理長者失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }

    //額外的function
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
    //search
    public function search(Request $request)
    {
        // method=>GET values=>params
        $filter = $request->input('filter');
        $condition = $request->input('condition');

        try {
            if ($filter != null and $condition != null) {
                $users = User::where($filter, "like", $condition . "%")->get();
                if (count($users) >= 1) {
                    return response()->json(['status' => 200, 'message' => '搜尋使用者成功' . count($users) . '筆', 'result' => $users, 'success' => true], 200);
                } else {
                    return response()->json(['status' => 200, 'message' => '查無任何使用者', 'result' => [], 'success' => true], 200);
                }
            } else {
                return response()->json(['status' => 400, 'message' => '篩選條件沒有', 'success' => true], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '搜尋使用者成功', 'result' => $th->getMessage(), 'success' => true], 400);
        }
    }
}
