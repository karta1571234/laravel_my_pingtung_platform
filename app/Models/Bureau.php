<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bureau extends Model
{
    use HasFactory;

    //relation
    public function users()
    {
        return $this->hasMany('App\Models\User')->withTrashed();
    }
}
