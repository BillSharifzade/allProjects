<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Resources\GoldPriceCollection;
use App\Http\Resources\LoanerResource;
use App\Models\GoldPrice;
use App\Models\Loaner;
use Illuminate\Http\Request;

class GoldPriceController extends Controller
{
    public function json() {
        return response()->json(new GoldPriceCollection(GoldPrice::get()));
    }
}
