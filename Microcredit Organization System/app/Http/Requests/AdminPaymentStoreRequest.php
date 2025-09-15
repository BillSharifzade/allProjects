<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPaymentStoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'interest_sum' => ['required_without:principal_sum'],
            'principal_sum' => ['required_without:interest_sum'],
            'paid_month' => ['required'],
            'paid_day' => ['required'],
            'paid_year' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'interest_sum.required_without' => 'Заполните поле "Сумма процента"',
            'principal_sum.required_without' => 'Заполните поле "Сумма основного кредита"',
        ];
    }
}
