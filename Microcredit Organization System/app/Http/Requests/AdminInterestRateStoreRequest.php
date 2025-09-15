<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminInterestRateStoreRequest extends FormRequest
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
            'sum_from' => ['required', 'integer', 'gt:0'],
            'sum_to' => ['required', 'integer', 'gt:0'],
            'rate' => ['required', 'numeric', 'gt:0']
        ];
    }

    public function messages()
    {
        return [
            'sum_from.required' => 'Заполните поле "Сумма от"',
            'sum_to.required' => 'Заполните поле "Сумма до"',
            'rate.required' => 'Заполните поле "Процента"',
            'sum_from.gt' => '"Сумма от" должен быть больше 0',
            'sum_to.gt' => '"Сумма до" должен быть больше 0',
            'rate.gt' => '"Процент" должен быть больше 0',
        ];
    }
}
