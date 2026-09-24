<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Pipeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $view = $request->string('view')->toString() ?: 'inbox';

        $leads = Lead::query()
            ->with(['person', 'organization', 'owner'])
            ->withCount(['activities as open_activities_count' => fn ($query) => $query->where('done', false)])
            ->when($view === 'inbox', fn ($query) => $query->inbox())
            ->when($view === 'archived', fn ($query) => $query->archived())
            ->when($view === 'converted', fn ($query) => $query->whereNotNull('converted_deal_id'))
            ->when($request->filled('q'), fn ($query) => $query->whereLike('title', '%'.$request->string('q')->toString().'%'))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('leads.index', [
            'leads' => $leads,
            'view' => $view,
            'counts' => [
                'inbox' => Lead::inbox()->count(),
                'archived' => Lead::archived()->count(),
                'converted' => Lead::whereNotNull('converted_deal_id')->count(),
            ],
        ] + $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        Lead::create($this->validated($request) + ['owner_id' => $request->user()->id]);

        return back()->with('status', 'Lead added to your inbox.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['person.organization', 'organization', 'owner', 'notes.user', 'activities', 'convertedDeal']);

        return view('leads.show', ['lead' => $lead] + $this->formOptions());
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $lead->update($this->validated($request));

        return back()->with('status', 'Lead updated.');
    }

    public function archive(Lead $lead): RedirectResponse
    {
        $lead->update(['archived_at' => $lead->archived_at ? null : now()]);

        return back()->with('status', $lead->archived_at ? 'Lead archived.' : 'Lead restored to your inbox.');
    }

    public function convert(Request $request, Lead $lead): RedirectResponse
    {
        if ($lead->converted_deal_id) {
            return redirect()->route('deals.show', $lead->converted_deal_id);
        }

        $pipeline = Pipeline::orderByDesc('is_default')->orderBy('position')->firstOrFail();
        $stage = $pipeline->stages()->first();

        $deal = Deal::create([
            'owner_id' => $lead->owner_id ?? $request->user()->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
            'person_id' => $lead->person_id,
            'organization_id' => $lead->organization_id,
            'title' => $lead->title,
            'value' => $lead->value ?? 0,
            'currency' => $lead->currency,
            'label' => $lead->label,
            'position' => (Deal::where('stage_id', $stage->id)->max('position') ?? 0) + 1,
        ]);

        $lead->update(['converted_deal_id' => $deal->id, 'archived_at' => null]);
        $lead->activities()->update(['deal_id' => $deal->id]);

        return redirect()->route('deals.show', $deal)->with('status', 'Lead converted into a deal.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('status', 'Lead deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'person_id' => ['nullable', 'exists:people,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'value' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'label' => ['nullable', 'string', 'max:40'],
            'source' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
