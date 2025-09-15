<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminGoldPriceStoreRequest;
use App\Http\Requests\AdminGoldPriceUpdateRequest;
use App\Http\Resources\GoldPriceCollection;
use App\Models\GoldPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoldPriceController extends Controller
{
    public function index(Request $request) {
        return view('admin.goldPrice.index', [
            'goldPrices' => GoldPrice::get(),
        ]);
    }

    public function create() {
        return view('admin.goldPrice.create');
    }

    public function store(AdminGoldPriceStoreRequest $request) {
        $validated = $request->validated();

        $goldPrice = GoldPrice::where('purity', $validated['purity'])->first();
        if(!$goldPrice) {
            $goldPrice = new GoldPrice();
        }

        $goldPrice->company_id = Auth::user()->company_id;
        $goldPrice->purity = $validated['purity'];
        $goldPrice->price = $validated['price'];

        if($goldPrice->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось сохранить цену'
            ])->withInput();
        }

        return redirect()->route('gold-prices');
    }

    public function edit(Request $request, GoldPrice $goldPrice) {
        return view('admin.goldPrice.edit', [
            'goldPrice' => $goldPrice
        ]);
    }

    public function update(AdminGoldPriceUpdateRequest $request, GoldPrice $goldPrice) {
        $validated = $request->validated();

        $goldPrice->purity = $validated['purity'];
        $goldPrice->price = $validated['price'];

        if($goldPrice->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось сохранить цену'
            ])->withInput();
        }

        return redirect()->route('gold-prices');
    }

    public function delete(Request $request, GoldPrice $goldPrice) {
        $goldPrice->delete();
        return redirect()->route('gold-prices');
    }

    public function json() {
        return response()->json(new GoldPriceCollection(GoldPrice::get()));
    }
}
