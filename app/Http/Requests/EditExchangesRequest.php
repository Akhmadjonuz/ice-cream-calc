<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditExchangesRequest extends FormRequest
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
            'id' => 'required|integer|exists:exchanges,id',
            'product_id' => 'nullable|integer|exists:products,id',
            'partner_id' => 'nullable|integer|exists:partners,id',
            'value' => 'nullable|integer',
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
            'id.exists' => 'Id must be exists in exchanges table',
            'product_id.integer' => 'Product id must be integer',
            'product_id.exists' => 'Product id must be exists in products table',
            'partner_id.integer' => 'Partner id must be integer',
            'partner_id.exists' => 'Partner id must be exists in partners table',
            'value.integer' => 'Value must be integer',
        ];
    }
}