<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterestRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id',
        'sum_from',
        'sum_to',
        'rate'
    ];

    protected static function booted()
    {
        self::addGlobalScope(new CompanyScope);
    }
}
