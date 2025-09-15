<?php
namespace App\Scopes;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
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
        if(Auth::check()) {
            $builder->where('company_id', '=', Auth::user()->company_id);
        }
    }
}
