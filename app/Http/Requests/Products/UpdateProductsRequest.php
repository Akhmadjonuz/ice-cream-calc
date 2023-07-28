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
            'caterogy_id' => 'required|integer|exists:caterogy,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'type_id' => 'required|integer|exists:settings,id',
            'is_active' => 'nullable|boolean',
        ];
    }
}
