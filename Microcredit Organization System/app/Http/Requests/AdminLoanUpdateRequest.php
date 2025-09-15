<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoanUpdateRequest extends FormRequest
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
        $rules = [
            'fullname' => ['required', 'min:10'],
            'phone1' => ['required', 'min:9'],
            'phone2' => ['nullable', 'min:9'],
            'phone3' => ['nullable', 'min:9'],
            'phone4' => ['nullable', 'min:9'],
            'residence_address' => ['required', 'min:8'],
            'is_notifiable' => ['required'],

            'initial_sum' => ['required'],
            'grace_period' => ['nullable'],

            'lend_day' => ['required'],
            'lend_month' => ['required'],
            'lend_year' => ['required'],

            'image' => ['nullable'],

            'item_1_name' => ['required'],
            'item_1_purity' => ['required'],
            'item_1_weight' => ['required'],
            'item_1_pure_weight' => ['required'],
            'item_1_count' => ['required'],
            'item_1_price' => ['required']
        ];

        for($i = 2; $i <= 10; $i++) {
            $rules['item_'.$i.'_name'] = ['required_with:item_'.$i.'_purity,item_'.$i.'_weight,item_'.$i.'_pure_weight,item_'.$i.'_count,item_'.$i.'_price'];
            $rules['item_'.$i.'_purity'] = ['required_with:item_'.$i.'_name,item_'.$i.'_weight,item_'.$i.'_pure_weight,item_'.$i.'_count,item_'.$i.'_price'];
            $rules['item_'.$i.'_weight'] = ['required_with:item_'.$i.'_name,item_'.$i.'_purity,item_'.$i.'_pure_weight,item_'.$i.'_count,item_'.$i.'_price'];
            $rules['item_'.$i.'_pure_weight'] = ['required_with:item_'.$i.'_name,item_'.$i.'_purity,item_'.$i.'_weight,item_'.$i.'_count,item_'.$i.'_price'];
            $rules['item_'.$i.'_count'] = ['required_with:item_'.$i.'_name,item_'.$i.'_purity,item_'.$i.'_weight,item_'.$i.'_pure_weight,item_'.$i.'_price'];
            $rules['item_'.$i.'_price'] = ['required_with:item_'.$i.'_name,item_'.$i.'_purity,item_'.$i.'_weight,item_'.$i.'_pure_weight,item_'.$i.'_count'];
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'fullname.required' => 'Заполните поле "ФИО"',
            'phone1.required' => 'Заполните поле "Телефон1"',
            'phone1.min' => 'Неправильный номер в поле "Телефон1"',
            'phone2.min' => 'Неправильный номер в поле "Телефон2"',
            'phone3.min' => 'Неправильный номер в поле "Телефон3"',
            'phone4.min' => 'Неправильный номер в поле "Телефон4"',

            'lend_day.required' => 'Заполните поле "День выдачи кредита"',
            'lend_month.required' => 'Заполните поле "Месяц выдачи кредита"',
            'lend_year.required' => 'Заполните поле "Год выдачи кредита"',

            'residence_address.required' => 'Заполните поле "Адрес проживания"',
            'residence_address.min' => 'Неправильный адрес проживания',

            'initial_sum.required' => 'Заполните поле "Сумма"',
        ];

        for($i = 1; $i <= 10; $i++) {
            $messages['item_'.$i.'_name.required'] = 'Заполните поле "Залог '.$i.' название"';
            $messages['item_'.$i.'_purity.required'] = 'Заполните поле "Залог '.$i.' проба"';
            $messages['item_'.$i.'_weight.required'] = 'Заполните поле "Залог '.$i.' весь"';
            $messages['item_'.$i.'_pure_weight.required'] = 'Заполните поле "Залог '.$i.' чистый весь"';
            $messages['item_'.$i.'_count.required'] = 'Заполните поле "Залог '.$i.' количество"';
            $messages['item_'.$i.'_price.required'] = 'Заполните поле "Залог '.$i.' цена"';

            $messages['item_'.$i.'_name.required_with'] = 'Залог '.$i.' не все поля заполнены';
            $messages['item_'.$i.'_purity.required_with'] = 'Залог '.$i.' не все поля заполнены';
            $messages['item_'.$i.'_weight.required_with'] = 'Залог '.$i.' не все поля заполнены';
            $messages['item_'.$i.'_pure_weight.required_with'] = 'Залог '.$i.' не все поля заполнены';
            $messages['item_'.$i.'_count.required_with'] = 'Залог '.$i.' не все поля заполнены';
            $messages['item_'.$i.'_price.required_with'] = 'Залог '.$i.' не все поля заполнены';
        }

        return $messages;
    }
}
