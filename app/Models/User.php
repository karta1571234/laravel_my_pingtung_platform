<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'ID_num',
        'gender',
        'birth',
        'address_1',
        'address_2',
        'phone',
        'TEL',
        'bureau_id',
        'password',
        'social_worker_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        //format unix-timestamp
        // 'created_at' => 'datetime:Y-m-d h:i:s',
        // 'updated_at' => 'datetime:Y-m-d h:i:s',
        // 'deleted_at' => 'datetime:Y-m-d h:i:s',
    ];

    //relation
    public function questionnaireAns()
    {
        return $this->hasOne('App\Models\QuestionnaireAnswer');
    }
    public function scaleAns()
    {
        return $this->hasMany('App\Models\ScaleAnswer');
    }
    public function news()
    {
        return $this->hasMany('App\Models\News');
    }
    public function roles()
    {
        //Eloquent will automatically set these column's values when models are created or updated
        return $this->belongsToMany('App\Models\Role', 'user_role')->withTimestamps();
    }
    public function bureau()
    {
        return $this->belongsTo('App\Models\Bureau');
    }

    //function
    public function index()
    {
        $users = User::withTrashed()->orderBy('id')->get();
        foreach ($users as $user) {
            $arr_roles = [];
            foreach ($user->roles as $role) {
                array_push($arr_roles, $role->name);
            }
            $user['role'] = $arr_roles;
        }
        if (count($users) >= 1) {
            return response()->json(['status' => 200, 'message' => '取得所有使用者成功', 'result' => $users, 'success' => true], 200);
        }
        return response()->json(['status' => 202, 'message' => '取得所有使用者失敗', 'success' => false], 202);
    }
    public function show($id)
    {
        $profile = User::withTrashed()->find($id);
        if ($profile != null) {
            return response()->json(['status' => 200, 'message' => '取得單一使用者成功', 'result' => $profile, 'success' => true], 200);
        } else {
            return response()->json(['status' => 202, 'message' => '抓不到沒有這個人', 'success' => false], 202);
        }
    }
    public function disable($id)
    {
        try {
            $user = User::withTrashed()->find($id);
            if ($user != null) {
                if ($user->deleted_at == null) {
                    $delete = $user->delete();
                    if ($delete) {
                        return response()->json(['status' => 200, 'message' => '該名使用者(' . $user->name . ')已禁用', 'success' => true], 200);
                    } else {
                        return response()->json(['status' => 202, 'message' => '不明原因<禁用失敗>', 'success' => false], 202);
                    }
                } else {
                    return response()->json(['status' => 202, 'message' => $user->name . '已經被禁用了', 'success' => false], 202);
                }
            } else {
                return response()->json(['status' => 202, 'message' => '沒有這個人<禁用失敗>', 'success' => false], 202);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '使用者禁用失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function recovery($id)
    {
        try {
            $user = User::withTrashed()->find($id);
            if ($user != null) {
                if ($user->deleted_at != null) {
                    $restore = $user->restore();
                    if ($restore) {
                        return response()->json(['status' => 200, 'message' =>  '該名使用者(' . $user->name . ')已啟用', 'success' => true], 200);
                    } else {
                        return response()->json(['status' => 202, 'message' => '不明原因<啟用失敗>', 'success' => false], 202);
                    }
                } else {
                    return response()->json(['status' => 202, 'message' => $user->name . '不需啟用<沒有禁用>', 'success' => false], 202);
                }
            } else {
                return response()->json(['status' => 202, 'message' => '沒有這個人<啟用失敗>', 'success' => false], 202);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '使用者啟用失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    //社工-長者(for director_admin)(目前用不到了)
    public function getAvailableSocialworkers($bureau_id)
    {
        $sql = 'SELECT `users`.`id`,`name`,`email`,`ID_num`,`gender`,`birth`,`address_1`,`address_2`,`phone`,`TEL`,`users`.`created_at`,`users`.`updated_at`,`users`.`deleted_at`
                FROM `users`,`user_role` WHERE `user_role`.`role_id`=5 and `user_role`.`user_id` = `users`.`id` and `users`.`bureau_id`=?;';
        $args = array($bureau_id);
        $select = DB::select($sql, $args);

        return $select;
    }
}
