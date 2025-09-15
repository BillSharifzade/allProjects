<?php

namespace App\Models;

use App\Scopes\CashboxScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loaner extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'phone1',
        'phone2',
        'phone3',
        'phone4',
        'tin',
        'passport_number',
        'passport_issuer',
        'passport_issued_day',
        'passport_issued_month',
        'passport_issued_year',
        'birth_day',
        'birth_month',
        'birth_year',
        'residence_address',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        self::addGlobalScope(new CashboxScope);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
