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
            'name' => 'required|string',
            'value' => 'required|string',
            'car' => 'required|string',
            'type' => 'required|string',
            'amount' => 'required|integer',
            'given_amount' => 'required|integer',
        ];
    }
}
