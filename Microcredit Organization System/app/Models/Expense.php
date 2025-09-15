<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id', 'cashbox_id', 'user_id', 'shift_id',
        'category', 'description', 'amount', 'occurred_at'
    ];
}


