<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoldPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    public static function booted()
    {
        self::addGlobalScope(new CompanyScope);
    }
}
