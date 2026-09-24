<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $organizations = Organization::query()
            ->with('owner')
            ->withCount([
                'people',
                'deals as open_deals_count' => fn ($query) => $query->where('status', 'open'),
                'deals as closed_deals_count' => fn ($query) => $query->whereIn('status', ['won', 'lost']),
            ])
            ->withSum(['deals as won_value' => fn ($query) => $query->where('status', 'won')], 'value')
            ->when($request->filled('q'), fn ($query) => $query->whereLike('name', '%'.$request->string('q')->toString().'%'))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('contacts.organizations', ['organizations' => $organizations]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = Organization::create(
            $this->validated($request) + ['owner_id' => $request->user()->id]
        );

        return redirect()
            ->route('organizations.show', $organization)
            ->with('status', "{$organization->name} was added.");
    }

    public function show(Organization $organization): View
    {
        $organization->load([
            'owner',
            'people',
            'deals.stage',
            'notes.user',
            'activities' => fn ($query) => $query->orderByDesc('due_date'),
        ]);

        return view('contacts.organization-show', ['organization' => $organization]);
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $organization->update($this->validated($request));

        return back()->with('status', 'Organization updated.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $name = $organization->name;
        $organization->delete();

        return redirect()->route('organizations.index')->with('status', "{$name} was deleted.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:190'],
            'industry' => ['nullable', 'string', 'max:120'],
            'employee_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'label' => ['nullable', 'string', 'max:40'],
        ]);
    }
}
