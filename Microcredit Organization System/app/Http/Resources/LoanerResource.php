<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return
            isset($this->id)
                ? [
                    'isOk' => true,
                    'id' => $this->id,
                    'fullname' => $this->full_name,
                    'phone1' => $this->phone1,
                    'phone2' => $this->phone2,
                    'phone3' => $this->phone3,
                    'phone4' => $this->phone4,
                    'tin' => $this->tin,
                    'passport_number' => $this->passport_number,
                    'passport_issuer' => $this->passport_issuer,
                    'passport_issued_day' => $this->passport_issued_day,
                    'passport_issued_month' => $this->passport_issued_month,
                    'passport_issued_year' => $this->passport_issued_year,
                    'birth_day' => $this->birth_day,
                    'birth_month' => $this->birth_month,
                    'birth_year' => $this->birth_year,
                    'residence_address' => $this->residence_address,
                ]
                : [
                    'isOk' => false,
                ];
    }
}
