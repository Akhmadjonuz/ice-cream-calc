<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDebtsRequest extends FormRequest
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
            'partner_id' => 'required|exists:partners,id',
            'name' => 'nullable|string',
            'value' => 'nullable|string',
            'type' => 'nullable|string',
            'amount' => 'nullable|integer',
            'given_amount' => 'required|integer',
            'other' => 'required|boolean',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
