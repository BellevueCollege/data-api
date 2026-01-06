<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'aliasName'     => $this->AliasName,
            'email'         => $this->WorkEmail,
            'phone'         => $this->WorkPhoneNumber,
            'username'      => $this->ADUserName
        ];
    }
}
