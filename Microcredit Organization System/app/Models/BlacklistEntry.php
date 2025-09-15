<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistEntry extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id', 'passport_id_norm', 'full_name', 'phone', 'raw_json'
    ];

    protected static function booted()
    {
        self::addGlobalScope(new CompanyScope);
    }
}


