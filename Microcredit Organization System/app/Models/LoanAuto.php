<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanAuto extends Model
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
        'loan_id',
        'brand',
        'year',
        'color',
        'plate_number',
        'engine',
        'location',
        'description',
        'gas',
        'transmission',
        'mileage'
    ];

    public function loan() {
        return $this->belongsTo(Loan::class);
    }
}
