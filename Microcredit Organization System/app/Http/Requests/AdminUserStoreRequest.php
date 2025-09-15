<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserStoreRequest extends FormRequest
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
            'login' => ['required', 'min:5', 'unique:users', 'regex:/(^([a-zA-z_-]+)(\d+)?$)/u'],
            'password' => ['required', 'min:5'],
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
            'login.required' => 'Заполните поле "Логин"',
            'password.required' => 'Заполните поле "Пароль"',
            'cashbox_id.required' => 'Заполните поле "Касса"',
            'first_name.min' => 'Поле "Имя" меньше 6 символов',
            'last_name.min' => 'Поле "Фамилия" меньше 6 символов',
            'phone.min' => 'Поле "Телефон" меньше 5 символов',
            'login.min' => 'Поле "Логин" меньше 5 символов',
            'login.unique' => 'Логин существует',
            'password.min' => 'Поле "Пароль" меньше 5 символов',
            'login.regex' => 'Недопустимый формат логина',
        ];
    }
}
