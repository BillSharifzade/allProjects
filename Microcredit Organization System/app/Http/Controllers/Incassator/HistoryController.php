<?php

namespace App\Http\Controllers\Incassator;

use App\Http\Controllers\Controller;
use App\Models\IncassationTransfer;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $items = IncassationTransfer::where('company_id', Auth::user()->company_id)
            ->where('delivered_by_incassator', true)
            ->orderBy('id', 'desc')
            ->paginate(100);

        return view('incassator.history.index', [ 'items' => $items ]);
    }
}


