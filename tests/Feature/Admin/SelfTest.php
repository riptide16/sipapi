<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Institution;
use App\Models\Instrument;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Models\InstrumentComponent;
use App\Models\Notification;
use App\Models\EvaluationAssignment;
use Illuminate\Testing\Fluent\AssertableJson;

class SelfTest extends TestCase
{
    protected $assessee;

    public function setUp(): void
    {
        parent::setUp();

        $this->assessee = User::factory()->assessee()->create();
        $this->actingAsPassport($this->assessee);
    }

    public function test_get_user_data()
    {
        $response = $this->get(route('admin.self.user'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) {
                     $json->has('data', function ($json) {
                              $json->where('id', $this->assessee->id)
                                   ->where('name', $this->assessee->name)
                                   ->where('email', $this->assessee->email)
                                   ->missing('password')
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_update_profile()
    {
        $param = [
            'name' => 'ganti nama',
            'phone_number' => '08113131',
        ];

        $response = $this->post(route('admin.self.profile.update'), $param);
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $this->assessee->id,
            'phone_number' => $param['phone_number'],
            'name' => $param['name'],
        ]);
    }

    public function test_get_permissions()
    {
        $response = $this->get(route('admin.self.permissions'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_get_menus()
    {
        $response = $this->get(route('admin.self.menus'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_get_institution()
    {
        $response = $this->get(route('admin.self.institution'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) {
                     $json->has('data', function ($json) {
                              $json->where('user_id', $this->assessee->id)
                                   ->etc();
                          })
                          ->etc();
                 });
    }

    public function test_show_instrument()
    {
        $institution = Institution::factory()->filled()->valid()->create([
            'user_id' => $this->assessee->id,
        ]);
        $component = InstrumentComponent::factory()->create([
            'category' => $institution->category,
        ]);
        $instrument = Instrument::where('category', $institution->category)->first();
        $aspect = InstrumentAspect::factory()->choice()->create([
            'instrument_id' => $instrument->id,
            'instrument_component_id' => $component->id,
        ]);
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);

        $response = $this->getJson(route('admin.self.instrument'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $comp = $response['data'][0];
        $this->assertEquals($component->id, $comp['id']);
        $this->assertEquals($component->category, $comp['category']);
        $this->assertEquals($aspect->aspect, $comp['aspects'][0]['aspect']);
        $this->assertEquals(5, count($comp['aspects'][0]['points']));
    }

    public function test_index_notifications()
    {
        $notifications = Notification::factory()->count(3)->create([
            'notifiable_id' => $this->assessee->id,
        ]);

        $response = $this->getJson(route('admin.self.notifications'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function test_read_notifications()
    {
        $notification = Notification::factory()->create([
            'notifiable_id' => $this->assessee->id,
        ]);

        $response = $this->getJson(route('admin.self.notifications.read', [$notification->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertTrue(!is_null($notification->refresh()->read_at));
    }

    public function test_index_evaluation_assignments()
    {
        $assignment = EvaluationAssignment::factory()->create();
        $user = User::factory()->assessor()->create();
        $assignment->assessors()->attach($user);

        $response = $this->actingAsPassport($user)
                         ->getJson(route('admin.self.evaluation_assignments'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $assignment->refresh();
        $data = $response['data'][0];
        $this->assertEquals($assignment->accreditation_id, $data['accreditation']['id']);
        $this->assertEquals($assignment->method, $data['method']);
        $this->assertEquals($assignment->scheduled_date, $data['scheduled_date']);
    }
}
