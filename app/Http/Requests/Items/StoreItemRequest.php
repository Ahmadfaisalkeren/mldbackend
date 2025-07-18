<?php

namespace App\Http\Requests\Items;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'description' => 'required|string',
            'size' => 'required|string',
            'stock' => 'required|numeric',
            'images' => 'required|array|min:1',
            'images.*' => 'mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
