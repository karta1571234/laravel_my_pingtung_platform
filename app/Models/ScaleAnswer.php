<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ScaleAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer',
        'scale_order_id',
    ];

    //relation
    public function scaleorder()
    {
        return $this->belongsTo('App\Models\ScaleOrder', 'scale_order_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    //SQL
    public function getScaleAnswer($scale_order_id = 0, $bureau_id = 0)
    {
        $sql = "SELECT * FROM `scale_answers`,`users` WHERE `scale_answers`.`user_id`=`users`.`id` and `users`.`bureau_id`=?;";
        $args = array($bureau_id);
        $response = DB::select($sql, $args);
        return $response;
    }
}
