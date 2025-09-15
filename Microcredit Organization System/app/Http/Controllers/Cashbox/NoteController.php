<?php

namespace App\Http\Controllers\Cashbox;

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
        return view('cashbox.note.index',[
            'notes' => Note::with('user')
                ->where('loan_id', $request->get('loan_id'))
                ->orderBy('id', 'desc')
                ->paginate(50),
        ]);
    }

    public function create() {
        return view('cashbox.note.create');
    }

    public function store(CashboxNoteStoreRequest $request) {
        $validated = $request->validated();
        Loan::where('id', $request->get('loan_id'))
            ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
            ->firstOrFail();

        $note = new Note();
        $note->company_id = Auth::user()->company_id;
        $note->user_id = Auth::user()->id;
        $note->loan_id = $request->get('loan_id');
        $note->text = addslashes($validated['text']);

        if($note->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось сохранить запись'
            ])->withInput();
        }

        return redirect('/notes?loan_id=' . $request->get('loan_id'));
    }

    public function edit(Request $request, Note $note) {
        return view('cashbox.note.edit', [
            'note' => $note
        ]);
    }

    public function update(CashboxNoteUpdateRequest $request, Note $note) {
        $validated = $request->validated();

        $note->text = addslashes($validated['text']);

        if($note->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось сохранить запись'
            ])->withInput();
        }

        return redirect('/notes?loan_id=' . $note->loan_id);
    }
}
