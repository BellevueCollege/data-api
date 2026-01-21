<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\EmployeeDirectorySummaryResource;

class EmployeeDirectorySummaryCollection extends ResourceCollection
{
    /**
     * The resource that this collection belongs to.
     *
     * @var string
     */
    public $collects = EmployeeDirectorySummaryResource::class;

    /**
     * The "wrap" value to be used when this collection is serialized.
     *
     * @var string
     */
    public static $wrap = 'employees';

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
