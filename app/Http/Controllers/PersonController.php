<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();

        $people = Person::query()
            ->with(['organization', 'owner'])
            ->withCount([
                'activities',
                'deals as closed_deals_count' => fn ($query) => $query->whereIn('status', ['won', 'lost']),
                'deals as open_deals_count' => fn ($query) => $query->where('status', 'open'),
            ])
            ->withSum(['deals as won_value' => fn ($query) => $query->where('status', 'won')], 'value')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($q) => $q->whereLike('name', $term)
                    ->orWhereLike('email', $term)
                    ->orWhereLike('phone', $term));
            })
            ->when($filter === 'with-activities', fn ($query) => $query->has('activities'))
            ->when($filter === 'no-activities', fn ($query) => $query->doesntHave('activities'))
            ->when($filter === 'open-deals', fn ($query) => $query->whereHas('deals', fn ($q) => $q->where('status', 'open')))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('contacts.people', [
            'people' => $people,
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'filter' => $filter,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $person = Person::create($data + ['owner_id' => $request->user()->id]);

        return redirect()
            ->route('people.show', $person)
            ->with('status', "{$person->display_name} was added to your contacts.");
    }

    public function show(Person $person): View
    {
        $person->load([
            'organization',
            'owner',
            'deals.stage',
            'notes.user',
            'activities' => fn ($query) => $query->orderByDesc('due_date'),
            'emailThreads.latestMessage',
        ]);

        return view('contacts.person-show', [
            'person' => $person,
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Person $person): RedirectResponse
    {
        $person->update($this->validated($request));

        return back()->with('status', 'Contact updated.');
    }

    public function destroy(Person $person): RedirectResponse
    {
        $name = $person->display_name;
        $person->delete();

        return redirect()->route('people.index')->with('status', "{$name} was deleted.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'email_label' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:40'],
            'phone_label' => ['nullable', 'string', 'max:20'],
            'label' => ['nullable', 'string', 'max:40'],
        ]);
    }
}
