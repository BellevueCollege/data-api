<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\EmployeeDirectoryDetailResource;
use App\Models\EmployeeDirectory;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of EmployeeDirectoryDetailResource matches the directory detail API contract.
 */
class EmployeeDirectoryDetailResourceTest extends ResourceTestCase
{
    public function test_serializes_directory_detail_contract_using_working_title(): void
    {
        $employee = new EmployeeDirectory();
        $employee->forceFill([
            'FirstName' => 'Directory',
            'LastName' => 'User',
            'AKA' => null,
            'DisplayName' => 'Directory User',
            'WorkingTitle' => 'Analyst',
            'OfficialTitle' => 'Official Analyst',
            'DepartmentName' => 'IT',
            'BCCEmail' => 'directory.test@example.test',
            'WorkPhone' => '555-0300',
            'DisplayPhone' => '555-0300',
            'WorkOffice' => 'A1',
            'MailStop' => 'MS-1',
        ]);

        $this->assertResourceContract(new EmployeeDirectoryDetailResource($employee), [
            'firstName' => 'Directory',
            'lastName' => 'User',
            'aliasName' => null,
            'displayName' => 'Directory User',
            'title' => 'Analyst',
            'department' => 'IT',
            'email' => 'directory.test@example.test',
            'phone' => '555-0300',
            'displayPhone' => '555-0300',
            'office' => 'A1',
            'mailstop' => 'MS-1',
        ]);
    }

    public function test_serializes_directory_detail_contract_using_official_title_when_working_title_is_null(): void
    {
        $employee = new EmployeeDirectory();
        $employee->forceFill([
            'FirstName' => 'Directory',
            'LastName' => 'User',
            'AKA' => 'Dir',
            'DisplayName' => 'Directory User',
            'WorkingTitle' => null,
            'OfficialTitle' => 'Official Analyst',
            'DepartmentName' => 'IT',
            'BCCEmail' => 'directory.test@example.test',
            'WorkPhone' => '555-0300',
            'DisplayPhone' => '555-0300',
            'WorkOffice' => 'A1',
            'MailStop' => 'MS-1',
        ]);

        $this->assertResourceContract(new EmployeeDirectoryDetailResource($employee), [
            'firstName' => 'Directory',
            'lastName' => 'User',
            'aliasName' => 'Dir',
            'displayName' => 'Directory User',
            'title' => 'Official Analyst',
            'department' => 'IT',
            'email' => 'directory.test@example.test',
            'phone' => '555-0300',
            'displayPhone' => '555-0300',
            'office' => 'A1',
            'mailstop' => 'MS-1',
        ]);
    }
}
