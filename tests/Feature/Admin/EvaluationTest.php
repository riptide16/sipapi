<?php

namespace Tests\Feature\Admin;

use App\Events\AccreditationEvaluated;
use App\Models\Accreditation;
use App\Models\AccreditationContent;
use App\Models\Evaluation;
use App\Models\EvaluationAssignment;
use App\Models\EvaluationContent;
use App\Models\InstrumentAspectPoint;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InputAccreditationResult;

class EvaluationTest extends TestCase
{
    protected $assessor;

    public function setUp(): void
    {
        parent::setUp();

        $this->assessor = User::factory()->assessor()->create();
    }

    public function test_store()
    {
        $accreditation = Accreditation::factory()->create();
        $choices = AccreditationContent::factory()->count(3)->choice()->create([
            'accreditation_id' => $accreditation->id,
        ]);
        $proofs = AccreditationContent::factory()->count(3)->proof()->create([
            'accreditation_id' => $accreditation->id,
        ]);
        $assignment = EvaluationAssignment::factory()->create([
            'accreditation_id' => $accreditation->id,
        ]);
        $assignment->assessors()->attach($this->assessor);

        $param = [
            'accreditation_id' => $accreditation->id,
            'is_complete' => true,
            'contents' => [],
        ];

        foreach ($choices as $choice) {
            $point = InstrumentAspectPoint::where('instrument_aspect_id', $choice->aspectable_id)
                                          ->inRandomOrder()
                                          ->first();
            $param['contents'][] = [
                'accreditation_content_id' => $choice->id,
                'instrument_aspect_point_id' => $point->id,
            ];
        }

        $response = $this->actingAsPassport($this->assessor)
                         ->postJson(route('admin.evaluations.store'), $param);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('evaluations', [
            'accreditation_id' => $accreditation->id,
            'institution_id' => $accreditation->institution_id,
            'assessor_id' => $this->assessor->id,
        ]);
        foreach ($param['contents'] as $content) {
            $this->assertDatabaseHas('evaluation_contents', [
                'accreditation_content_id' => $content['accreditation_content_id'],
                'instrument_aspect_point_id' => $content['instrument_aspect_point_id'],
            ]);
        }

        $data = $response['data'];
        $this->assertTrue($data['need_upload_document']);
    }

    public function test_show()
    {
        $evaluation = Evaluation::factory()->create();
        EvaluationContent::factory()->count(3)->create([
            'evaluation_id' => $evaluation->id,
        ]);

        $response = $this->actingAsPassport($this->assessor)
                         ->getJson(route('admin.evaluations.show', [$evaluation->id])); 

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response['data'];
        $this->assertEquals($evaluation->id, $data['id']);
        $this->assertEquals(3, count($data['contents']));
    }

    public function test_upload_document()
    {
        Notification::fake();

        $evaluation = Evaluation::factory()->create();
        EvaluationContent::factory()->count(3)->create([
            'evaluation_id' => $evaluation->id,
        ]);
        $admins = User::factory()->count(3)->admin()->create([
            'region_id' => $evaluation->institution->region_id,
        ]);

        $param = [
            'file' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
        ];

        $response = $this->actingAsPassport($this->assessor)
                         ->post(route('admin.evaluations.upload_document', [$evaluation->id]), $param); 

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $evaluation->refresh();
        $this->assertNotNull($evaluation->document_file);

        $this->assertDatabaseHas('accreditations', [
            'id' => $evaluation->accreditation_id,
            'status' => Accreditation::STATUS_REVIEWED,
        ]);

        Notification::assertSentTo($admins, InputAccreditationResult::class);
    }

    public function test_index()
    {
        $evaluations = Evaluation::factory()->count(3)->create();

        $response = $this->actingAsPassport($this->assessor)
                         ->getJson(route('admin.evaluations.index')); 

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}
