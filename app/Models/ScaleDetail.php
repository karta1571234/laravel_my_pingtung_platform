<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'scale_order_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
