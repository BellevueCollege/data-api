<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of SubjectResource matches the subjects API contract.
 */
class SubjectResourceTest extends ResourceTestCase
{
    public function test_serializes_subject_contract(): void
    {
        $subject = new Subject();
        $subject->forceFill([
            'SUBJECT' => 'ACCT',
            'DESCR' => 'Accounting',
        ]);

        $this->assertResourceContract(new SubjectResource($subject), [
            'subject' => 'ACCT',
            'name' => 'Accounting',
        ]);
    }
}
