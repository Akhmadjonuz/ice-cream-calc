<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsRequest extends FormRequest
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
            'id' => 'required|integer|exists:products,id',
            'caterogy_id' => 'nullable|integer|exists:caterogy,id',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'count' => 'nullable|regex:/^\d+(\.\d{1,2})?$/|min:1',
            'type_id' => 'nullable|integer|exists:settings,id',
            'is_active' => 'nullable|boolean',
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
            'id.exists' => 'Id must be exists in products table',
            'caterogy_id.integer' => 'Caterogy id must be integer',
            'caterogy_id.exists' => 'Caterogy id must be exists in caterogy table',
            'name.string' => 'Name must be string',
            'name.max' => 'Name must be max 255 characters',
            'price.regex' => 'Price must be float',
            'count.regex' => 'Count must be float',
            'count.min' => 'Count must be min 1',
            'type_id.integer' => 'Type id must be integer',
            'type_id.exists' => 'Type id must be exists in settings table',
            'is_active.boolean' => 'Is active must be boolean',
        ];
    }
}