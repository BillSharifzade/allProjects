<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncassatorSafeLoan extends Model
{
    use HasFactory;

    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id', 'incassator_id', 'cashbox_id', 'contract_no', 'client_name', 'loan_info'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function cashbox() { return $this->belongsTo(Cashbox::class); }
    public function incassator() { return $this->belongsTo(User::class, 'incassator_id'); }
}


