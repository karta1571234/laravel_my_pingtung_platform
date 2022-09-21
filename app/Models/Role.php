<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    //relation
    public function users()
    {
        //Eloquent will automatically set these column's values when models are created or updated
        return $this->belongsToMany('App\Models\User', 'user_role')->withTimestamps();
    }
}
