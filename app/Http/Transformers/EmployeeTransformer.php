<?php namespace App\Http\Transformers;

use App\Models\Employee;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class EmployeeTransformer extends TransformerAbstract
{

    /**
    * Fractal transformer for a YearQuarter. Defines how data for a
    * YearQuarter should be output in the API.
    **/

    public function transform(Employee $emp)
    {
        return [
            'SID'           => $emp->SID,
            'EMPLID'        => $emp->EMPLID,
            'firstName'     => $emp->FirstName,
            'lastName'      => $emp->LastName,
            'aliasName'     => $emp->AliasName,
            'email'         => $emp->WorkEmail,
            'phone'         => $emp->WorkPhoneNumber,
            'username'      => $emp->ADUserName
        ];
    }
}
