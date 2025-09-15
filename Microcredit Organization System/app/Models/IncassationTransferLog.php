<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncassationTransferLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';
    protected $table = 'incassation_transfer_logs';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'incassation_transfer_id',
        'actor_user_id',
        'action',
        'picked_by_incassator',
        'delivered_by_incassator',
        'accepted_by_cashier',
        'created_at',
    ];
}
