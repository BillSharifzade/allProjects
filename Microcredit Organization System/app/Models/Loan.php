<?php

namespace App\Models;

use App\Scopes\AuditScope;
use App\Scopes\CashboxScope;
use App\Scopes\LoanOpenScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    public $grace_period = 0;
    public $unpaid_interest = 0;
    public $unpaid_days = 0;
    public $daily_interest = 0;
    public $paid_days = 0;
    public $monthly_interest = 0;
    public $daily_interest_rate = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cashbox_id',
        'user_id',
        'loaner_id',
        'document_no',
        'audit_document_no',
        'lend_date',
        'initial_sum',
        'left_sum',
        'interestRate',
        'interest_accumulation_date',
        'last_principal_payment_date',
        'latest_interest_payments_sum',
        'last_interest_payment_date',
        'close_request_at',
        'closed_at',
        'image',
        'props',
        'in_audit',
        'is_notifiable',
        'collateral_type'
    ];

    protected $casts = [
        'props' => 'array',
        'in_audit' => 'boolean',
        'is_notifiable' => 'boolean'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::addGlobalScope(new AuditScope);
        self::addGlobalScope(new CashboxScope);
        self::addGlobalScope(new LoanOpenScope);
    }

    public function loaner() {
        return $this->belongsTo(Loaner::class);
    }

    public function jewelries() {
        return $this->hasMany(LoanJewelry::class);
    }

    public function auto() {
        return $this->hasOne(LoanAuto::class);
    }

    public function phone() {
        return $this->hasOne(LoanPhone::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function cashbox() {
        return $this->belongsTo(Cashbox::class);
    }

    /**
     * Unified monthly interest rate for display, sourced from admin InterestRate table.
     * Falls back to stored fields for legacy data.
     */
    public function getDisplayRateAttribute(): float
    {
        // Prefer normalized snake_case column if present
        if (isset($this->attributes['interest_rate']) && $this->attributes['interest_rate'] !== null) {
            return (float)$this->attributes['interest_rate'];
        }
        // Legacy camelCase column fallback
        if (isset($this->attributes['interestRate']) && $this->attributes['interestRate'] !== null) {
            return (float)$this->attributes['interestRate'];
        }

        // Final fallback: compute from current left_sum via admin-defined ranges
        try {
            $rate = \App\Models\InterestRate::where('company_id', (int)($this->attributes['company_id'] ?? 0))
                ->where('sum_from', '<=', (float)($this->attributes['left_sum'] ?? 0))
                ->where('sum_to', '>=', (float)($this->attributes['left_sum'] ?? 0))
                ->value('rate');
            return (float)$rate;
        } catch (\Throwable $e) {
            return 0.0;
        }
    }
}
