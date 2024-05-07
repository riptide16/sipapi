<?php

namespace App\Http\Requests;

use App\Models\InstrumentAspect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBulkInstrumentAspectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'aspects' => 'required',
            'aspects.*.aspect' => 'required|max:65535',
            'aspects.*.instrument_component_id' => 'required|exists:instrument_components,id',
            'aspects.*.type' => ['required', Rule::in(InstrumentAspect::typeList())],
            'aspects.*.order' => 'required|numeric|min:1',
            'aspects.*.points' => 'required_unless:aspects.*.type,multi_aspect',
            'aspects.*.points.*.statement' => 'required|max:65535',
            'aspects.*.points.*.order' => 'required|numeric|min:1',
            'aspects.*.children' => 'required_if:aspects.*.type,multi_aspect|max:65535',
            'aspects.*.children.*.aspect' => 'required|max:65535',
            'aspects.*.children.*.order' => 'required|numeric|min:1',
            'aspects.*.children.*.points' => 'required_with:aspects.*.children',
            'aspects.*.children.*.points.*.statement' => 'required|max:65535',
            'aspects.*.children.*.points.*.order' => 'required|numeric|min:1',
        ];
    }
}
