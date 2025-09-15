<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminInterestRateStoreRequest;
use App\Http\Requests\AdminInterestRateUpdateRequest;
use App\Models\InterestRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InterestRateController extends Controller
{
    public function index() {
        return view('admin.interestRate.index', [
            'interestRates' => InterestRate::get(),
        ]);
    }

    public function create() {
        return view('admin.interestRate.create');
    }

    public function store(AdminInterestRateStoreRequest $request) {
        $validated = $request->validated();

        $interestRate = InterestRate::where('sum_from', $validated['sum_from'])->where('sum_to', $validated['sum_to'])->first();
        if(!$interestRate) {
           $interestRate = new InterestRate();
        }
        $interestRate->company_id = Auth::user()->company_id;
        $interestRate->sum_from = $validated['sum_from'];
        $interestRate->sum_to = $validated['sum_to'];
        $interestRate->rate = $validated['rate'];

        // Guard against overlapping ranges for the same company
        $overlap = InterestRate::where('company_id', Auth::user()->company_id)
            ->where('id', '<>', $interestRate->id ?? 0)
            ->where(function($q) use ($validated) {
                $q->whereBetween('sum_from', [$validated['sum_from'], $validated['sum_to']])
                  ->orWhereBetween('sum_to', [$validated['sum_from'], $validated['sum_to']])
                  ->orWhere(function($qq) use ($validated){
                      $qq->where('sum_from', '<=', $validated['sum_from'])
                         ->where('sum_to', '>=', $validated['sum_to']);
                  });
            })
            ->exists();
        if($overlap) {
            return redirect()->back()->withErrors(['Диапазон сумм пересекается с существующим'])->withInput();
        }

        if($interestRate->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось сохранить процентовку'
            ])->withInput();
        }

        return redirect()->route('interest-rates');
    }

    public function edit(Request $request, InterestRate $interestRate) {
        return view('admin.interestRate.edit', [
            'interestRate' => $interestRate,
        ]);
    }

    public function update(AdminInterestRateUpdateRequest $request, InterestRate $interestRate) {
        $validated = $request->validated();

        $interestRate->sum_from = $validated['sum_from'];
        $interestRate->sum_to = $validated['sum_to'];
        $interestRate->rate = $validated['rate'];

        // Guard against overlapping ranges for the same company (excluding current)
        $overlap = InterestRate::where('company_id', Auth::user()->company_id)
            ->where('id', '<>', $interestRate->id)
            ->where(function($q) use ($validated) {
                $q->whereBetween('sum_from', [$validated['sum_from'], $validated['sum_to']])
                  ->orWhereBetween('sum_to', [$validated['sum_from'], $validated['sum_to']])
                  ->orWhere(function($qq) use ($validated){
                      $qq->where('sum_from', '<=', $validated['sum_from'])
                         ->where('sum_to', '>=', $validated['sum_to']);
                  });
            })
            ->exists();
        if($overlap) {
            return redirect()->back()->withErrors(['Диапазон сумм пересекается с существующим'])->withInput();
        }

        if($interestRate->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось сохранить процентовку'
            ])->withInput();
        }

        return redirect()->route('interest-rates');
    }

    public function delete(Request $request, InterestRate $interestRate) {
        $interestRate->delete();
        return redirect()->route('interest-rates');
    }
}
