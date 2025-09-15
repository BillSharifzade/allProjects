<?php

namespace App\Models;

use App\Models\CashierShift;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashboxLedger extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $dateFormat = 'U';
    protected $table = 'cashbox_ledger';

    protected $fillable = [
        'company_id', 'cashbox_id', 'user_id', 'shift_id',
        'event_type', 'event_id', 'direction', 'amount', 'occurred_at'
    ];

    protected static function booted()
    {
        static::creating(function (CashboxLedger $entry) {
            // Prevent writing an outflow that would result in a negative balance
            if ((int)$entry->direction < 0) {
                // Allow administrative reversals to be recorded even if the original shift is closed
                $exemptEvents = ['expense_reversal','reversal','loan_sale_reversal','loan_sale_profit_reversal'];
                if (in_array((string)$entry->event_type, $exemptEvents, true)) {
                    return true;
                }

                $shiftId = (int)$entry->shift_id;
                if ($shiftId <= 0) {
                    abort(422, 'Нет открытой смены');
                }

                $shift = CashierShift::where('id', $shiftId)->first();
                if (!$shift || (int)$shift->closed_at > 0) {
                    abort(422, 'Нет открытой смены');
                }

                $delta = CashboxLedger::where('shift_id', $shiftId)
                    ->selectRaw('COALESCE(SUM(direction * amount),0) as delta')
                    ->value('delta');

                $available = (float)$shift->opening_balance + (float)$delta;

                if ($available < (float)$entry->amount) {
                    abort(422, 'Недостаточно средств. Доступно: ' . number_format($available, 2, '.', ' '));
                }
            }

            return true;
        });
    }
}


