<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\CourseYearQuarterResource;

class CourseYearQuarterCollection extends ResourceCollection
{
    public $collects = CourseYearQuarterResource::class;
    
    public static $wrap = 'classes';
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
