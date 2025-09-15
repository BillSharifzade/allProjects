<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GoldPriceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(count($this->collection) > 0) {
            return [
                'isOk' => true,
                'goldPrices' => $this->collection->map(function($item) {
                    return [
                        'purity' => $item->purity,
                        'price' => round($item->price, 2),
                    ];
                })
            ];
        } else {
            return [
                'isOk' => false,
            ];
        }
    }
}
