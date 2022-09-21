<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScaleOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function scaledetails()
    {
        return $this->hasMany('App\Models\ScaleDetail');
    }
    public function scaleAns()
    {
        return $this->hasMany('App\Models\ScaleAnswer');
    }
}
