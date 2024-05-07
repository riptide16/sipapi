<?php

namespace Tests\Feature\Admin;

use App\Models\Institution;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\Subdistrict;
use App\Models\Village;
use App\Models\User;
use App\Notifications\ValidateAssesseeInstitutionSubmission;
use App\Notifications\AssesseeInstitutionValidated;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

class InstitutionTest extends TestCase
{
    protected $assessee;

    public function setUp(): void
    {
        parent::setUp();
        $this->assessee = User::factory()->assessee()->create();
    }

    public function test_update()
    {
        Storage::fake('public');
        Notification::fake();

        $institution = Institution::factory()->create([
            'user_id' => $this->assessee->id,
        ]);

        $body = [
            'category' => 'Khusus',
            'region_id' => Region::factory()->create()->id,
            'library_name' => 'Perpustakaan',
            'npp' => '123',
            'agency_name' => 'Instansi 1',
            'typology' => 'A',
            'address' => 'Alamat Testing',
            'province_id' => Province::factory()->create()->id,
            'city_id' => City::factory()->create()->id,
            'subdistrict_id' => Subdistrict::factory()->create()->id,
            'village_id' => Village::factory()->create()->id,
            'institution_head_name' => 'Nama Kepala',
            'email' => 'test@email.com',
            'telephone_number' => '021912321',
            'mobile_number' => '08123129123',
            'library_head_name' => 'Kepala Perpus',
            'library_worker_name' => 'Tenaga Perpus',
            'registration_form' => UploadedFile::fake()->image('form.jpg'),
            'title_count' => '100',
        ];
        $response = $this->actingAsPassport($this->assessee)->post(
            route('admin.institutions.update', [$institution->id]),
            $body
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id,
            'library_name' => $body['library_name'],
            'address' => $body['address'],
        ]);
        $institution = $institution->refresh();
        Storage::disk('public')->assertExists($institution->getRawOriginal('registration_form_file'));
        Notification::assertSentTo($this->superAdmin, ValidateAssesseeInstitutionSubmission::class);
    }

    public function test_verify()
    {
        Notification::fake();

        $institution = Institution::factory()->filled()->create([
            'user_id' => $this->assessee->id,
        ]);

        $body = [
            'is_valid' => true,
        ];
        $response = $this->putJson(
            route('admin.institutions.verify', [$institution->id]),
            $body
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
        $this->assertNotNull($institution->refresh()->validated_at);
        Notification::assertSentTo($this->assessee, AssesseeInstitutionValidated::class);
    }

    public function test_index()
    {
        $institution = Institution::factory()->filled()->create([
            'user_id' => $this->assessee->id,
        ]);
        $response = $this->getJson(route('admin.institutions.index'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $sample = $response['data'][0];
        $this->assertEquals($institution->id, $sample['id']);
        $this->assertEquals($institution->library_name, $sample['library_name']);
        $this->assertEquals($institution->agency_name, $sample['agency_name']);
    }

    public function test_show()
    {
        $institution = Institution::factory()->filled()->create([
            'user_id' => $this->assessee->id,
        ]);
        $response = $this->getJson(route('admin.institutions.show', [$institution->id]));

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJson(function (AssertableJson $json) use ($institution) {
                     $json->has('data', function ($json) use ($institution) {
                              $json->where('id', $institution->id)
                                   ->where('library_name', $institution->library_name)
                                   ->where('agency_name', $institution->agency_name)
                                   ->etc();
                          })
                          ->etc();
                 });
    }
}
