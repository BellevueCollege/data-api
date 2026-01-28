<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkFoundResource extends JsonResource
{
    public static $wrap = 'Links';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Link'         =>  $this->LinkText,
            'Description'  =>  $this->LinkDescr,
        ];
    }
}
