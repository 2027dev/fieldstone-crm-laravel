<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\EmailThread;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Support\Carbon;

class DemoSeeder
{
    /**
     * The default sales process every new workspace starts with.
     *
     * @var array<int, array{name:string, probability:int}>
     */
    public const DEFAULT_STAGES = [
        ['name' => 'Qualified', 'probability' => 20],
        ['name' => 'Contact Made', 'probability' => 40],
        ['name' => 'Demo Scheduled', 'probability' => 60],
        ['name' => 'Proposal Made', 'probability' => 80],
        ['name' => 'Negotiations Started', 'probability' => 90],
    ];

    public static function ensurePipeline(): Pipeline
    {
        $pipeline = Pipeline::firstOrCreate(
            ['name' => 'Sales Pipeline'],
            ['is_default' => true, 'position' => 0],
        );

        if ($pipeline->stages()->doesntExist()) {
            foreach (self::DEFAULT_STAGES as $index => $stage) {
                $pipeline->stages()->create($stage + ['position' => $index]);
            }
        }

        return $pipeline->refresh();
    }

    /**
     * Seed the small "[Sample]" dataset that ships with a fresh workspace.
     */
    public static function seedSampleData(User $user): void
    {
        $pipeline = self::ensurePipeline();

        if (Person::where('is_sample', true)->exists()) {
            return;
        }

        $stages = $pipeline->stages()->get();

        $organization = Organization::create([
            'owner_id' => $user->id,
            'name' => '[Sample] MoveEr',
            'address' => '1200 Harbour Way, Portland, OR',
            'industry' => 'Logistics',
            'employee_count' => 48,
            'is_sample' => true,
        ]);

        $benjamin = Person::create([
            'owner_id' => $user->id,
            'name' => '[Sample] Benjamin Leon',
            'job_title' => 'Operations Lead',
            'email' => 'benjamin.leon@gmial.com',
            'phone' => '785-202-7824',
            'is_sample' => true,
        ]);

        $tony = Person::create([
            'owner_id' => $user->id,
            'organization_id' => $organization->id,
            'name' => '[Sample] Tony Turner',
            'job_title' => 'Head of Fleet',
            'email' => 'tony.turner@moveer.com',
            'phone' => '218-348-8528',
            'is_sample' => true,
        ]);

        $tonyDeal = Deal::create([
            'owner_id' => $user->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stages[1]->id,
            'person_id' => $tony->id,
            'organization_id' => $organization->id,
            'title' => '[Sample] Tony Turner',
            'value' => 4200,
            'expected_close_date' => Carbon::now()->addWeeks(3)->toDateString(),
            'position' => 1,
            'is_sample' => true,
        ]);

        Activity::create([
            'owner_id' => $user->id,
            'person_id' => $benjamin->id,
            'subject' => '[Sample] Final attempt',
            'type' => 'call',
            'due_date' => Carbon::now()->addDay()->toDateString(),
            'due_time' => '09:30',
            'duration_minutes' => 30,
            'is_sample' => true,
        ]);

        Activity::create([
            'owner_id' => $user->id,
            'person_id' => $tony->id,
            'deal_id' => $tonyDeal->id,
            'subject' => '[Sample] Context call',
            'type' => 'call',
            'due_date' => Carbon::now()->addDays(2)->toDateString(),
            'due_time' => '14:00',
            'duration_minutes' => 45,
            'is_sample' => true,
        ]);

        $thread = EmailThread::create([
            'user_id' => $user->id,
            'person_id' => $tony->id,
            'deal_id' => $tonyDeal->id,
            'subject' => '[Sample] Fleet upgrade — pricing question',
            'folder' => 'inbox',
            'is_read' => false,
            'last_message_at' => Carbon::now()->subHours(5),
            'is_sample' => true,
        ]);

        $thread->messages()->create([
            'direction' => 'incoming',
            'from_name' => 'Tony Turner',
            'from_email' => 'tony.turner@moveer.com',
            'to_email' => $user->email,
            'body' => "Hi,\n\nThanks for the walkthrough last week. Could you send over pricing for 25 seats, plus what onboarding looks like?\n\nBest,\nTony",
            'sent_at' => Carbon::now()->subHours(5),
        ]);
    }
}
