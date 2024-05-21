<?php

namespace App\Http\Requests\Admin;

use App\Rules\Alphaspace;
use App\Rules\Alphanumericplus;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name' => ['required', 'min:3', 'max:191'],
            'username' => ['required', new Alphanumericplus(), 'min:3', 'max:191', 'unique:users'],
            'email' => 'required|email|max:191|unique:users',
            'password' => 'required|min:8|max:191|confirmed',
            'role_id' => 'required|exists:roles,id',
            'region_id' => 'nullable|exists:regions,id',
        ];
    }
}
