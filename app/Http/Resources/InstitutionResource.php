<?php

namespace App\Http\Resources;

class InstitutionResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'library_name' => $this->library_name,
            'npp' => $this->npp,
            'agency_name' => $this->agency_name,
            'typology' => $this->typology,
            'address' => $this->address,
            'institution_head_name' => $this->institution_head_name,
            'email' => $this->email,
            'telephone_number' => $this->telephone_number,
            'mobile_number' => $this->mobile_number,
            'library_head_name' => $this->library_head_name,
            'library_worker_name' => $this->library_worker_name,
            'registration_form_file' => $this->registration_form_file ? config('services.frontend.secure_storage_url').$this->registration_form_file : null,
            'title_count' => $this->title_count,
            'status' => $this->status,
            'last_predicate' => $this->last_predicate,
            'last_certification_date' => $this->last_certification_date,
            'region' => new RegionResource($this->region),
            'province' => new ProvinceResource($this->province),
            'city' => new CityResource($this->city),
            'subdistrict' => new SubdistrictResource($this->subdistrict),
            'village' => new VillageResource($this->village),
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'validated_at' => $this->validated_at,
            'predicate' => $this->predicate,
            'accredited_at' => $this->accredited_at,
            'accreditation_expires_at' => $this->accreditation_expires_at,
            $this->mergeWhen(isset($this->type), [
                'type' => $this->type,
            ]),
            $this->mergeWhen(!isset($this->type), [
                'update_form_file' => $this->requests()->typeUpdate()
                                           ->pluck('registration_form_file')->map(function ($file) {
                                               return config('services.frontend.secure_storage_url').$file;
                                           })->toArray(),
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
