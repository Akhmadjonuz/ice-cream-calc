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


    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
            'type.required' => 'Type is required',
            'type.string' => 'Type must be string',
            'type.in' => 'Type must be in debtor,partner',
            'id.integer' => 'Id must be integer',
            'id.exists' => 'Id must be exists in partners table',
            'from_date.string' => 'From date must be string',
            'from_date.date_format' => 'From date must be date format Y-m-d H:i',
            'to_date.string' => 'To date must be string',
            'to_date.date_format' => 'To date must be date format Y-m-d H:i',
        ];
    }
}