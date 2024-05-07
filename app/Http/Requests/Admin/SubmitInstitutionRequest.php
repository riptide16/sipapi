<?php

namespace App\Http\Requests\Admin;

use App\Models\Institution;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitInstitutionRequest extends FormRequest
{
    public $institution;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isActive();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category' => ['required', Rule::in(Institution::categoryList())],
            'region_id' => 'required|exists:regions,id',
            'library_name' => 'required|max:191',
            'npp' => 'nullable|max:191',
            'agency_name' => 'required|max:191',
            'typology' => [
                Rule::requiredIf(function () {
                    return in_array($this->request->get('category'), ['Provinsi', 'Kabupaten Kota']);
                }), 
                Rule::in(Institution::typologyList())
            ],
            'address' => 'required',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'subdistrict_id' => 'required|exists:subdistricts,id',
            'village_id' => 'required|exists:villages,id',
            'institution_head_name' => 'required|max:191',
            'email' => 'required|email|max:191',
            'telephone_number' => 'required|digits_between:6,20',
            'mobile_number' => 'required|digits_between:6,20',
            'library_head_name' => 'required|max:191',
            'library_worker_name' => 'required|array',
            'registration_form' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg',
                'max:2048',
            ],
            'title_count' => 'required|numeric',
            'last_predicate' => ['nullable', Rule::in(Institution::predicateList())],
            'last_certification_date' => 'nullable|date|date_format:Y-m-d',
        ];
    }
}
