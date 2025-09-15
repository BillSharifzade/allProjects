<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashierShift extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id', 'cashbox_id', 'user_id',
        'opened_at', 'closed_at',
        'opening_balance', 'closing_balance', 'discrepancy', 'note'
    ];
}


