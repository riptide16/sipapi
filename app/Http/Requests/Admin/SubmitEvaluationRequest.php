<?php

namespace App\Http\Requests\Admin;

use App\Models\AccreditationContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->request->has('accreditation_id') 
            && $this->user()->canEvaluate($this->request->get('accreditation_id'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accreditation_id' => [
                'required',
                'exists:accreditations,id',
                // Rule::exists('accreditations', 'id', function ($query) {
                //     return $query->whereHas('evaluationAssignments.assessors', function ($query) {
                //         $query->where('user_id', $this->user()->id);
                //     });
                // ]),
            ],
            'contents' => 'required|array',
            'contents.*.accreditation_content_id' => [
                'required',
                Rule::exists('accreditation_contents', 'id', function ($query) {
                    return $query->where('type', AccreditationContent::TYPE_CHOICE);
                }),
            ],
            'contents.*.instrument_aspect_point_id' => 'required|exists:instrument_aspect_points,id',
            'is_complete' => 'required|boolean',
        ];
    }
}
