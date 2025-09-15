<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrEmployeeContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrController extends Controller
{
    public function index()
    {
        $items = HrEmployee::with(['contracts' => function($q){ $q->orderBy('id','desc'); }])
            ->orderBy('last_name')->paginate(50);
        return view('admin.hr.index', [ 'items' => $items ]);
    }

    public function create()
    {
        return view('admin.hr.create');
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'first_name' => ['required','string','min:2'],
            'last_name' => ['required','string','min:2'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'passport_number' => ['nullable','string'],
            'position' => ['nullable','string'],
            'photo' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:3072'],
            'salary' => ['nullable','numeric','gte:0'],
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date'],
        ]);

        $e = new HrEmployee();
        $e->company_id = Auth::user()->company_id;
        $e->first_name = $v['first_name'];
        $e->last_name = $v['last_name'];
        $e->phone = $v['phone'] ?? '';
        $e->email = $v['email'] ?? '';
        $e->passport_number = $v['passport_number'] ?? '';
        $e->position = $v['position'] ?? '';
        $e->created_at = time();

        if(isset($v['photo'])){
            $file = $request->file('photo');
            $newFileName = Auth::user()->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $uploadsDir = storage_path('app/public/hr');
            if(!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0775, true); }
            $file->move($uploadsDir, $newFileName);
            $e->photo = 'storage/hr/' . $newFileName;
        }

        $e->save();

        // Optional immediate contract
        if(isset($v['salary'])){
            $c = new HrEmployeeContract();
            $c->company_id = Auth::user()->company_id;
            $c->employee_id = $e->id;
            $c->contract_no = '';
            $c->start_date = isset($v['start_date']) ? strtotime($v['start_date']) : time();
            $c->end_date = isset($v['end_date']) ? strtotime($v['end_date']) : 0;
            $c->salary = $v['salary'];
            $c->currency = 'TJS';
            $c->created_at = time();
            $c->save();
        }

        return redirect()->route('admin-hr');
    }

    public function edit(HrEmployee $employee)
    {
        return view('admin.hr.edit', [ 'e' => $employee->load('contracts') ]);
    }

    public function update(Request $request, HrEmployee $employee)
    {
        $v = $request->validate([
            'first_name' => ['required','string','min:2'],
            'last_name' => ['required','string','min:2'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'passport_number' => ['nullable','string'],
            'position' => ['nullable','string'],
            'photo' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:3072'],
        ]);

        $employee->first_name = $v['first_name'];
        $employee->last_name = $v['last_name'];
        $employee->phone = $v['phone'] ?? '';
        $employee->email = $v['email'] ?? '';
        $employee->passport_number = $v['passport_number'] ?? '';
        $employee->position = $v['position'] ?? '';
        $employee->updated_at = time();

        if(isset($v['photo'])){
            $file = $request->file('photo');
            $newFileName = Auth::user()->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $uploadsDir = storage_path('app/public/hr');
            if(!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0775, true); }
            $file->move($uploadsDir, $newFileName);
            $employee->photo = 'storage/hr/' . $newFileName;
        }

        $employee->save();

        return redirect()->route('admin-hr');
    }

    public function delete(HrEmployee $employee)
    {
        try {
            HrEmployeeContract::where('employee_id', $employee->id)->forceDelete();
        } catch (\Throwable $e) {}
        $employee->forceDelete();
        return redirect()->route('admin-hr');
    }

    public function addContract(Request $request, HrEmployee $employee)
    {
        $v = $request->validate([
            'contract_no' => ['nullable','string'],
            'start_date' => ['required','date'],
            'end_date' => ['nullable','date'],
            'salary' => ['required','numeric','gte:0'],
            'currency' => ['nullable','string','max:8'],
            'notes' => ['nullable','string'],
            'file' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp','max:8192'],
        ]);

        $c = new HrEmployeeContract();
        $c->company_id = Auth::user()->company_id;
        $c->employee_id = $employee->id;
        $c->contract_no = $v['contract_no'] ?? '';
        $c->start_date = strtotime($v['start_date']);
        $c->end_date = isset($v['end_date']) ? strtotime($v['end_date']) : 0;
        $c->salary = $v['salary'];
        $c->currency = $v['currency'] ?? 'TJS';
        $c->notes = $v['notes'] ?? '';
        if(isset($v['file'])){
            $file = $request->file('file');
            $newFileName = Auth::user()->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $uploadsDir = storage_path('app/public/hr/contracts');
            if(!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0775, true); }
            $file->move($uploadsDir, $newFileName);
            $c->notes = trim(($c->notes ? $c->notes.' ' : '').'(файл: storage/hr/contracts/'.$newFileName.')');
        }
        $c->created_at = time();
        $c->save();

        return redirect()->route('admin-hr-edit', ['employee' => $employee->id]);
    }
}


