<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of EmployeeResource matches the internal employee API contract.
 */
class EmployeeResourceTest extends ResourceTestCase
{
    public function test_serializes_employee_contract(): void
    {
        $employee = new Employee();
        $employee->forceFill([
            'SID' => '987654321',
            'EMPLID' => '200000001',
            'AzureID' => 'azure-employee-1',
            'FirstName' => 'Test',
            'LastName' => 'Employee',
            'AliasName' => null,
            'WorkEmail' => 'employee.test@example.test',
            'WorkPhoneNumber' => '555-0200',
            'ADUserName' => 'employee.test',
            'UserPrincipalName' => 'employee.test@example.test',
        ]);

        $this->assertResourceContract(new EmployeeResource($employee), [
            'SID' => '987654321',
            'EMPLID' => '200000001',
            'AzureID' => 'azure-employee-1',
            'firstName' => 'Test',
            'lastName' => 'Employee',
            'aliasName' => null,
            'email' => 'employee.test@example.test',
            'phone' => '555-0200',
            'username' => 'employee.test',
            'UPN' => 'employee.test@example.test',
        ]);
    }
}
