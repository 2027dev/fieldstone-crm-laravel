<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DuplicateController extends Controller
{
    /**
     * Group contacts that look like duplicates of each other by normalised
     * name or shared email address.
     */
    public function index(): View
    {
        $people = Person::with('organization')->orderBy('name')->get();

        $groups = $people
            ->groupBy(fn (Person $person) => Str::of($person->email ?: $person->display_name)->lower()->squish()->toString())
            ->filter(fn ($group) => $group->count() > 1)
            ->values();

        $organizationGroups = Organization::orderBy('name')->get()
            ->groupBy(fn (Organization $organization) => Str::of($organization->name)->lower()->replaceMatches('/[^a-z0-9]/', '')->toString())
            ->filter(fn ($group) => $group->count() > 1)
            ->values();

        return view('contacts.duplicates', [
            'groups' => $groups,
            'organizationGroups' => $organizationGroups,
        ]);
    }

    /**
     * Merge the losing records into the winner, re-pointing their relations.
     */
    public function merge(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'keep_id' => ['required', 'exists:people,id'],
            'merge_ids' => ['required', 'array', 'min:1'],
            'merge_ids.*' => ['exists:people,id'],
        ]);

        $keep = Person::findOrFail($data['keep_id']);
        $losers = Person::whereIn('id', $data['merge_ids'])->whereKeyNot($keep->id)->get();

        foreach ($losers as $loser) {
            $loser->deals()->update(['person_id' => $keep->id]);
            $loser->activities()->update(['person_id' => $keep->id]);
            $loser->leads()->update(['person_id' => $keep->id]);
            $loser->notes()->update(['person_id' => $keep->id]);
            $loser->emailThreads()->update(['person_id' => $keep->id]);

            $keep->fill(array_filter([
                'email' => $keep->email ?: $loser->email,
                'phone' => $keep->phone ?: $loser->phone,
                'job_title' => $keep->job_title ?: $loser->job_title,
                'organization_id' => $keep->organization_id ?: $loser->organization_id,
            ]));

            $loser->delete();
        }

        $keep->save();

        return back()->with('status', "Merged {$losers->count()} duplicate record(s) into {$keep->display_name}.");
    }
}
