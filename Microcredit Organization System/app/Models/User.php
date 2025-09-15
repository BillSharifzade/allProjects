<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'login',
        'password',
        'phone',
        'role',
        'is_auditor'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_auditor' => 'boolean',
    ];

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function isCashier() {
        return $this->role === "cashier";
    }
    public function isCashierAudit() {
        return $this->role === "cashier-audit";
    }

    public function isAdmin() {
        return $this->role === "admin";
    }

    public function isReporter() {
        return $this->role === "reporter";
    }

    public function isAudit() {
        return $this->role == "audit";
    }

    public function isIncassator() {
        return $this->role === "incassator";
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function cashboxUser() {
        return $this->hasOne(CashboxUser::class);
    }
}
