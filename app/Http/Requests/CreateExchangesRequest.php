<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExchangesRequest extends FormRequest
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
            'partner_id' => 'required|integer|exists:partners,id',
            'value' => 'required|integer',
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
            'partner_id.required' => 'Partner id is required',
            'partner_id.integer' => 'Partner id must be integer',
            'partner_id.exists' => 'Partner id must be exists in partners table',
            'value.required' => 'Value is required',
            'value.integer' => 'Value must be integer',
        ];
    }
}
