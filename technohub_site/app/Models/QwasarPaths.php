<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QwasarPaths extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status'
    ];
}
