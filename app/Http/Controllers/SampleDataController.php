<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\EmailThread;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;

class SampleDataController extends Controller
{
    /**
     * Wipe every record flagged as sample data, the same way the
     * "Remove sample data" button behaves in the reference product.
     */
    public function __invoke(): RedirectResponse
    {
        Activity::where('is_sample', true)->delete();
        EmailThread::where('is_sample', true)->delete();
        Lead::where('is_sample', true)->delete();
        Deal::where('is_sample', true)->delete();
        Person::where('is_sample', true)->delete();
        Organization::where('is_sample', true)->delete();

        return back()->with('status', 'Sample data removed.');
    }
}
