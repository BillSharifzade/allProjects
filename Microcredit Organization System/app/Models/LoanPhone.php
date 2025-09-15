<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanPhone extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'loan_id',
        'brand',
        'model',
        'imei',
        'storage_gb',
        'color',
        'condition',
        'description',
    ];

    public function loan() {
        return $this->belongsTo(Loan::class);
    }
}


