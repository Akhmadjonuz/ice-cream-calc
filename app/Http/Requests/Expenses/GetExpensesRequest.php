<?php

namespace App\Http\Requests\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class GetExpensesRequest extends FormRequest
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
            'product_id' => 'nullable|integer|exists:products,id',
            'material_id' => 'nullable|integer|exists:products,id',
            'from_date' => 'nullable|string',
            'to_date' => 'nullable|string',
        ];
    }
}