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
            'name' => 'nullable|string|max:30',
            'partner_id' => 'required|integer|exists:partners,id',
            'value' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255|exists:settings,value',
            'amount' => 'nullable|integer',
            'given_amount' => 'required|integer',
            'other' => 'required|boolean|max:255',
        ];
    }
}
