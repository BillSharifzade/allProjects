<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $fillable = [
        'title',
        'description',
        'status',
        'start_date',
        'duration',
        'start_time',
        'end_time',
        'category_id'
    ];
}
