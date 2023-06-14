<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPartnersRequest extends FormRequest
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
            'type' => 'required|string|in:debtor,partner',
            'id' => 'nullable|integer|exists:partners,id',
            'from_date' => 'nullable|string|date_format:Y-m-d H:i',
            'to_date' => 'nullable|string|date_format:Y-m-d H:i',
        ];
    }
}
