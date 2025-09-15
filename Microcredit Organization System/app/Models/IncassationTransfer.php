<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncassationTransfer extends Model
{
    use HasFactory;

    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id','cashbox_id','incassator_id','cashier_id','loan_id','contract_no','client_name','loan_info',
        'picked_by_incassator','picked_at','delivered_by_incassator','delivered_at','accepted_by_cashier','accepted_at'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function cashbox() { return $this->belongsTo(Cashbox::class); }
    public function incassator() { return $this->belongsTo(User::class, 'incassator_id'); }
    public function cashier() { return $this->belongsTo(User::class, 'cashier_id'); }
    public function loan() { return $this->belongsTo(Loan::class); }
}


