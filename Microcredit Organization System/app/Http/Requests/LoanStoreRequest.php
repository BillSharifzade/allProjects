<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanStoreRequest extends FormRequest
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
            'collateral_type' => ['required'],
            'passport_number' => ['required'],
            'tin' => ['nullable'],
            'passport_issuer' => ['required', 'min:10'],
            'fullname' => ['required', 'min:10'],
            'phone1' => ['required', 'min:9'],
            'phone2' => ['nullable', 'min:9'],
            'phone3' => ['nullable', 'min:9'],
            'phone4' => ['nullable', 'min:9'],
            'residence_address' => ['required', 'min:8'],
            'initial_sum' => ['required'],
            'passport_issued_day' => ['required'],
            'passport_issued_month' => ['required'],
            'passport_issued_year' => ['required'],

            'in_audit' => ['required'],
            'is_notifiable' => ['required'],

            'birth_day' => ['required'],
            'birth_month' => ['required'],
            'birth_year' => ['required'],

            'image' => ['nullable'],
        ];

        if($this->collateral_type == 1) {
            $rules['item_1_name'] = ['required'];
            $rules['item_1_purity'] = ['required'];
            $rules['item_1_weight'] = ['required'];
            $rules['item_1_pure_weight'] = ['required'];
            $rules['item_1_count'] = ['required'];
            $rules['item_1_price'] = ['required'];

            for($i = 2; $i <= 10; $i++) {
                $rules['item_'.$i.'_name'] = ['required_with:item_'.$i.'_weight,item_'.$i.'_pure_weight,item_'.$i.'_count,item_'.$i.'_price'];
                $rules['item_'.$i.'_purity'] = ['required'];
                $rules['item_'.$i.'_weight'] = ['required_with:item_'.$i.'_name,item_'.$i.'_pure_weight,item_'.$i.'_count,item_'.$i.'_price'];
                $rules['item_'.$i.'_pure_weight'] = ['required_with:item_'.$i.'_name,item_'.$i.'_weight,item_'.$i.'_count,item_'.$i.'_price'];
                $rules['item_'.$i.'_count'] = ['required_with:item_'.$i.'_name,item_'.$i.'_weight,item_'.$i.'_pure_weight,item_'.$i.'_price'];
                $rules['item_'.$i.'_price'] = ['required_with:item_'.$i.'_name,item_'.$i.'_weight,item_'.$i.'_pure_weight,item_'.$i.'_count'];
            }
        }

        if($this->collateral_type == 2) {
            $rules['vehicle_brand'] = ['required'];
            $rules['vehicle_year'] = ['required'];
            $rules['vehicle_color'] = ['required'];
            $rules['vehicle_plate_number'] = ['required'];
            $rules['vehicle_engine'] = ['required'];
            $rules['vehicle_location'] = ['required'];
            $rules['vehicle_description'] = ['required'];
            $rules['vehicle_mileage'] = ['required'];
            $rules['vehicle_transmission'] = ['required'];
            $rules['vehicle_gas'] = ['required'];
        }

        if($this->collateral_type == 3) {
            $rules['phone_brand'] = ['required'];
            $rules['phone_model'] = ['required'];
            $rules['phone_storage_gb'] = ['nullable','integer','min:1'];
            $rules['phone_color'] = ['nullable','string'];
            $rules['phone_condition'] = ['nullable','string'];
            $rules['phone_imei'] = ['nullable','string'];
            $rules['phone_description'] = ['nullable','string'];
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'passport_number.required' => 'Заполните поле "Номер паспорта"',
            'passport_issuer.required' => 'Заполните поле "Орган, выдавший паспорт паспорта"',
            'fullname.required' => 'Заполните поле "ФИО"',
            'phone1.required' => 'Заполните поле "Телефон1"',
            'residence_address.required' => 'Заполните поле "Адрес проживания"',
            'initial_sum.required' => 'Заполните поле "Сумма"',
            'phone1.min' => 'Неправильный номер в поле "Телефон1"',
            'phone2.min' => 'Неправильный номер в поле "Телефон2"',
            'phone3.min' => 'Неправильный номер в поле "Телефон3"',
            'phone4.min' => 'Неправильный номер в поле "Телефон4"',

            'residence_address.min' => 'Неправильный адрес проживания',
            'vehicle_brand' => 'Заполните поле "Марка"',
            'vehicle_year' => 'Заполните поле "Год"',
            'vehicle_color' => 'Заполните поле "Цвет"',
            'vehicle_plate_number' => 'Заполните поле "Гос. номер"',
            'vehicle_engine' => 'Заполните поле "Двигатель"',
            'vehicle_location' => 'Заполните поле "Место хранения"',
            'vehicle_description' => 'Заполните поле "Описание"',
            'vehicle_mileage' => 'Заполните поле "Пробег"',
            'vehicle_transmission' => 'Заполните поле "Трансмиссия"',
            'vehicle_gas' => 'Заполните поле "Топливо"',

            'phone_brand' => 'Заполните поле "Бренд телефона"',
            'phone_model' => 'Заполните поле "Модель телефона"',
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
