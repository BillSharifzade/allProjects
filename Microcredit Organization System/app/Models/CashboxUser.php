<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashboxUser extends Model
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
        'cashbox_id',
        'user_id',
        'user_license'
    ];

    public static function booted()
    {
        self::addGlobalScope(new CompanyScope);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cashbox() {
        return $this->belongsTo(Cashbox::class);
    }
}
