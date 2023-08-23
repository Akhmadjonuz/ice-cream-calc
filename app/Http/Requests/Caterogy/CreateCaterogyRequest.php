<?php

namespace App\Http\Requests\Caterogy;

use Illuminate\Foundation\Http\FormRequest;

class CreateCaterogyRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            // 'type' => 'nullable|integer',
        ];
    }

    
    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be string',
            'name.max' => 'Name must be max 255 characters',
            // 'type.integer' => 'Type must be integer',
        ];
    }
}