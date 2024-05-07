<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Alphanumericplus implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_string($value) && preg_match('/^[A-Za-z0-9_]+$/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.alphanumericplus');
    }
}
