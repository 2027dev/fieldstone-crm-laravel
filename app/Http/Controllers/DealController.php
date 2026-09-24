<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DealController extends Controller
{
    public function index(Request $request): View
    {
        $pipeline = $this->currentPipeline($request);

        $stages = $pipeline->stages()->get();

        $deals = Deal::query()
            ->with(['person', 'organization', 'owner'])
            ->withCount(['activities as open_activities_count' => fn ($query) => $query->where('done', false)])
            ->where('pipeline_id', $pipeline->id)
            ->where('status', 'open')
            ->when($request->filled('q'), fn ($query) => $query->whereLike('title', '%'.$request->string('q')->toString().'%'))
            ->orderBy('position')
            ->orderByDesc('value')
            ->get()
            ->groupBy('stage_id');

        return view('deals.index', [
            'pipeline' => $pipeline,
            'pipelines' => Pipeline::orderBy('position')->get(),
            'stages' => $stages,
            'dealsByStage' => $deals,
        ] + $this->formOptions($pipeline));
    }

    public function list(Request $request): View
    {
        $pipeline = $this->currentPipeline($request);
        $status = $request->string('status')->toString() ?: 'open';

        $deals = Deal::query()
            ->with(['person', 'organization', 'stage', 'owner'])
            ->where('pipeline_id', $pipeline->id)
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($request->filled('q'), fn ($query) => $query->whereLike('title', '%'.$request->string('q')->toString().'%'))
            ->orderByDesc('updated_at')
            ->paginate(30)
            ->withQueryString();

        return view('deals.list', [
            'pipeline' => $pipeline,
            'pipelines' => Pipeline::orderBy('position')->get(),
            'deals' => $deals,
            'status' => $status,
        ] + $this->formOptions($pipeline));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $stage = Stage::findOrFail($data['stage_id']);

        $deal = Deal::create($data + [
            'pipeline_id' => $stage->pipeline_id,
            'owner_id' => $request->user()->id,
            'currency' => $request->user()->default_currency ?? 'USD',
            'position' => (Deal::where('stage_id', $stage->id)->max('position') ?? 0) + 1,
        ]);

        return redirect()->route('deals.show', $deal)->with('status', 'Deal created.');
    }

    public function show(Deal $deal): View
    {
        $deal->load([
            'person.organization',
            'organization',
            'stage.pipeline.stages',
            'owner',
            'notes.user',
            'activities' => fn ($query) => $query->orderBy('done')->orderBy('due_date'),
        ]);

        return view('deals.show', [
            'deal' => $deal,
            'stages' => $deal->stage->pipeline->stages,
        ] + $this->formOptions($deal->stage->pipeline));
    }

    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $data = $this->validated($request);

        if (($data['stage_id'] ?? null) && $data['stage_id'] !== $deal->stage_id) {
            $data['pipeline_id'] = Stage::findOrFail($data['stage_id'])->pipeline_id;
        }

        $deal->update($data);

        return back()->with('status', 'Deal updated.');
    }

    public function move(Request $request, Deal $deal)
    {
        $data = $request->validate([
            'stage_id' => ['required', 'exists:stages,id'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $stage = Stage::findOrFail($data['stage_id']);

        $deal->update([
            'stage_id' => $stage->id,
            'pipeline_id' => $stage->pipeline_id,
            'position' => $data['position'] ?? ((Deal::where('stage_id', $stage->id)->max('position') ?? 0) + 1),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'stage' => $stage->name]);
        }

        return back();
    }

    public function status(Request $request, Deal $deal): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,won,lost'],
            'lost_reason' => ['nullable', 'string', 'max:190'],
        ]);

        $deal->update([
            'status' => $data['status'],
            'won_at' => $data['status'] === 'won' ? now() : null,
            'lost_at' => $data['status'] === 'lost' ? now() : null,
            'lost_reason' => $data['status'] === 'lost' ? ($data['lost_reason'] ?? null) : null,
        ]);

        return back()->with('status', match ($data['status']) {
            'won' => 'Deal marked as won. 🎉',
            'lost' => 'Deal marked as lost.',
            default => 'Deal reopened.',
        });
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $deal->delete();

        return redirect()->route('deals.index')->with('status', 'Deal deleted.');
    }

    private function currentPipeline(Request $request): Pipeline
    {
        return Pipeline::query()
            ->when($request->filled('pipeline'), fn ($query) => $query->whereKey($request->integer('pipeline')))
            ->orderByDesc('is_default')
            ->orderBy('position')
            ->firstOrFail();
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(Pipeline $pipeline): array
    {
        return [
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'stageOptions' => $pipeline->stages()->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'value' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'stage_id' => ['required', 'exists:stages,id'],
            'person_id' => ['nullable', 'exists:people,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'expected_close_date' => ['nullable', 'date'],
            'label' => ['nullable', 'string', 'max:40'],
        ]);
    }
}
