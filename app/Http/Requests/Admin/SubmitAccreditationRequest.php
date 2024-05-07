<?php

namespace App\Http\Requests\Admin;

use App\Models\Accreditation;
use App\Models\AccreditationContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitAccreditationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();

        return $user->institution->isValid();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in(Accreditation::typeList())],
            'contents' => 'required|array',
            'contents.*.type' => ['required', Rule::in(AccreditationContent::typeList())],
            'contents.*.instrument_component_id' => 'required|exists:instrument_components,id',
            'contents.*.instrument_aspect_id' => 'required_if:contents.*.type,choice|exists:instrument_aspects,id',
            'contents.*.instrument_aspect_point_id' => [
                'required_with:contents.*.instrument_aspect_id',
                'exists:instrument_aspect_points,id',
            ],
            'contents.*.file_upload' => 'required_if:contents.*.type,proof|file|mimes:pdf,rar,zip|max:10240',
            'contents.*.url' => 'nullable|url|max:191',
        ];
    }
}
