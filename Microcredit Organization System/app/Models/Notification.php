<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

    protected $fillable = [
        'company_id',
        'date',
        'type'
    ];
}
