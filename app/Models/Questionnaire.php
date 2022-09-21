<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'option',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
}
