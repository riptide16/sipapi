<?php

namespace App\Http\Controllers\Admin;

use App\Models\Institution;
use App\Models\InstitutionRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInstitutionRequest;
use App\Http\Resources\InstitutionCollection;
use App\Http\Resources\InstitutionResource;
use App\Notifications\ValidateAssesseeInstitutionSubmission;
use App\Notifications\AssesseeInstitutionValidated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class InstitutionController extends Controller
{
    public function update(UpdateInstitutionRequest $request, $id)
    {
        $institution = $request->institution;
        $data = $request->all();

        if ($request->hasFile('registration_form')) {
            $formPath = $request->file('registration_form')
                                ->storePublicly('institutions/forms', 'public');
            $data = array_merge($data, ['registration_form_file' => $formPath]);
        }
        $institution->update($data);

        if (is_null($institution->validated_at)) {
            $this->notifyToValidate($institution);
        }

        return new InstitutionResource($institution);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $datatype = $request->get('datatype') ?? 'valid';
        if ($datatype == 'valid') {
            $institutions = Institution::with(['region', 'province', 'city', 'subdistrict', 'village'])->filter($request->all())->orderBy('created_at','desc');
        } elseif($request->datatype == 'request') {
            $institutions = InstitutionRequest::with(['user', 'region', 'province', 'city', 'subdistrict', 'village'])
                ->filter($request->all())->orderBy('created_at','desc');
        //$institutions->orderBy('created_at', 'desc');
        //$institutions = $request->has('per_page') && $request->per_page <= -1
            //? $institutions->get()
            //: $institutions->paginate($request->per_page ?? 20)->withQueryString();
	    }

        if($user->isSuperAdmin()){
            $institutions =  $institutions->get();
        } else {
            $institutions = $institutions->where("region_id",$user["region_id"])->get();
        }
        
        return new InstitutionCollection($institutions);
    }

    public function show(Request $request, $id)
    {
        $datatype = $request->get('datatype') ?? 'valid';
        if ($datatype == 'valid') {
            $institution = Institution::with(['region', 'province', 'city', 'subdistrict', 'village'])->findOrFail($id);
        } elseif($request->datatype == 'request') {
            $institution = InstitutionRequest::with(['user', 'region', 'province', 'city', 'subdistrict', 'village'])->findOrFail($id);
        }
        return new InstitutionResource($institution);
    }

    protected function notifyToValidate($institution)
    {
        $users = User::admins()->get();
        Notification::send($users, new ValidateAssesseeInstitutionSubmission($institution));
    }

    public function validation(Request $request, $id)
    {
        $institution = InstitutionRequest::findOrFail($id);

        $request->validate([
            'is_valid' => 'required|boolean',
        ]);

        if ($request->is_valid) {
            $institution->setValid();
            $institution->save();

            $validInstitution = Institution::firstOrNew([
                'category' => $request->category,
                'library_name' => $request->library_name,
                'agency_name' => $request->agency_name,
            ]);
            if (!$validInstitution->exists) {
                $validInstitution->fill([
                    'region_id' => $institution->region_id,
                    'npp' => $institution->npp,
                    'typology' => $institution->typology,
                    'address' => $institution->address,
                    'province_id' => $institution->province_id,
                    'city_id' => $institution->city_id,
                    'subdistrict_id' => $institution->subdistrict_id,
                    'village_id' => $institution->village_id,
                    'institution_head_name' => $institution->institution_head_name,
                    'email' => $institution->email,
                    'telephone_number' => $institution->telephone_number,
                    'mobile_number' => $institution->mobile_number,
                    'library_head_name' => $institution->library_head_name,
                    'library_worker_name' => $institution->library_worker_name,
                    'registration_form_file' => $institution->registration_form_file,
                    'title_count' => $institution->title_count,
                    'status' => $institution->status,
                    'validated_at' => $institution->validated_at,
                    'last_predicate' => $institution->last_predicate,
                    'last_certification_date' => $institution->last_certification_date,
                ])->save();
            }
            $validInstitution->update($request->all());
            $institution->user->institution_id = $validInstitution->id;
            $institution->user->save();
            $institution->institution_id = $validInstitution->id;
            $institution->save();
        } else {
            $institution->setInvalid()->save();
        }

        $institution->user->notify(new AssesseeInstitutionValidated($institution));

        return new InstitutionResource($institution);
    }

}
