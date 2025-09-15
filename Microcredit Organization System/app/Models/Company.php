<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [

    ];
    protected $casts = [
        'is_auditable' => 'boolean',
    ];

    public function isAuditable() {
        return $this->is_auditable;
    }

    public function interestRates() {
        return $this->hasMany(InterestRate::class);
    }
}
