<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\EmployeeDirectorySummaryResource;
use App\Models\EmployeeDirectory;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of EmployeeDirectorySummaryResource matches the directory summary API contract.
 */
class EmployeeDirectorySummaryResourceTest extends ResourceTestCase
{
    public function test_serializes_directory_summary_contract(): void
    {
        $employee = new EmployeeDirectory();
        $employee->forceFill([
            'ADAccountName' => 'directory.test',
        ]);

        $this->assertResourceContract(new EmployeeDirectorySummaryResource($employee), [
            'username' => 'directory.test',
        ]);
    }
}
