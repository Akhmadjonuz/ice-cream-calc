<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetExchangesRequest extends FormRequest
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
            'caterogy_id' => 'nullable|integer',
            'partner_id' => 'nullable|integer',
            'type_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
            'cyrrency' => 'nullable|boolean',
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
            'caterogy_id.integer' => 'Caterogy id must be integer',
            'partner_id.integer' => 'Partner id must be integer',
            'type_id.integer' => 'Type id must be integer',
            'product_id.integer' => 'Product id must be integer',
            'cyrrency.boolean' => 'Cyrrency must be boolean',
            'from_date.date' => 'From date must be date',
            'to_date.date' => 'To date must be date',
        ];
    }
}
