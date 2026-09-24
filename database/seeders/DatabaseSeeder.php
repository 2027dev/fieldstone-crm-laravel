<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\EmailThread;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Organization;
use App\Models\Person;
use App\Models\User;
use App\Support\DemoSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(20260924);

        $user = User::firstOrCreate(
            ['email' => 'demo@fieldstonecrm.test'],
            [
                'name' => 'Avery Sinclair',
                'password' => 'password',
                'company_name' => 'Fieldstone Partners',
                'job_title' => 'Head of Sales',
            ],
        );

        $pipeline = DemoSeeder::ensurePipeline();
        $stages = $pipeline->stages()->get();

        if (Deal::where('is_sample', false)->exists()) {
            return;
        }

        DemoSeeder::seedSampleData($user);

        $organizations = collect($this->organizationData())->map(fn (array $row) => Organization::create($row + [
            'owner_id' => $user->id,
        ]));

        $people = collect($this->personData())->map(function (array $row) use ($organizations, $user) {
            $organization = $organizations->firstWhere('name', $row['organization']);
            unset($row['organization']);

            return Person::create($row + [
                'owner_id' => $user->id,
                'organization_id' => $organization?->id,
            ]);
        });

        $deals = collect($this->dealData())->map(function (array $row, int $index) use ($people, $pipeline, $stages, $user) {
            $person = $people->firstWhere('name', $row['person']);
            unset($row['person']);

            $createdAt = Carbon::now()->subDays($row['age_days']);
            unset($row['age_days']);

            $status = $row['status'];
            $stage = $stages[min($row['stage'], $stages->count() - 1)];
            unset($row['stage']);

            return Deal::create($row + [
                'owner_id' => $user->id,
                'pipeline_id' => $pipeline->id,
                'stage_id' => $stage->id,
                'person_id' => $person?->id,
                'organization_id' => $person?->organization_id,
                'position' => $index,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(mt_rand(0, 12)),
                'won_at' => $status === 'won' ? $createdAt->copy()->addDays(mt_rand(8, 40)) : null,
                'lost_at' => $status === 'lost' ? $createdAt->copy()->addDays(mt_rand(8, 40)) : null,
                'expected_close_date' => $createdAt->copy()->addDays(mt_rand(10, 60))->toDateString(),
            ]);
        });

        $this->seedActivities($user, $deals, $people);
        $this->seedLeads($user, $people, $organizations);
        $this->seedInbox($user, $people, $deals);
        $this->seedNotes($user, $deals);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function organizationData(): array
    {
        return [
            ['name' => 'Northwind Logistics', 'industry' => 'Logistics', 'employee_count' => 320, 'address' => '44 Dockside Rd, Seattle, WA', 'website' => 'northwind-logistics.com'],
            ['name' => 'Harbor & Vale', 'industry' => 'Professional Services', 'employee_count' => 85, 'address' => '9 Kingsway, Boston, MA', 'website' => 'harborvale.com'],
            ['name' => 'Cobalt Health', 'industry' => 'Healthcare', 'employee_count' => 1400, 'address' => '2100 Medical Center Dr, Austin, TX', 'website' => 'cobalthealth.io'],
            ['name' => 'Larkspur Foods', 'industry' => 'Food & Beverage', 'employee_count' => 210, 'address' => '77 Orchard Ln, Sacramento, CA', 'website' => 'larkspurfoods.com'],
            ['name' => 'Meridian Build', 'industry' => 'Construction', 'employee_count' => 640, 'address' => '15 Steelworks Ave, Pittsburgh, PA', 'website' => 'meridianbuild.com'],
            ['name' => 'Foxglove Studio', 'industry' => 'Design', 'employee_count' => 24, 'address' => '3 Hoxton Sq, Brooklyn, NY', 'website' => 'foxglove.studio'],
            ['name' => 'Ironbark Energy', 'industry' => 'Energy', 'employee_count' => 980, 'address' => '500 Turbine Way, Denver, CO', 'website' => 'ironbark.energy'],
            ['name' => 'Pennwick Retail Group', 'industry' => 'Retail', 'employee_count' => 2300, 'address' => '1 Market Plaza, Chicago, IL', 'website' => 'pennwick.com'],
            ['name' => 'Salt & Cedar', 'industry' => 'Hospitality', 'employee_count' => 60, 'address' => '212 Bayfront, Charleston, SC', 'website' => 'saltandcedar.co'],
            ['name' => 'Quillon Analytics', 'industry' => 'Software', 'employee_count' => 145, 'address' => '400 Innovation Pkwy, Raleigh, NC', 'website' => 'quillon.ai'],
            ['name' => 'Beacon Freight', 'industry' => 'Logistics', 'employee_count' => 410, 'address' => '88 Rail Yard Rd, Kansas City, MO', 'website' => 'beaconfreight.com'],
            ['name' => 'Thornfield Legal', 'industry' => 'Legal', 'employee_count' => 130, 'address' => '60 Chancery St, New York, NY', 'website' => 'thornfieldlegal.com'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function personData(): array
    {
        $rows = [
            ['Marta Okonkwo', 'VP Operations', 'Northwind Logistics', 'marta.okonkwo@northwind-logistics.com', '206-555-0141'],
            ['Devin Halloway', 'Procurement Manager', 'Northwind Logistics', 'devin.halloway@northwind-logistics.com', '206-555-0187'],
            ['Priya Raghunathan', 'Managing Partner', 'Harbor & Vale', 'priya.r@harborvale.com', '617-555-0110'],
            ['Colin Speers', 'Director of IT', 'Cobalt Health', 'c.speers@cobalthealth.io', '512-555-0163'],
            ['Nadia Brightwater', 'Chief of Staff', 'Cobalt Health', 'nadia.b@cobalthealth.io', '512-555-0198'],
            ['Tomas Avellan', 'Supply Chain Lead', 'Larkspur Foods', 'tavellan@larkspurfoods.com', '916-555-0122'],
            ['Greer Mackintosh', 'Regional Manager', 'Larkspur Foods', 'greer.m@larkspurfoods.com', '916-555-0174'],
            ['Silas Whitcombe', 'Project Director', 'Meridian Build', 'swhitcombe@meridianbuild.com', '412-555-0155'],
            ['Imani Fairbrook', 'Creative Director', 'Foxglove Studio', 'imani@foxglove.studio', '718-555-0139'],
            ['Rafael Demarco', 'Head of Field Ops', 'Ironbark Energy', 'r.demarco@ironbark.energy', '303-555-0177'],
            ['Wren Tallowmere', 'Sustainability Lead', 'Ironbark Energy', 'wren.t@ironbark.energy', '303-555-0119'],
            ['Beatrice Lunden', 'Merchandising VP', 'Pennwick Retail Group', 'blunden@pennwick.com', '312-555-0168'],
            ['Oscar Nyberg', 'Store Systems Manager', 'Pennwick Retail Group', 'onyberg@pennwick.com', '312-555-0193'],
            ['Juniper Cass', 'General Manager', 'Salt & Cedar', 'juniper@saltandcedar.co', '843-555-0146'],
            ['Hollis Grayfield', 'VP Engineering', 'Quillon Analytics', 'hollis@quillon.ai', '919-555-0131'],
            ['Tamsin Rourke', 'Revenue Operations', 'Quillon Analytics', 'tamsin@quillon.ai', '919-555-0184'],
            ['Emeka Bartholomew', 'Dispatch Director', 'Beacon Freight', 'emeka.b@beaconfreight.com', '816-555-0157'],
            ['Lucinda Pell', 'Practice Manager', 'Thornfield Legal', 'lpell@thornfieldlegal.com', '212-555-0102'],
            ['Arthur Vane', 'Senior Partner', 'Thornfield Legal', 'avane@thornfieldlegal.com', '212-555-0128'],
            ['Saoirse Quill', 'Independent Consultant', null, 'saoirse.quill@protonmail.com', '415-555-0166'],
        ];

        return array_map(fn (array $row) => [
            'name' => $row[0],
            'job_title' => $row[1],
            'organization' => $row[2],
            'email' => $row[3],
            'phone' => $row[4],
        ], $rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function dealData(): array
    {
        $rows = [
            ['Northwind — fleet telematics rollout', 'Marta Okonkwo', 86000, 4, 'open', 12],
            ['Northwind — warehouse scanner refresh', 'Devin Halloway', 24500, 1, 'open', 5],
            ['Harbor & Vale — client portal', 'Priya Raghunathan', 41000, 2, 'open', 21],
            ['Cobalt Health — scheduling platform', 'Colin Speers', 154000, 3, 'open', 34],
            ['Cobalt Health — staff onboarding suite', 'Nadia Brightwater', 62000, 0, 'open', 3],
            ['Larkspur — cold chain monitoring', 'Tomas Avellan', 73500, 2, 'open', 44],
            ['Larkspur — regional expansion tooling', 'Greer Mackintosh', 38000, 1, 'open', 9],
            ['Meridian — site safety reporting', 'Silas Whitcombe', 95000, 3, 'open', 58],
            ['Foxglove — studio CRM starter', 'Imani Fairbrook', 8400, 0, 'open', 2],
            ['Ironbark — field crew dispatch', 'Rafael Demarco', 128000, 4, 'open', 40],
            ['Ironbark — emissions reporting add-on', 'Wren Tallowmere', 46000, 1, 'open', 16],
            ['Pennwick — omnichannel inventory', 'Beatrice Lunden', 210000, 3, 'open', 66],
            ['Pennwick — POS integration', 'Oscar Nyberg', 57000, 2, 'open', 27],
            ['Salt & Cedar — booking automation', 'Juniper Cass', 15600, 1, 'open', 7],
            ['Quillon — enterprise tier upgrade', 'Hollis Grayfield', 88000, 4, 'open', 19],
            ['Quillon — revops consulting block', 'Tamsin Rourke', 22000, 0, 'open', 4],
            ['Beacon — dispatch optimisation', 'Emeka Bartholomew', 67000, 2, 'open', 31],
            ['Thornfield — matter intake workflow', 'Lucinda Pell', 34000, 1, 'open', 11],

            ['Northwind — pilot programme', 'Marta Okonkwo', 19000, 4, 'won', 150],
            ['Harbor & Vale — discovery engagement', 'Priya Raghunathan', 12500, 4, 'won', 132],
            ['Cobalt Health — department pilot', 'Colin Speers', 44000, 4, 'won', 118],
            ['Larkspur — depot rollout phase 1', 'Tomas Avellan', 58000, 4, 'won', 96],
            ['Meridian — annual licence', 'Silas Whitcombe', 76000, 4, 'won', 84],
            ['Ironbark — regional trial', 'Rafael Demarco', 31000, 4, 'won', 71],
            ['Pennwick — flagship store pilot', 'Beatrice Lunden', 92000, 4, 'won', 63],
            ['Quillon — team plan', 'Tamsin Rourke', 14000, 4, 'won', 52],
            ['Salt & Cedar — front desk tablets', 'Juniper Cass', 9800, 4, 'won', 41],
            ['Beacon — driver app licences', 'Emeka Bartholomew', 27500, 4, 'won', 29],
            ['Thornfield — partner seats', 'Arthur Vane', 18400, 4, 'won', 17],
            ['Foxglove — retainer', 'Imani Fairbrook', 6200, 4, 'won', 8],

            ['Pennwick — legacy migration', 'Oscar Nyberg', 145000, 3, 'lost', 140],
            ['Cobalt Health — records archive', 'Nadia Brightwater', 68000, 2, 'lost', 108],
            ['Ironbark — turbine analytics', 'Wren Tallowmere', 112000, 3, 'lost', 77],
            ['Larkspur — packaging traceability', 'Greer Mackintosh', 43000, 1, 'lost', 55],
            ['Independent — advisory retainer', 'Saoirse Quill', 7500, 1, 'lost', 22],
        ];

        $lostReasons = ['Lost to competitor', 'Budget frozen', 'No decision made', 'Timing not right', 'Feature gap'];

        return array_map(function (array $row, int $i) use ($lostReasons) {
            return [
                'title' => $row[0],
                'person' => $row[1],
                'value' => $row[2],
                'stage' => $row[3],
                'status' => $row[4],
                'age_days' => $row[5],
                'lost_reason' => $row[4] === 'lost' ? $lostReasons[$i % count($lostReasons)] : null,
            ];
        }, $rows, array_keys($rows));
    }

    private function seedActivities(User $user, $deals, $people): void
    {
        $templates = [
            ['call', 'Discovery call', 30],
            ['call', 'Follow-up call', 20],
            ['meeting', 'Solution walkthrough', 60],
            ['meeting', 'Stakeholder alignment', 45],
            ['task', 'Send pricing breakdown', null],
            ['task', 'Prepare security questionnaire', null],
            ['deadline', 'Proposal due', null],
            ['email', 'Share implementation timeline', null],
            ['lunch', 'Lunch with champion', 90],
        ];

        $openDeals = $deals->where('status', 'open')->values();

        foreach ($openDeals as $index => $deal) {
            foreach (range(0, mt_rand(1, 2)) as $n) {
                $template = $templates[($index + $n * 3) % count($templates)];
                $offset = mt_rand(-14, 21);

                Activity::create([
                    'owner_id' => $user->id,
                    'deal_id' => $deal->id,
                    'person_id' => $deal->person_id,
                    'organization_id' => $deal->organization_id,
                    'subject' => $template[1].' — '.str($deal->title)->before(' —'),
                    'type' => $template[0],
                    'due_date' => Carbon::now()->addDays($offset)->toDateString(),
                    'due_time' => sprintf('%02d:%02d', mt_rand(8, 17), [0, 15, 30, 45][mt_rand(0, 3)]),
                    'duration_minutes' => $template[2],
                    'priority' => ['low', 'medium', 'high'][mt_rand(0, 2)],
                    'done' => $offset < -3,
                    'completed_at' => $offset < -3 ? Carbon::now()->addDays($offset) : null,
                ]);
            }
        }

        foreach ($deals->where('status', 'won')->take(8) as $deal) {
            Activity::create([
                'owner_id' => $user->id,
                'deal_id' => $deal->id,
                'person_id' => $deal->person_id,
                'organization_id' => $deal->organization_id,
                'subject' => 'Kickoff & handover — '.str($deal->title)->before(' —'),
                'type' => 'meeting',
                'due_date' => $deal->won_at?->copy()->addDays(4)->toDateString(),
                'due_time' => '10:00',
                'duration_minutes' => 60,
                'done' => true,
                'outcome' => 'Handed over to onboarding',
                'completed_at' => $deal->won_at?->copy()->addDays(4),
            ]);
        }

        foreach ($people->take(6) as $index => $person) {
            Activity::create([
                'owner_id' => $user->id,
                'person_id' => $person->id,
                'organization_id' => $person->organization_id,
                'subject' => 'Quarterly check-in with '.$person->name,
                'type' => 'call',
                'due_date' => Carbon::now()->addDays($index + 1)->toDateString(),
                'due_time' => '15:30',
                'duration_minutes' => 30,
            ]);
        }
    }

    private function seedLeads(User $user, $people, $organizations): void
    {
        $rows = [
            ['Enterprise trial request — Vestral Group', 'Saoirse Quill', null, 45000, 'Web form', 'Hot'],
            ['Inbound demo — Halcyon Manufacturing', null, 'Meridian Build', 30000, 'Live chat', 'Warm'],
            ['Referral from Harbor & Vale', 'Priya Raghunathan', 'Harbor & Vale', 22000, 'Referral', 'Hot'],
            ['Conference badge scan — LogiCon', 'Emeka Bartholomew', 'Beacon Freight', 18000, 'Event', 'Warm'],
            ['Pricing enquiry via website', null, 'Foxglove Studio', 7400, 'Web form', 'Cold'],
            ['LinkedIn outreach reply', 'Hollis Grayfield', 'Quillon Analytics', 52000, 'Outbound', 'Warm'],
            ['Partner introduction — Cedar Systems', null, null, 64000, 'Partner', 'Hot'],
        ];

        foreach ($rows as $index => $row) {
            $person = $row[1] ? $people->firstWhere('name', $row[1]) : null;
            $organization = $row[2] ? $organizations->firstWhere('name', $row[2]) : null;

            Lead::create([
                'owner_id' => $user->id,
                'person_id' => $person?->id,
                'organization_id' => $organization?->id ?? $person?->organization_id,
                'title' => $row[0],
                'value' => $row[3],
                'source' => $row[4],
                'label' => $row[5],
                'note' => 'Captured automatically. Qualify and convert to a deal when the budget and timeline are confirmed.',
                'archived_at' => $index === 6 ? Carbon::now()->subDays(3) : null,
                'created_at' => Carbon::now()->subDays($index * 2 + 1),
                'updated_at' => Carbon::now()->subDays($index * 2 + 1),
            ]);
        }
    }

    private function seedInbox(User $user, $people, $deals): void
    {
        $rows = [
            ['Re: Fleet telematics rollout — next steps', 'Marta Okonkwo', "Hi Avery,\n\nThe operations board approved moving ahead with a 12-month term. Can you resend the revised SOW so legal can begin their review this week?\n\nThanks,\nMarta", 2, false],
            ['Security questionnaire', 'Colin Speers', "Avery,\n\nOur InfoSec team needs the SOC 2 report and your sub-processor list before we can sign. Attaching their questionnaire.\n\nColin", 6, false],
            ['Quick question on the proposal', 'Beatrice Lunden', "Hi,\n\nOn page 4 you list per-store pricing — does that include the POS connector, or is that a separate line item?\n\nBeatrice", 20, true],
            ['Thanks for the demo', 'Juniper Cass', "That was really helpful, thank you. I'll take this to our owners on Monday and come back with a decision.\n\nJuniper", 28, true],
            ['Intro: Wren on our sustainability team', 'Rafael Demarco', "Avery — looping in Wren who owns our emissions reporting. She has questions about the add-on module.\n\nRafael", 50, true],
            ['Contract countersigned', 'Silas Whitcombe', "Countersigned copy attached. Looking forward to the kickoff.\n\nSilas", 96, true],
        ];

        foreach ($rows as $index => $row) {
            $person = $people->firstWhere('name', $row[1]);
            $deal = $deals->first(fn ($deal) => $deal->person_id === $person?->id);
            $sentAt = Carbon::now()->subHours($row[3]);

            $thread = EmailThread::create([
                'user_id' => $user->id,
                'person_id' => $person?->id,
                'deal_id' => $deal?->id,
                'subject' => $row[0],
                'folder' => 'inbox',
                'is_read' => $row[4],
                'is_starred' => $index < 2,
                'last_message_at' => $sentAt,
            ]);

            $thread->messages()->create([
                'direction' => 'incoming',
                'from_name' => $person?->name,
                'from_email' => $person?->email ?? 'contact@example.com',
                'to_email' => $user->email,
                'body' => $row[2],
                'sent_at' => $sentAt,
            ]);

            if ($index >= 3) {
                $thread->messages()->create([
                    'direction' => 'outgoing',
                    'from_name' => $user->name,
                    'from_email' => $user->email,
                    'to_email' => $person?->email ?? 'contact@example.com',
                    'body' => "Thanks for the update — I've noted this against the deal and will follow up shortly.\n\n".$user->name,
                    'sent_at' => $sentAt->copy()->addHours(2),
                ]);
                $thread->update(['last_message_at' => $sentAt->copy()->addHours(2)]);
            }
        }

        $sentThread = EmailThread::create([
            'user_id' => $user->id,
            'person_id' => $people->firstWhere('name', 'Tamsin Rourke')?->id,
            'subject' => 'Renewal options for the team plan',
            'folder' => 'sent',
            'is_read' => true,
            'last_message_at' => Carbon::now()->subDays(1),
        ]);

        $sentThread->messages()->create([
            'direction' => 'outgoing',
            'from_name' => $user->name,
            'from_email' => $user->email,
            'to_email' => 'tamsin@quillon.ai',
            'body' => "Hi Tamsin,\n\nHere are the two renewal options we discussed, along with the volume discount if you add the revops block.\n\nBest,\n".$user->name,
            'sent_at' => Carbon::now()->subDays(1),
        ]);
    }

    private function seedNotes(User $user, $deals): void
    {
        $notes = [
            'Champion confirmed budget is approved for this quarter. Procurement is the only remaining gate.',
            'Competitor is incumbent but the contract lapses in 60 days — timing is on our side.',
            'Needs SSO and audit logs before security sign-off. Flagged with product.',
            'Decision committee meets fortnightly; next window is in three weeks.',
        ];

        foreach ($deals->where('status', 'open')->take(4)->values() as $index => $deal) {
            Note::create([
                'user_id' => $user->id,
                'deal_id' => $deal->id,
                'body' => $notes[$index],
            ]);
        }
    }
}
