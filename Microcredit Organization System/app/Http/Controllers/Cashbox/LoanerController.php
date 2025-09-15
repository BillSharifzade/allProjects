<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanerResource;
use App\Models\Loaner;
use App\Models\BlacklistEntry;
use App\Helpers;
use Illuminate\Http\Request;

class LoanerController extends Controller
{
    public function show(Request $request, $passportNumber) {
        $resource = new LoanerResource(Loaner::firstWhere('passport_number', $passportNumber));
        $pidNorm = Helpers::normalizePassportId($passportNumber);
        $isBlacklisted = $pidNorm !== '' ? BlacklistEntry::where('passport_id_norm', $pidNorm)->exists() : false;
        $data = $resource->toArray($request);
        $data['is_blacklisted'] = $isBlacklisted;
        return response()->json($data);
    }
}
