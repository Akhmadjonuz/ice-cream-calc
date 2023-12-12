<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class MakeProductsRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'count' => 'required|regex:/^\d+(\.\d{1,2})?$/|min:1',
            'materials' => 'required|array',
            'values' => 'required|array'
        ];
    }
}