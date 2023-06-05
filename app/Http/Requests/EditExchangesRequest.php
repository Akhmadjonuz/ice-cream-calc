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
            'name' => 'required|string|max:30',
            'parter_id' => 'required|integer|exists:partners,id',
            'value' => 'required|string|max:255',
            'type' => 'required|string|max:255|exists:settings,value',
            'amount' => 'required|integer',
            'given_amount' => 'nullable|integer',
            'other' => 'nullable|string|max:255',
        ];
    }
}