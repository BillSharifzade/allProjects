<?php
namespace App\Scopes;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CashboxScope implements Scope
{
    /**
    * Apply the scope to a given Eloquent query builder.
    *
    * @param  \Illuminate\Database\Eloquent\Builder  $builder
    * @param  \Illuminate\Database\Eloquent\Model  $model
    * @return void
    */
    public function apply(Builder $builder, Model $model)
    {
        if(Auth::check() && (Auth::user()->isCashier() || Auth::user()->isCashierAudit())) {
            $builder->where('cashbox_id', '=', Auth::user()->cashboxUser->cashbox_id);
        }
    }
}
