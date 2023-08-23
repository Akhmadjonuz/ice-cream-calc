<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductsRequest extends FormRequest
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
            'caterogy_id' => 'required|integer|exists:caterogy,id',
            'name' => 'required|string|max:255',
            // regex for float
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'type_id' => 'required|integer|exists:settings,id',
            'cyrrency' => 'required|boolean',
            // 0 - UZS 1 - USD
            'type' => 'required|boolean', // 0 - icecream 1 - other
        ];
    }

    
    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'caterogy_id.required' => 'Caterogy id is required',
            'caterogy_id.integer' => 'Caterogy id must be integer',
            'caterogy_id.exists' => 'Caterogy id must be exists in caterogy table',
            'name.required' => 'Name is required',
            'name.string' => 'Name must be string',
            'name.max' => 'Name must be max 255 characters',
            'price.required' => 'Price is required',
            'price.regex' => 'Price must be float',
            'type_id.required' => 'Type id is required',
            'type_id.integer' => 'Type id must be integer',
            'type_id.exists' => 'Type id must be exists in settings table',
            'cyrrency.required' => 'Cyrrency is required',
            'cyrrency.boolean' => 'Cyrrency must be boolean',
            'type.required' => 'Type is required',
            'type.boolean' => 'Type must be boolean',
        ];
    }
}