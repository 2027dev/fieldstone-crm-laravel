<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Person;
use App\Models\Pipeline;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InsightsController extends Controller
{
    public function index(Request $request): View
    {
        $months = max(3, min(12, $request->integer('months') ?: 6));
        $since = CarbonImmutable::now()->startOfMonth()->subMonths($months - 1);

        $pipeline = Pipeline::orderByDesc('is_default')->orderBy('position')->first();

        return view('insights.index', [
            'months' => $months,
            'scorecards' => $this->scorecards(),
            'stageFunnel' => $this->stageFunnel($pipeline),
            'statusSplit' => $this->statusSplit(),
            'topOrganizations' => $this->topOrganizations(),
            'monthlyPerformance' => $this->monthlyPerformance($since),
            'activityMix' => $this->activityMix(),
            'pipeline' => $pipeline,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function scorecards(): array
    {
        $open = Deal::open();
        $openValue = (float) Deal::open()->sum('value');
        $wonValue = (float) Deal::won()->sum('value');
        $wonCount = Deal::won()->count();
        $lostCount = Deal::lost()->count();
        $closed = $wonCount + $lostCount;

        return [
            'open_count' => $open->count(),
            'open_value' => $openValue,
            'won_value' => $wonValue,
            'won_count' => $wonCount,
            'win_rate' => $closed > 0 ? (int) round($wonCount / $closed * 100) : 0,
            'avg_deal' => $wonCount > 0 ? $wonValue / $wonCount : 0,
            'activities_due' => Activity::todo()->count(),
            'overdue' => Activity::overdue()->count(),
            'leads_inbox' => Lead::inbox()->count(),
            'contacts' => Person::count(),
        ];
    }

    /**
     * Deal count and weighted value per stage — the classic funnel report.
     *
     * @return array<int, array<string, mixed>>
     */
    private function stageFunnel(?Pipeline $pipeline): array
    {
        if (! $pipeline) {
            return [];
        }

        return $pipeline->stages()->get()->map(function ($stage) {
            $deals = Deal::where('stage_id', $stage->id)->where('status', 'open');

            return [
                'label' => $stage->name,
                'count' => $deals->count(),
                'value' => (float) $deals->sum('value'),
            ];
        })->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function statusSplit(): array
    {
        $rows = Deal::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(['won' => '#2f9e5e', 'open' => '#7d7ae8', 'lost' => '#d1435b'])
            ->map(fn ($color, $status) => [
                'label' => ucfirst($status),
                'value' => (int) ($rows[$status] ?? 0),
                'color' => $color,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function topOrganizations(): array
    {
        return Deal::query()
            ->join('organizations', 'organizations.id', '=', 'deals.organization_id')
            ->select('organizations.name', DB::raw('sum(deals.value) as total'))
            ->where('deals.status', '!=', 'lost')
            ->groupBy('organizations.id', 'organizations.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($row) => ['label' => $row->name, 'value' => (float) $row->total])
            ->all();
    }

    /**
     * Won vs. lost value per month for the stacked column report.
     *
     * @return array<int, array<string, mixed>>
     */
    private function monthlyPerformance(CarbonImmutable $since): array
    {
        $buckets = [];
        $cursor = $since;

        while ($cursor->lte(CarbonImmutable::now()->startOfMonth())) {
            $buckets[$cursor->format('Y-m')] = [
                'label' => $cursor->format('M'),
                'won' => 0.0,
                'open' => 0.0,
                'created' => 0,
            ];
            $cursor = $cursor->addMonth();
        }

        Deal::query()
            ->where('created_at', '>=', $since)
            ->get(['created_at', 'won_at', 'value', 'status'])
            ->each(function (Deal $deal) use (&$buckets) {
                $createdKey = $deal->created_at->format('Y-m');
                if (isset($buckets[$createdKey])) {
                    $buckets[$createdKey]['created']++;
                    if ($deal->status === 'open') {
                        $buckets[$createdKey]['open'] += (float) $deal->value;
                    }
                }

                if ($deal->status === 'won' && $deal->won_at) {
                    $wonKey = $deal->won_at->format('Y-m');
                    if (isset($buckets[$wonKey])) {
                        $buckets[$wonKey]['won'] += (float) $deal->value;
                    }
                }
            });

        return array_values($buckets);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function activityMix(): array
    {
        $rows = Activity::query()
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        return collect(Activity::TYPES)
            ->map(fn ($meta, $type) => [
                'label' => $meta['label'],
                'value' => (int) ($rows[$type] ?? 0),
            ])
            ->filter(fn ($row) => $row['value'] > 0)
            ->sortByDesc('value')
            ->values()
            ->all();
    }
}
