<?php

namespace App\Http\Middleware;

use App\Models\CashierShift;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOpenShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if (!($user->isCashier() || $user->isCashierAudit())) {
            abort(403);
        }

        $cashboxId = optional($user->cashboxUser)->cashbox_id;
        if (!$cashboxId) {
            return redirect()->back()->withErrors(['Кассир не привязан к кассе'])->withInput();
        }

        $shift = CashierShift::where('user_id', $user->id)
            ->where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (!$shift) {
            return redirect()->back()->withErrors(['Нет открытой смены'])->withInput();
        }

        return $next($request);
    }
}


