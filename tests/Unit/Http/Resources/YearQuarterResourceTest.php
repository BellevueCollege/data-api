<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\YearQuarterResource;
use App\Models\YearQuarter;
use Tests\Support\ResourceTestCase;

/**
 * JSON shape of YearQuarterResource matches the quarter API contract.
 */
class YearQuarterResourceTest extends ResourceTestCase
{
    public function test_serializes_year_quarter_contract(): void
    {
        $yearQuarter = new YearQuarter();
        $yearQuarter->forceFill([
            'YearQuarterID' => 'C124',
            'STRM' => '2241',
            'Title' => 'SPRING 2024',
        ]);

        $this->assertResourceContract(new YearQuarterResource($yearQuarter), [
            'quarter' => 'C124',
            'strm' => '2241',
            'title' => 'SPRING 2024',
        ]);
    }
}
