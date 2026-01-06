<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class EmployeeDirectoryDetailResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $title = $this->WorkingTitle !== NULL ? $this->WorkingTitle : $this->OfficialTitle;

        // Remove whitespace and control characters
        $title = preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', $title);

        return [
            'firstName'     => $this->FirstName,
            'lastName'      => $this->LastName,
            /** @var string|null Alias/Nickname */
            'aliasName'     => $this->AKA,
            'displayName'   => $this->DisplayName,
            /** @var string|null Title */
            'title'         => $title,
            'department'    => $this->DepartmentName,
            'email'         => $this->BCCEmail,
            'phone'         => $this->WorkPhone,
            'displayPhone'  => $this->DisplayPhone,
            'office'        => $this->WorkOffice,
            'mailstop'      => $this->MailStop,
        ];
    }
}
