<?php

namespace App\Models;

use App\Scopes\CashboxScope;
use App\Scopes\LoanOpenScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
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
        'loan_id',
        'uuid',
        'type',
        'sum',
        'paid_date',
        'document_no',
    ];

    public function loan() {
        return $this->belongsTo(Loan::class)->withoutGlobalScope(new LoanOpenScope);
    }

    public static function booted()
    {
        self::addGlobalScope(new CashboxScope);
    }
}
