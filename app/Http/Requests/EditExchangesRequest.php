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
}