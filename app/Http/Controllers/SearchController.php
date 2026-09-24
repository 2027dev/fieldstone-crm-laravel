<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $term = trim($request->string('q')->toString());
        $like = '%'.$term.'%';

        $results = [
            'people' => collect(),
            'organizations' => collect(),
            'deals' => collect(),
            'leads' => collect(),
            'activities' => collect(),
        ];

        if ($term !== '') {
            $results['people'] = Person::with('organization')
                ->where(fn ($q) => $q->whereLike('name', $like)->orWhereLike('email', $like)->orWhereLike('phone', $like))
                ->limit(10)->get();

            $results['organizations'] = Organization::whereLike('name', $like)->limit(10)->get();

            $results['deals'] = Deal::with(['person', 'stage'])->whereLike('title', $like)->limit(10)->get();

            $results['leads'] = Lead::with('person')->whereLike('title', $like)->limit(10)->get();

            $results['activities'] = Activity::with('person')->whereLike('subject', $like)->limit(10)->get();
        }

        return view('search', [
            'term' => $term,
            'results' => $results,
            'total' => collect($results)->sum(fn ($items) => $items->count()),
        ]);
    }
}
