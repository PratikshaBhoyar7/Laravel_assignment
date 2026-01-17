<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'sku' => 'required|string|unique:products,sku' . ($this->getMethod() === 'PUT' ? ',' . $this->route('product')->id : ''),
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|gte:0',
            'is_active' => 'boolean',
        ];
    }
}
