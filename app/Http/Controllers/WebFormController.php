<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebFormController extends Controller
{
    /**
     * The in-app settings screen describing the public capture form.
     */
    public function settings(): View
    {
        return view('leads.web-forms', [
            'publicUrl' => route('web-form.show'),
            'submissions' => Lead::where('source', 'Web form')->latest()->limit(10)->get(),
        ]);
    }

    /**
     * The public, unauthenticated lead capture form.
     */
    public function show(): View
    {
        return view('web-form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:160'],
            'budget' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $owner = User::orderBy('id')->first();

        $organization = filled($data['company'] ?? null)
            ? Organization::firstOrCreate(['name' => $data['company']], ['owner_id' => $owner?->id])
            : null;

        $person = Person::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'organization_id' => $organization?->id,
                'owner_id' => $owner?->id,
            ],
        );

        Lead::create([
            'owner_id' => $owner?->id,
            'person_id' => $person->id,
            'organization_id' => $organization?->id,
            'title' => $data['company'] ? "Web enquiry — {$data['company']}" : "Web enquiry — {$data['name']}",
            'value' => $data['budget'] ?? null,
            'source' => 'Web form',
            'label' => 'New',
            'note' => $data['message'] ?? null,
        ]);

        return back()->with('status', 'Thanks — your enquiry is on its way to the sales team.');
    }
}
