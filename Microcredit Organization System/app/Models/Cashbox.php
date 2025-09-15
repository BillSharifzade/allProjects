<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cashbox extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'address',
        'phone',
        'license',
    ];

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public static function booted()
    {
        self::addGlobalScope(new CompanyScope);
    }
}
