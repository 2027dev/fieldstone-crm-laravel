<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
            'deal_id' => ['nullable', 'exists:deals,id'],
            'person_id' => ['nullable', 'exists:people,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
        ]);

        Note::create($data + ['user_id' => $request->user()->id]);

        return back()->with('status', 'Note added.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return back()->with('status', 'Note deleted.');
    }
}
