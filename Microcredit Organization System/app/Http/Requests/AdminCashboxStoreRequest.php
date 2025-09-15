<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCashboxStoreRequest extends FormRequest
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
            'name' => ['required', 'min:6'],
            'nickname' => ['required', 'min:6'],
            'address' => ['required', 'min:6'],
            'phone' => ['required', 'min:5'],
            'license' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Заполните поле "Название"',
            'nickname.required' => 'Заполните поле "Внутреннее название"',
            'address.required' => 'Заполните поле "Адрес"',
            'phone.required' => 'Заполните поле "Телефон"',
            'name.min' => 'Поле "Название" меньше 6 символов',
            'nickname.min' => 'Поле "Внутреннее название" меньше 6 символов',
            'address.min' => 'Поле "Адрес" меньше 6 символов',
            'phone.min' => 'Поле "Телефон" меньше 5 символов',
        ];
    }
}
