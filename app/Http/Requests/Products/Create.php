<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'code' => 'required|integer|digits:5',
            'quantity' => 'required|integer',
            'status' => 'required|between:0,1',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
            'desc' => 'required|string',
            'brand_id' => 'required|integer|exists:brands,id',
            'subcategory_id' => 'required|integer|exists:subcategories,id',
        ];
    }
}
