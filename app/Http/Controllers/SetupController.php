<?php

namespace App\Http\Controllers;

use App\Support\SetupGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('setup.index', [
            'groups' => SetupGuide::groups($user),
            'progress' => SetupGuide::progress($user),
        ]);
    }

    public function complete(Request $request, string $task): RedirectResponse
    {
        $request->user()->setupTasks()->updateOrCreate(
            ['task_key' => $task],
            ['completed_at' => now()],
        );

        return back()->with('status', 'Nice work — task marked as complete.');
    }

    public function uncomplete(Request $request, string $task): RedirectResponse
    {
        $request->user()->setupTasks()->where('task_key', $task)->delete();

        return back();
    }
}
