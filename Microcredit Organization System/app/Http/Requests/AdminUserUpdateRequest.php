<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserUpdateRequest extends FormRequest
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
            'first_name' => ['required', 'min:6'],
            'last_name' => ['required', 'min:6'],
            'phone' => ['required', 'min:5'],
            'password' => ['nullable', 'min:5'],
            'cashbox_id' => ['required'],
            'user_license' => ['nullable'],
            'role' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Заполните поле "Имя"',
            'last_name.required' => 'Заполните поле "Фамилия"',
            'phone.required' => 'Заполните поле "Телефон"',
            'cashbox_id.required' => 'Заполните поле "Касса"',
            'first_name.min' => 'Поле "Имя" меньше 6 символов',
            'last_name.min' => 'Поле "Фамилия" меньше 6 символов',
            'phone.min' => 'Поле "Телефон" меньше 5 символов',
            'password.min' => 'Поле "Пароль" меньше 5 символов',
        ];
    }
}
