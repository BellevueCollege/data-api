<?php

namespace App\Http\Resources;

use App\Http\Resources\SubjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubjectCollection extends ResourceCollection
{

    public $collects = SubjectResource::class;

    public static $wrap = 'subjects';
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
