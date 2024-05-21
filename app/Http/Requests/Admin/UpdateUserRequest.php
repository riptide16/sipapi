<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Rules\Alphaspace;
use App\Rules\Alphanumericplus;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public $user;

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
        $this->user = User::findOrFail($this->route()->parameter('user'));

        return [
            'name' => ['sometimes', new Alphanumericplus(), 'min:3', 'max:191'],
            'username' => [
                'sometimes',
                new Alphanumericplus(),
                'min:3',
                'max:191',
                Rule::unique('users')->ignore($this->user),
            ],
            'email' => [
                'sometimes',
                'max:191',
                Rule::unique('users')->ignore($this->user),
            ],
            'password' => 'nullable|min:8|max:191|confirmed',
            'role_id' => 'sometimes|exists:roles,id',
            'status' => [
                'sometimes',
                Rule::in(User::statusList()),
            ],
            'region_id' => 'nullable|exists:regions,id',
        ];
    }
}
