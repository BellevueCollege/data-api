<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'SID'           => $this->SID,
            'EMPLID'        => $this->EMPLID,
            'firstName'     => $this->FirstName,
            'lastName'      => $this->LastName,
            'email'         => $this->Email,
            'phoneDaytime'  => $this->DaytimePhone,
            'phoneEvening'  => $this->EveningPhone,
            'username'      => $this->NTUserName,
            'ferpaBlock'    => $this->PrivateRecord,
        ];
    }
}
