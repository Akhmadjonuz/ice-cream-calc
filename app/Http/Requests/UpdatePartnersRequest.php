<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:partners,id',
            'name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|regex:/^998[0-9]{9}$/',
            'address' => 'nullable|string|max:255',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'id.required' => 'Id is required',
            'id.integer' => 'Id must be integer',
            'id.exists' => 'Id must be exists in partners table',
            'name.string' => 'Name must be string',
            'name.max' => 'Name must be max 255 characters',
            'phone_number.string' => 'Phone number must be string',
            'phone_number.regex' => 'Phone number must be valid',
            'address.string' => 'Address must be string',
            'address.max' => 'Address must be max 255 characters',
        ];
    }
}
