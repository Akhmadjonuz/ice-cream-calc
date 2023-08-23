<?php

namespace App\Http\Requests\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class GetExpensesRequest extends FormRequest
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
            'product_id' => 'nullable|integer|exists:products,id',
            'material_id' => 'nullable|integer|exists:products,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ];
    }

    
    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'product_id.integer' => 'Product id must be integer',
            'product_id.exists' => 'Product id must be exists in products table',
            'material_id.integer' => 'Material id must be integer',
            'material_id.exists' => 'Material id must be exists in products table',
            'from_date.date' => 'From date must be date',
            'to_date.date' => 'To date must be date',
        ];
    }
}