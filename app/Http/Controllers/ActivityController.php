<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Person;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $period = $request->string('period')->toString() ?: 'todo';

        $activities = $this->baseQuery($request, $type, $period)
            ->orderByRaw('due_date is null')
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->paginate(30)
            ->withQueryString();

        return view('activities.index', [
            'activities' => $activities,
            'type' => $type,
            'period' => $period,
            'counts' => $this->periodCounts(),
        ] + $this->formOptions());
    }

    public function calendar(Request $request): View
    {
        $anchor = CarbonImmutable::parse($request->string('week')->toString() ?: 'today')->startOfWeek();

        $activities = Activity::query()
            ->with(['person', 'deal'])
            ->whereBetween('due_date', [$anchor->toDateString(), $anchor->addDays(6)->toDateString()])
            ->orderBy('due_time')
            ->get()
            ->groupBy(fn (Activity $activity) => $activity->due_date->toDateString());

        return view('activities.calendar', [
            'anchor' => $anchor,
            'activities' => $activities,
        ] + $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Activity::create($data + ['owner_id' => $request->user()->id]);

        return back()->with('status', 'Activity scheduled.');
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $activity->update($this->validated($request));

        return back()->with('status', 'Activity updated.');
    }

    public function toggle(Activity $activity): RedirectResponse
    {
        $activity->update([
            'done' => ! $activity->done,
            'completed_at' => $activity->done ? null : now(),
        ]);

        return back()->with('status', $activity->done ? 'Activity marked as done.' : 'Activity moved back to to-do.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return back()->with('status', 'Activity deleted.');
    }

    private function baseQuery(Request $request, string $type, string $period)
    {
        $today = now()->startOfDay();

        return Activity::query()
            ->with(['person.organization', 'deal', 'organization', 'owner'])
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($request->filled('q'), fn ($query) => $query->whereLike('subject', '%'.$request->string('q')->toString().'%'))
            ->when($period === 'todo', fn ($query) => $query->where('done', false))
            ->when($period === 'done', fn ($query) => $query->where('done', true))
            ->when($period === 'overdue', fn ($query) => $query->where('done', false)->whereDate('due_date', '<', $today))
            ->when($period === 'today', fn ($query) => $query->whereDate('due_date', $today))
            ->when($period === 'tomorrow', fn ($query) => $query->whereDate('due_date', $today->copy()->addDay()))
            ->when($period === 'this-week', fn ($query) => $query->whereBetween('due_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]))
            ->when($period === 'next-week', fn ($query) => $query->whereBetween('due_date', [$today->copy()->addWeek()->startOfWeek(), $today->copy()->addWeek()->endOfWeek()]));
    }

    /**
     * @return array<string, int>
     */
    private function periodCounts(): array
    {
        $today = now()->startOfDay();

        return [
            'todo' => Activity::where('done', false)->count(),
            'overdue' => Activity::where('done', false)->whereDate('due_date', '<', $today)->count(),
            'today' => Activity::whereDate('due_date', $today)->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'people' => Person::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::open()->orderBy('title')->get(['id', 'title']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'subject' => ['required', 'string', 'max:190'],
            'type' => ['required', 'in:'.implode(',', array_keys(Activity::TYPES))],
            'due_date' => ['nullable', 'date'],
            'due_time' => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'person_id' => ['nullable', 'exists:people,id'],
            'deal_id' => ['nullable', 'exists:deals,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'location' => ['nullable', 'string', 'max:190'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'outcome' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
