<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanSale extends Model
{
    use HasFactory;

    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id',
        'cashbox_id',
        'user_id',
        'shift_id',
        'loan_id',
        'sold_at',
        'amount_principal',
        'amount_interest',
        'total_amount',
        'event_id',
        'prev_left_sum',
        'prev_last_principal_payment_date',
        'prev_last_interest_payment_date',
        'prev_latest_interest_payments_sum',
        'canceled_at',
        'canceled_by',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }
}


