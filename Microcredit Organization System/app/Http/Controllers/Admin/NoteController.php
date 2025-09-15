<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashboxNoteStoreRequest;
use App\Http\Requests\CashboxNoteUpdateRequest;
use App\Models\Loan;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request) {
        return view('admin.note.index',[
            'notes' => Note::with('user')
                ->where('loan_id', $request->get('loan_id'))
                ->orderBy('id', 'desc')
                ->paginate(50),
        ]);
    }
}
