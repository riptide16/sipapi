<?php

namespace Tests\Feature\Admin;

use App\Models\Accreditation;
use App\Models\AccreditationContent;
use App\Models\Institution;
use App\Models\Instrument;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Models\InstrumentComponent;
use App\Models\User;
use App\Notifications\VerifyNewAccreditation;
use App\Notifications\ResubmitIncompleteAccreditation;
use App\Notifications\EvaluateAccreditation;
use App\Notifications\AcceptAccreditationEvaluation;
use App\Notifications\AccreditationAccepted;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AccreditationTest extends TestCase
{
    public function test_index_as_admin()
    {
        $accreditations = Accreditation::factory()->count(3)->create();

        $response = $this->getJson(route('admin.accreditations.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($accreditations[0]->id, $sample['id']);
        $this->assertEquals($accreditations[0]->code, $sample['code']);
        $this->assertEquals($accreditations[0]->status, $sample['status']);
    }

    public function test_index_as_assessee()
    {
        $assessee = User::factory()->assessee()->active()->create();
        $institution = Institution::factory()->filled()->valid()->create([
            'user_id' => $assessee->id,
        ]);
        $accreditation = Accreditation::factory()->create([
            'user_id' => $assessee->id,
            'institution_id' => $institution->id,
        ]);
        $accreditations = Accreditation::factory()->count(3)->create();

        $response = $this->actingAsPassport($assessee)
                         ->getJson(route('admin.accreditations.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($accreditation->id, $sample['id']);
        $this->assertEquals($accreditation->code, $sample['code']);
        $this->assertEquals($accreditation->status, $sample['status']);

        foreach ($response['data'] as $acc) {
            foreach ($accreditations as $accredit) {
                $this->assertNotEquals($accredit->code, $acc['code']);
            }
        }
    }

    public function test_show_as_admin()
    {
        $accreditation = Accreditation::factory()->create();

        $response = $this->getJson(route('admin.accreditations.show', [$accreditation->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'];
        $this->assertEquals($accreditation->id, $sample['id']);
        $this->assertEquals($accreditation->code, $sample['code']);
        $this->assertEquals($accreditation->status, $sample['status']);
    }

    public function test_store()
    {
        Notification::fake();

        $assessee = User::factory()->assessee()->create();
        $institution = Institution::factory()->filled()->valid()->create([
            'user_id' => $assessee->id,
        ]);
        $regionalAdmin = User::factory()->admin()->create([
            'region_id' => $institution->region_id,
        ]);
        $component = InstrumentComponent::factory()->create([
            'category' => $institution->category,
        ]);
        $instrument = Instrument::where('category', $institution->category)->first();
        $aspect = InstrumentAspect::factory()->create([
            'instrument_id' => $instrument->id,
            'instrument_component_id' => $component->id,
        ]);
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);

        $param = [
            'type' => 'baru',
            'contents' => [[
                'type' => 'choice',
                'instrument_component_id' => $component->id,
                'instrument_aspect_id' => $aspect->id,
                'instrument_aspect_point_id' => $points[0]->id,
            ]],
            'is_complete' => true,
        ];

        $response = $this->actingAsPassport($assessee)
                         ->postJson(route('admin.accreditations.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertDatabaseHas('accreditations', [
            'code' => now()->format('Y') . '/1',
            'status' => Accreditation::STATUS_SUBMITTED,
            'user_id' => $assessee->id,
        ]);
        $this->assertDatabaseHas('accreditation_contents', [
            'accreditation_id' => $data['id'],
            'aspectable_type' => InstrumentAspect::class,
            'aspectable_id' => $aspect->id,
            'main_component_id' => $component->id,
        ]);

        Notification::assertSentTo($regionalAdmin, VerifyNewAccreditation::class);
    }

    public function test_store_proof()
    {
        Notification::fake();

        $assessee = User::factory()->assessee()->create();
        $institution = Institution::factory()->filled()->valid()->create([
            'user_id' => $assessee->id,
        ]);
        $regionalAdmin = User::factory()->admin()->create([
            'region_id' => $institution->region_id,
        ]);
        $component = InstrumentComponent::factory()->create([
            'category' => $institution->category,
        ]);
        $subcomponent = InstrumentComponent::factory()->sub1()->create([
            'parent_id' => $component->id,
        ]);
        $instrument = Instrument::where('category', $institution->category)->first();
        $aspect = InstrumentAspect::factory()->create([
            'instrument_id' => $instrument->id,
            'instrument_component_id' => $component->id,
        ]);
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);

        $param = [
            'type' => 'baru',
            'contents' => [[
                'type' => 'proof',
                'instrument_component_id' => $subcomponent->id,
                'file_upload' => UploadedFile::fake()->create('file.pdf', 10, 'application/pdf'),
            ]],
            'is_complete' => true,
        ];

        $response = $this->actingAsPassport($assessee)
                         ->postJson(route('admin.accreditations.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertDatabaseHas('accreditations', [
            'code' => now()->format('Y') . '/1',
            'status' => Accreditation::STATUS_SUBMITTED,
            'user_id' => $assessee->id,
        ]);
        $this->assertDatabaseHas('accreditation_contents', [
            'accreditation_id' => $data['id'],
            'aspectable_type' => InstrumentComponent::class,
            'aspectable_id' => $component->id,
            'main_component_id' => $component->id,
        ]);

        Notification::assertSentTo($regionalAdmin, VerifyNewAccreditation::class);
    }

    public function test_verify()
    {
        $accreditation = Accreditation::factory()->create();

        $response = $this->postJson(
            route('admin.accreditations.verify', [$accreditation->id]),
            [
                'is_approved' => true,
                'notes' => 'Nice',
            ]
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('accreditations', [
            'id' => $accreditation->id,
            'status' => Accreditation::STATUS_VERIFIED,
            'notes' => 'Nice',
        ]);
    }

    public function test_verify_not_approved()
    {
        Notification::fake();

        $accreditation = Accreditation::factory()->create();

        $response = $this->postJson(
            route('admin.accreditations.verify', [$accreditation->id]),
            [
                'is_approved' => false,
                'notes' => 'Buruk',
            ]
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('accreditations', [
            'id' => $accreditation->id,
            'status' => Accreditation::STATUS_INCOMPLETE,
            'notes' => 'Buruk',
        ]);

        Notification::assertSentTo($accreditation->user, ResubmitIncompleteAccreditation::class);
    }

    public function test_show_instruments()
    {
        $assessee = User::factory()->assessee()->create();
        $institution = Institution::factory()->filled()->valid()->create([
            'user_id' => $assessee->id,
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
        $accreditation = Accreditation::factory()->create([
            'user_id' => $assessee->id,
            'institution_id' => $institution->id,
        ]);
        $content = AccreditationContent::factory()->choice()->create([
            'accreditation_id' => $accreditation->id,
            'aspectable_id' => $aspect->id,
            'aspect' => $aspect->aspect,
            'instrument_aspect_point_id' => $points[4]->id,
            'statement' => $points[4]->statement,
            'value' => $points[4]->value,
        ]);

        $response = $this->actingAsPassport($assessee)
                         ->getJson(route('admin.accreditations.instruments.index', [
                             $accreditation->id
                         ]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $comp = $response['data'][0];
        $this->assertEquals($component->id, $comp['id']);
        $this->assertEquals($component->category, $comp['category']);
        $this->assertEquals($aspect->aspect, $comp['aspects'][0]['aspect']);
        $this->assertEquals($content->statement, $comp['aspects'][0]['answers'][0]['statement']);
        $this->assertEquals($content->instrument_aspect_point_id, $comp['aspects'][0]['answers'][0]['instrument_aspect_point_id']);
        $this->assertEquals(5, count($comp['aspects'][0]['points']));
    }

    public function test_assign_assessors()
    {
        Notification::fake();

        $accreditation = Accreditation::factory()->create();
        $assessor = User::factory()->assessor()->create();

        $param = [
            'scheduled_date' => '2021-01-01',
            'method' => 'Online',
            'assessors' => [[
                'user_id' => $assessor->id,
            ]],
        ];
        $response = $this->postJson(
            route('admin.accreditations.evaluation_assignments', [$accreditation->id]),
            $param
        );

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('evaluation_assignments', [
            'accreditation_id' => $accreditation->id,
            'method' => $param['method'],
            'scheduled_date' => $param['scheduled_date'],
        ]);
        $this->assertDatabaseHas('evaluation_assignment_user', [
            'user_id' => $assessor->id,
        ]);

        Notification::assertSentTo($assessor, EvaluateAccreditation::class);
    }

    public function test_index_contents()
    {
        $assessor = User::factory()->assessor()->create();
        $aspect = InstrumentAspect::factory()->choice()->create();
        $points = InstrumentAspectPoint::factory()->count(5)->create([
            'instrument_aspect_id' => $aspect->id,
        ]);
        $accreditation = Accreditation::factory()->create();
        $content = AccreditationContent::factory()->choice()->create([
            'accreditation_id' => $accreditation->id,
            'aspectable_id' => $aspect->id,
            'aspect' => $aspect->aspect,
            'instrument_aspect_point_id' => $points[4]->id,
            'statement' => $points[4]->statement,
            'value' => $points[4]->value,
        ]);

        $response = $this->actingAsPassport($assessor)
                         ->getJson(route('admin.accreditations.contents.index', [
                             $accreditation->id,
                             'type' => 'choice',
                         ]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $comp = $response['data'][0];
        $this->assertEquals($content->id, $comp['id']);
        $this->assertEquals($content->statement, $comp['statement']);
        $this->assertEquals($content->instrument_aspect_point_id, $comp['instrument_aspect_point_id']);
    }

    public function test_process()
    {
        Notification::fake();

        $accreditation = Accreditation::factory()->reviewed()->create();

        $param = [
            'predicate' => 'A',
        ];
        $response = $this->postJson(
            route('admin.accreditations.process', [$accreditation->id]),
            $param
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('accreditations', [
            'id' => $accreditation->id,
            'predicate' => $param['predicate'],
            'status' => Accreditation::STATUS_EVALUATED,
        ]);

        Notification::assertSentTo($accreditation->user, AcceptAccreditationEvaluation::class);
    }

    public function test_accept()
    {
        Notification::fake();

        $accreditation = Accreditation::factory()->evaluated()->create();
        $regionalAdmin = User::factory()->admin()->create([
            'region_id' => $accreditation->institution->region_id,
        ]);

        $param = [
            'is_accepted' => true,
        ];
        $response = $this->actingAsPassport($accreditation->user)
                         ->postJson(
                             route('admin.accreditations.accept', [$accreditation->id]),
                             $param
                         );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('accreditations', [
            'id' => $accreditation->id,
            'status' => Accreditation::STATUS_ACCREDITED,
        ]);
        $this->assertFalse(is_null($accreditation->refresh()->accredited_at));

        Notification::assertSentTo($regionalAdmin, AccreditationAccepted::class);
    }
}
