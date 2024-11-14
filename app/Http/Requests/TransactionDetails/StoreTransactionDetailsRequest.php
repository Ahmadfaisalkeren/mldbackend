<?php

namespace App\Http\Requests\TransactionDetails;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionDetailsRequest extends FormRequest
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
            'transaction_id' => 'required',
            'item_id' => 'required',
            'quantity' => 'required|numeric',
            'return_date' => 'required|date',
            'status' => 'required|string|max:255',
        ];
    }
}
