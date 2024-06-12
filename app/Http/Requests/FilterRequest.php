<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class FilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function failedValidation(Validator $validator)
    {
        return $validator;
    }

    public function rules()
    {
        return [
            'status' => 'nullable|in:ASC,DESC',
            'group_id' => 'nullable|in:ASC,DESC',
            'amount' => 'nullable|in:ASC,DESC',
        ];
    }
}
