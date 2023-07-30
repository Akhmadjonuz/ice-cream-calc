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
            'from_date' => 'nullable|string',
            'to_date' => 'nullable|string',
        ];
    }
}
