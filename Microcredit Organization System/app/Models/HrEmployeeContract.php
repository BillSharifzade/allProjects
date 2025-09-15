<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrEmployeeContract extends Model
{
    use HasFactory; // hard delete to avoid datetime mismatch

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id','employee_id','contract_no','start_date','end_date','salary','currency','notes','created_at','updated_at'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}


