<?php

namespace App\Http\Requests;

class TransactionRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'transaction_user_id' => 'required',
            'transaction_source_id' => 'required',
            'transaction_type_id' => 'required',
            'transaction_date' => 'required',
            'transaction_total' => 'required',
            'transaction_description' => 'required',
        ];
    }
}
