<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use App\Models\User;

class SetupGuide
{
    /**
     * Groups of suggested onboarding tasks, mirroring the CRM setup guide.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function groups(User $user): array
    {
        $manual = $user->setupTasks()->whereNotNull('completed_at')->pluck('task_key')->all();

        $done = fn (string $key, bool $auto) => $auto || in_array($key, $manual, true);

        return [
            [
                'key' => 'basics',
                'title' => 'Cover the basics',
                'tasks' => [
                    [
                        'key' => 'add_contact',
                        'title' => 'Add a contact',
                        'duration' => '1-2 min',
                        'description' => 'Enter details about a person or company so their deals, emails and activities can be linked.',
                        'cta' => 'Add contact',
                        'route' => route('people.index', ['new' => 1]),
                        'icon' => 'contacts',
                        'done' => $done('add_contact', Person::where('is_sample', false)->exists() || Organization::where('is_sample', false)->exists()),
                    ],
                    [
                        'key' => 'schedule_activity',
                        'title' => 'Schedule an activity',
                        'duration' => '1-2 min',
                        'description' => 'Arrange the details of a call, meeting or task to advance a deal.',
                        'cta' => 'Schedule activity',
                        'route' => route('activities.index', ['new' => 1]),
                        'icon' => 'activities',
                        'done' => $done('schedule_activity', Activity::where('is_sample', false)->exists()),
                    ],
                    [
                        'key' => 'add_deal',
                        'title' => 'Add a deal',
                        'duration' => '2-4 min',
                        'description' => 'Create an opportunity to move it through your sales process and close faster.',
                        'cta' => 'Add deal',
                        'route' => route('deals.index', ['new' => 1]),
                        'icon' => 'deals',
                        'done' => $done('add_deal', Deal::where('is_sample', false)->exists()),
                    ],
                ],
            ],
            [
                'key' => 'pipeline',
                'title' => 'Build your pipeline',
                'tasks' => [
                    [
                        'key' => 'add_lead',
                        'title' => 'Capture a lead',
                        'duration' => '1-2 min',
                        'description' => 'Park incoming opportunities in the Leads Inbox and qualify them before they reach your pipeline.',
                        'cta' => 'Add lead',
                        'route' => route('leads.index', ['new' => 1]),
                        'icon' => 'leads',
                        'done' => $done('add_lead', Lead::where('is_sample', false)->exists()),
                    ],
                    [
                        'key' => 'win_deal',
                        'title' => 'Mark a deal as won',
                        'duration' => '1 min',
                        'description' => 'Close your first opportunity so revenue starts flowing into your Insights dashboard.',
                        'cta' => 'Go to deals',
                        'route' => route('deals.index'),
                        'icon' => 'deals',
                        'done' => $done('win_deal', Deal::where('status', 'won')->exists()),
                    ],
                    [
                        'key' => 'view_insights',
                        'title' => 'Review your Insights',
                        'duration' => '2 min',
                        'description' => 'Track conversion, revenue and activity performance from a single reporting dashboard.',
                        'cta' => 'Open Insights',
                        'route' => route('insights.index'),
                        'icon' => 'insights',
                        'done' => $done('view_insights', false),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{completed:int,total:int,percent:int}
     */
    public static function progress(User $user): array
    {
        $tasks = collect(static::groups($user))->flatMap(fn ($group) => $group['tasks']);

        // The account itself counts as a completed suggested task.
        $total = $tasks->count() + 1;
        $completed = $tasks->where('done', true)->count() + 1;

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => (int) round($completed / max($total, 1) * 100),
        ];
    }

    public static function remaining(User $user): int
    {
        $progress = static::progress($user);

        return max($progress['total'] - $progress['completed'], 0);
    }
}
