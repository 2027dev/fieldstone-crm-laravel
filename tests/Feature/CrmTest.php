<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\EmailThread;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Pipeline;
use App\Models\User;
use App\Support\DemoSeeder;
use App\Support\SetupGuide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Pipeline $pipeline;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->pipeline = DemoSeeder::ensurePipeline();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/deals')->assertRedirect('/login');
        $this->get('/insights')->assertRedirect('/login');
    }

    public function test_registration_creates_a_workspace_with_sample_data(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jordan Reyes',
            'email' => 'jordan@example.com',
            'company_name' => 'Reyes & Co',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('setup.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'jordan@example.com']);
        $this->assertTrue(Person::where('is_sample', true)->exists());
        $this->assertTrue(Activity::where('is_sample', true)->exists());
    }

    public function test_main_pages_render(): void
    {
        $this->actingAs($this->user);

        foreach ([
            '/setup', '/people', '/organizations', '/contacts/timeline', '/contacts/duplicates',
            '/activities', '/activities/calendar', '/deals', '/deals/list', '/leads',
            '/leads/web-forms', '/insights', '/inbox', '/profile', '/search?q=test',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_a_person_can_be_created_and_deleted(): void
    {
        $this->actingAs($this->user);

        $this->post('/people', ['name' => 'Dana Wexler', 'email' => 'dana@example.com'])
            ->assertRedirect();

        $person = Person::firstWhere('name', 'Dana Wexler');
        $this->assertNotNull($person);
        $this->assertSame($this->user->id, $person->owner_id);

        $this->delete("/people/{$person->id}")->assertRedirect(route('people.index'));
        $this->assertModelMissing($person);
    }

    public function test_a_deal_moves_between_stages(): void
    {
        $this->actingAs($this->user);

        $stages = $this->pipeline->stages()->get();

        $this->post('/deals', [
            'title' => 'Acme rollout',
            'value' => 5000,
            'stage_id' => $stages[0]->id,
        ])->assertRedirect();

        $deal = Deal::firstWhere('title', 'Acme rollout');

        $this->post("/deals/{$deal->id}/move", ['stage_id' => $stages[3]->id])->assertRedirect();
        $this->assertSame($stages[3]->id, $deal->refresh()->stage_id);
    }

    public function test_a_deal_can_be_won_and_lost(): void
    {
        $this->actingAs($this->user);

        $deal = Deal::create([
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $this->pipeline->stages()->first()->id,
            'title' => 'Closing test',
            'value' => 1000,
        ]);

        $this->post("/deals/{$deal->id}/status", ['status' => 'won'])->assertRedirect();
        $this->assertSame('won', $deal->refresh()->status);
        $this->assertNotNull($deal->won_at);

        $this->post("/deals/{$deal->id}/status", ['status' => 'lost', 'lost_reason' => 'Budget frozen'])->assertRedirect();
        $deal->refresh();
        $this->assertSame('lost', $deal->status);
        $this->assertSame('Budget frozen', $deal->lost_reason);
        $this->assertNull($deal->won_at);
    }

    public function test_an_activity_can_be_toggled_done(): void
    {
        $this->actingAs($this->user);

        $this->post('/activities', [
            'subject' => 'Kickoff call',
            'type' => 'call',
            'due_date' => now()->toDateString(),
        ])->assertRedirect();

        $activity = Activity::firstWhere('subject', 'Kickoff call');
        $this->assertFalse($activity->done);

        $this->post("/activities/{$activity->id}/toggle")->assertRedirect();
        $this->assertTrue($activity->refresh()->done);
        $this->assertNotNull($activity->completed_at);

        $this->post("/activities/{$activity->id}/toggle")->assertRedirect();
        $this->assertFalse($activity->refresh()->done);
    }

    public function test_a_lead_converts_into_a_deal(): void
    {
        $this->actingAs($this->user);

        $person = Person::create(['name' => 'Inbound Contact']);
        $lead = Lead::create([
            'owner_id' => $this->user->id,
            'person_id' => $person->id,
            'title' => 'Inbound enquiry',
            'value' => 12000,
        ]);

        $this->post("/leads/{$lead->id}/convert")->assertRedirect();

        $lead->refresh();
        $this->assertNotNull($lead->converted_deal_id);

        $deal = $lead->convertedDeal;
        $this->assertSame('Inbound enquiry', $deal->title);
        $this->assertSame('12000.00', $deal->value);
        $this->assertSame($person->id, $deal->person_id);
    }

    public function test_the_public_web_form_creates_a_lead_and_contact(): void
    {
        $this->post('/f/contact', [
            'name' => 'Web Visitor',
            'email' => 'visitor@example.com',
            'company' => 'Visitor Co',
            'budget' => 2500,
            'message' => 'Interested in a demo',
        ])->assertRedirect();

        $this->assertDatabaseHas('people', ['email' => 'visitor@example.com']);
        $this->assertDatabaseHas('organizations', ['name' => 'Visitor Co']);
        $this->assertDatabaseHas('leads', ['source' => 'Web form', 'value' => 2500]);
    }

    public function test_replying_appends_an_outgoing_message(): void
    {
        $this->actingAs($this->user);

        $thread = EmailThread::create([
            'user_id' => $this->user->id,
            'subject' => 'Pricing',
            'last_message_at' => now(),
        ]);
        $thread->messages()->create([
            'direction' => 'incoming',
            'from_email' => 'buyer@example.com',
            'to_email' => $this->user->email,
            'body' => 'How much?',
            'sent_at' => now(),
        ]);

        $this->post("/inbox/{$thread->id}/reply", ['body' => 'Here is our pricing.'])->assertRedirect();

        $this->assertSame(2, $thread->messages()->count());
        $this->assertDatabaseHas('email_messages', [
            'direction' => 'outgoing',
            'to_email' => 'buyer@example.com',
        ]);
    }

    public function test_removing_sample_data_only_deletes_sample_records(): void
    {
        $this->actingAs($this->user);

        DemoSeeder::seedSampleData($this->user);
        $keeper = Person::create(['name' => 'Real Contact']);

        $this->delete('/sample-data')->assertRedirect();

        $this->assertSame(0, Person::where('is_sample', true)->count());
        $this->assertSame(0, Organization::where('is_sample', true)->count());
        $this->assertModelExists($keeper);
    }

    public function test_setup_progress_tracks_completed_tasks(): void
    {
        $this->actingAs($this->user);

        $before = SetupGuide::progress($this->user->fresh());

        $this->post('/setup/view_insights/complete')->assertRedirect();

        $after = SetupGuide::progress($this->user->fresh());
        $this->assertSame($before['completed'] + 1, $after['completed']);
    }

    public function test_duplicate_people_can_be_merged(): void
    {
        $this->actingAs($this->user);

        $keep = Person::create(['name' => 'Sam Rivera', 'email' => 'sam@example.com']);
        $dupe = Person::create(['name' => 'Sam Rivera', 'email' => 'sam@example.com', 'phone' => '555-0199']);

        Deal::create([
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $this->pipeline->stages()->first()->id,
            'person_id' => $dupe->id,
            'title' => 'Dupe deal',
        ]);

        $this->post('/contacts/duplicates', [
            'keep_id' => $keep->id,
            'merge_ids' => [$dupe->id],
        ])->assertRedirect();

        $this->assertModelMissing($dupe);
        $this->assertSame('555-0199', $keep->refresh()->phone);
        $this->assertSame($keep->id, Deal::firstWhere('title', 'Dupe deal')->person_id);
    }
}
