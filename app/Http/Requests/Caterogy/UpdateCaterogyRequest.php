<?php

namespace App\Http\Requests\Caterogy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCaterogyRequest extends FormRequest
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
            'id' => 'required|integer|exists:caterogy,id',
            'name' => 'nullable|string|max:255',
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
            'id.exists' => 'Id must be exists in caterogy table',
            'name.string' => 'Name must be string',
            'name.max' => 'Name must be max 255 characters',
        ];
    }
}