<?php

namespace App\Http\Requests\Items;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'category_id' => 'nullable',
            'description' => 'nullable|string',
            'size' => 'nullable|string',
            'stock' => 'nullable|numeric',

            'images' => 'nullable|array|max:3',
            'images.*' => 'mimes:png,jpg,jpeg|max:2048',

            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
        ];
    }
}
