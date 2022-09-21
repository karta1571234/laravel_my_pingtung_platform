<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    //column setting
    protected $fillable = ['role_id'];
    //rename table name=>manually specify the model's table name by defining a table property on the model
    protected $table = 'user_role';

    // protected $hidden = ['pivot'];
}
