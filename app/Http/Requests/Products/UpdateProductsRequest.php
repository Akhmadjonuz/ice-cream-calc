<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsRequest extends FormRequest
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
            'id' => 'required|integer|exists:products,id',
            'caterogy_id' => 'nullable|integer|exists:caterogy,id',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'count' => 'nullable|regex:/^\d+(\.\d{1,2})?$/|min:1',
            'type_id' => 'nullable|integer|exists:settings,id',
            'is_active' => 'nullable|boolean',
        ];
    }
}