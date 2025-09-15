<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutQwasar extends Model
{
    use HasFactory;

    protected $table = 'about_qwasar';

    protected $fillable = ['title', 'description'];
}
