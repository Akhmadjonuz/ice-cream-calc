<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class MakeProductsRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'count' => 'required|regex:/^\d+(\.\d{1,2})?$/|min:1',
            'materials' => 'required|array',
            'values' => 'required|array'
        ];
    }

    
    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product id is required',
            'product_id.integer' => 'Product id must be integer',
            'product_id.exists' => 'Product id must be exists in products table',
            'count.required' => 'Count is required',
            'count.regex' => 'Count must be float',
            'count.min' => 'Count must be min 1',
            'materials.required' => 'Materials is required',
            'materials.array' => 'Materials must be array',
            'values.required' => 'Values is required',
            'values.array' => 'Values must be array',
        ];
    }
}