<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrEmployee extends Model
{
    use HasFactory; // hard delete to avoid datetime mismatch

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id','first_name','last_name','phone','email','passport_number','photo','position','active','created_at','updated_at'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function contracts()
    {
        return $this->hasMany(HrEmployeeContract::class, 'employee_id');
    }
}


