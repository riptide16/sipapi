<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Rules\Alphaspace;
use App\Rules\Alphanumericplus;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|max:191|unique:users',
            'username' => ['required', new Alphanumericplus(), 'min:3', 'max:191', 'unique:users'],
            'name' => ['required', new Alphanumericplus, 'min:3', 'max:191'],
            'password' => ['required', 'max:191', 'confirmed', Password::min(8)],
            'role_name' => ['required', Rule::in([Role::ASSESSOR, Role::ASSESSEE])],
            'institution_name' => 'required_if:role_name,'.Role::ASSESSOR,
            'province_id' => 'required_if:role_name,'.Role::ASSESSOR.'|exists:provinces,id',
        ];
    }
}
