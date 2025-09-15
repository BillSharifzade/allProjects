<?php

namespace App\Http\Controllers\Incassator;

use App\Http\Controllers\Controller;
use App\Models\IncassatorSafeLoan;
use App\Models\Cashbox;
use App\Models\Loan;
use App\Models\IncassationTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SafeController extends Controller
{
    public function index(Request $request)
    {
        $cashboxId = (int)$request->get('cashbox');
        $q = IncassatorSafeLoan::where('incassator_id', Auth::id());
        if ($cashboxId > 0) { $q->where('cashbox_id', $cashboxId); }
        $items = $q->orderBy('id','desc')->paginate(50);
        $cashboxes = Cashbox::orderBy('name')->get();
        return view('incassator.safe.index', [ 'items' => $items, 'cashboxes' => $cashboxes, 'selectedCashbox' => $cashboxId ]);
    }

    public function create(Request $request)
    {
        $cashboxes = Cashbox::orderBy('name')->get();
        $cashboxId = (int)$request->get('cashbox');
        $loans = collect();
        if ($cashboxId > 0) {
            // Exclude loans already in this incassator's safe (robustly)
            $safeLoanIds = IncassatorSafeLoan::where('incassator_id', Auth::id())
                ->where('cashbox_id', $cashboxId)
                ->whereNotNull('loan_id')
                ->pluck('loan_id')->all();
            $safeContractsRaw = IncassatorSafeLoan::where('incassator_id', Auth::id())
                ->where('cashbox_id', $cashboxId)
                ->pluck('contract_no')->all();

            // Normalize contract strings (strip '№', spaces, anything except digits and dash)
            $normalized = array_values(array_filter(array_map(function($c){
                $c = (string)$c;
                $c = preg_replace('/[^0-9\-]/', '', $c);
                return $c;
            }, $safeContractsRaw), function($v){ return $v !== null && $v !== ''; }));

            $baseOnly = [];
            $pairs = [];
            foreach ($normalized as $n) {
                if (strpos($n, '-') !== false) {
                    [$b, $a] = explode('-', $n, 2);
                    if ($b !== '' && $a !== '' && ctype_digit($b) && ctype_digit($a)) {
                        $pairs[] = [ (int)$b, (int)$a ];
                    } elseif ($b !== '' && ctype_digit($b)) {
                        $baseOnly[] = (int)$b;
                    }
                } else {
                    if ($n !== '' && ctype_digit($n)) { $baseOnly[] = (int)$n; }
                }
            }

            // Map normalized contracts to loan ids to exclude
            $excludeIds = [];
            if (!empty($baseOnly)) {
                $idsByBase = Loan::where('cashbox_id', $cashboxId)
                    ->where('closed_at', 0)
                    ->whereIn('document_no', $baseOnly)
                    ->pluck('id')->all();
                $excludeIds = array_merge($excludeIds, $idsByBase);
            }
            if (!empty($pairs)) {
                $idsByPairs = Loan::where('cashbox_id', $cashboxId)
                    ->where('closed_at', 0)
                    ->where(function($q) use ($pairs){
                        foreach ($pairs as $p) {
                            $q->orWhere(function($qq) use ($p){
                                $qq->where('document_no', (int)$p[0])
                                   ->where('audit_document_no', (int)$p[1]);
                            });
                        }
                    })
                    ->pluck('id')->all();
                $excludeIds = array_merge($excludeIds, $idsByPairs);
            }
            if (!empty($safeLoanIds)) {
                $excludeIds = array_merge($excludeIds, $safeLoanIds);
            }
            $excludeIds = array_values(array_unique($excludeIds));

            $query = Loan::with('loaner')->with('jewelries')->with('auto')->with('phone')
                ->where('cashbox_id', $cashboxId)
                ->where('closed_at', 0);

            if (!empty($excludeIds)) {
                $query->whereNotIn('id', $excludeIds);
            }

            $loans = $query->orderBy('id','desc')->paginate(100);
        }
        return view('incassator.safe.create', [ 'cashboxes' => $cashboxes, 'loans' => $loans, 'selectedCashbox' => $cashboxId ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cashbox_id' => ['required','integer','gt:0'],
            'loan_ids' => ['array'],
            'loan_ids.*' => ['integer','gt:0'],
            'select_all' => ['nullable','in:on,1,true'],
        ]);

        $selectAll = (bool)$request->get('select_all');

        $query = Loan::with('loaner')->with('jewelries')->with('auto')->with('phone')
            ->where('cashbox_id', $validated['cashbox_id'])
            ->where('closed_at', 0)
            ->orderBy('id','asc');

        if (!$selectAll) {
            $ids = (array)($validated['loan_ids'] ?? []);
            if (count($ids) === 0) {
                return redirect()->back()->withErrors(['Ничего не выбрано'])->withInput();
            }
            $query->whereIn('id', $ids);
        }

        $query->chunk(500, function($loans) use ($validated) {
            foreach ($loans as $loan) {
                $contractFullForCheck = ($loan->audit_document_no > 0 ? ($loan->document_no . '-' . $loan->audit_document_no) : $loan->document_no);
                $exists = IncassatorSafeLoan::where('incassator_id', Auth::id())
                    ->where('cashbox_id', $validated['cashbox_id'])
                    ->where(function($q) use ($loan, $contractFullForCheck){
                        $q->where('loan_id', $loan->id)
                          ->orWhere('contract_no', $loan->document_no)
                          ->orWhere('contract_no', $contractFullForCheck);
                    })
                    ->exists();
                if ($exists) { continue; }
                $info = '';
                if ($loan->collateral_type == 1) {
                    foreach ($loan->jewelries as $j) {
                        $info .= ($info ? '; ' : '') . $j->name . ', ' . $j->purity . ' пр., ' . $j->weight . ' гр.';
                    }
                } elseif ($loan->collateral_type == 2 && $loan->auto) {
                    $info = 'марка ' . $loan->auto->brand . ', ' . $loan->auto->year . ', ' . $loan->auto->plate_number;
                } elseif ($loan->collateral_type == 3 && $loan->phone) {
                    $info = 'смартфон ' . $loan->phone->brand . ' ' . $loan->phone->model;
                    if (!empty($loan->phone->storage_gb)) { $info .= ' ' . $loan->phone->storage_gb . 'GB'; }
                    if (!empty($loan->phone->color)) { $info .= ', ' . $loan->phone->color; }
                    if (!empty($loan->phone->imei)) { $info .= ', IMEI ' . $loan->phone->imei; }
                }
                IncassatorSafeLoan::create([
                    'company_id' => Auth::user()->company_id,
                    'incassator_id' => Auth::id(),
                    'cashbox_id' => $validated['cashbox_id'],
                    'loan_id' => $loan->id,
                    'contract_no' => ($loan->audit_document_no > 0 ? ($loan->document_no . '-' . $loan->audit_document_no) : $loan->document_no),
                    'client_name' => optional($loan->loaner)->full_name,
                    'loan_info' => $info,
                    'created_at' => time(),
                ]);

                // If the loan is already fully settled, create an incassation transfer now
                try {
                    $fresh = Loan::with('jewelries','auto','phone','loaner')->where('id', $loan->id)->first();
                    if ($fresh && (float)$fresh->left_sum == 0.0 && (float)round($fresh->unpaid_interest,2) == 0.0) {
                        $hasTransfer = IncassationTransfer::where('company_id', Auth::user()->company_id)
                            ->where('loan_id', $fresh->id)->exists();
                        if (!$hasTransfer) {
                            $contractFull = '№' . $fresh->document_no . ($fresh->audit_document_no > 0 ? ('-' . $fresh->audit_document_no) : '');
                            $tInfo = $info; // already built above
                            IncassationTransfer::create([
                                'company_id' => Auth::user()->company_id,
                                'cashbox_id' => $validated['cashbox_id'],
                                'incassator_id' => null,
                                'cashier_id' => null,
                                'loan_id' => $fresh->id,
                                'contract_no' => $contractFull,
                                'client_name' => optional($fresh->loaner)->full_name,
                                'loan_info' => $tInfo,
                                'picked_by_incassator' => false,
                                'picked_at' => 0,
                                'delivered_by_incassator' => false,
                                'delivered_at' => 0,
                                'accepted_by_cashier' => false,
                                'accepted_at' => 0,
                                'created_at' => time(),
                            ]);
                        }
                    }
                } catch (\Throwable $e) {}
            }
        });

        return redirect()->route('inc-safe', ['cashbox' => $validated['cashbox_id']]);
    }
}


