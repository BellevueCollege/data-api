<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of StudentResource matches the internal student API contract.
 */
class StudentResourceTest extends ResourceTestCase
{
    public function test_serializes_student_contract(): void
    {
        $student = new Student();
        $student->forceFill([
            'SID' => '123456789',
            'EMPLID' => '100000001',
            'AzureID' => 'azure-student-1',
            'FirstName' => 'Test',
            'LastName' => 'Student',
            'Email' => 'student.test@example.test',
            'DaytimePhone' => '555-0100',
            'EveningPhone' => null,
            'NTUserName' => 'student.test',
            'UserPrincipalName' => 'student.test@example.test',
            'PrivateRecord' => false,
        ]);

        $this->assertResourceContract(new StudentResource($student), [
            'SID' => '123456789',
            'EMPLID' => '100000001',
            'AzureID' => 'azure-student-1',
            'firstName' => 'Test',
            'lastName' => 'Student',
            'email' => 'student.test@example.test',
            'phoneDaytime' => '555-0100',
            'phoneEvening' => null,
            'username' => 'student.test',
            'UPN' => 'student.test@example.test',
            'ferpaBlock' => false,
        ]);
    }
}
