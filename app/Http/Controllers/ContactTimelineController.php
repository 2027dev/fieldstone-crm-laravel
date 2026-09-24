<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactTimelineController extends Controller
{
    /**
     * A month-by-month grid of every contact's activity, mirroring the
     * "Contacts timeline" view.
     */
    public function __invoke(Request $request): View
    {
        $anchor = CarbonImmutable::parse($request->string('month')->toString() ?: 'today')->startOfMonth();

        $people = Person::query()
            ->with([
                'organization',
                'activities' => fn ($query) => $query
                    ->whereBetween('due_date', [$anchor->toDateString(), $anchor->endOfMonth()->toDateString()])
                    ->orderBy('due_date'),
            ])
            ->orderBy('name')
            ->get();

        return view('contacts.timeline', [
            'anchor' => $anchor,
            'people' => $people,
            'days' => range(1, $anchor->daysInMonth),
        ]);
    }
}
