<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id',
        'user_id',
        'loan_id',
        'text'
    ];

    protected static function booted()
    {
        self::addGlobalScope(new CompanyScope);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
