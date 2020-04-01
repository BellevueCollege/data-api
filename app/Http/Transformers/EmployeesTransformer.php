<?php namespace App\Http\Transformers;

use App\Models\EmployeeDirectory;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class EmployeesTransformer extends TransformerAbstract {

    /**
    * Fractal transformer for an Employee, showing minimal info
    * YearQuarter should be output in the API.
    **/

    public function transform(EmployeeDirectory $emp)
    {
        $title = $emp->WorkingTitle !== NULL ? $emp->WorkingTitle : $emp->OfficialTitle;
        return [
            'username'     => $emp->ADAccountName,
        ];
    }

}
