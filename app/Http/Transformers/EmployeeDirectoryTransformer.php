<?php namespace App\Http\Transformers;

use App\Models\EmployeeDirectory;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class EmployeeDirectoryTransformer extends TransformerAbstract {

    /**
    * Fractal transformer for an employee. Based on Directory.
    * YearQuarter should be output in the API.
    **/

    public function transform(EmployeeDirectory $emp)
    {
        $title = $emp->WorkingTitle !== NULL ? $emp->WorkingTitle : $emp->OfficialTitle;
        return [
            'firstName'     => $emp->FirstName,
            'lastName'      => $emp->LastName,
            'aliasName'     => $emp->AKA,
            'displayName'   => $emp->DisplayName,
            'title'         => $title,
            'department'    => $emp->DepartmentName,
            'email'         => $emp->BCCEmail,
            'phone'         => $emp->WorkPhone,
            'displayPhone'  => $emp->DisplayPhone,
            'office'        => $emp->WorkOffice,
            'mailstop'      => $emp->MailStop,
        ];
    }

}
