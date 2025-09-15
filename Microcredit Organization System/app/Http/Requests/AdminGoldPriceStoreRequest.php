<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminGoldPriceStoreRequest extends FormRequest
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
            'purity' => ['required', 'integer', 'gt:0'],
            'price' => ['required', 'gt:0'],
        ];
    }

    public function messages()
    {
        return [
            'purity.required' => 'Заполните поле "Проба"',
            'price.required' => 'Заполните поле "Цена"',
        ];
    }
}
