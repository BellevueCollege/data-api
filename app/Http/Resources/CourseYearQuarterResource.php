<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SectionCollection;

class CourseYearQuarterResource extends JsonResource
{
    public static $wrap = 'classes';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Get the course description logic from the transformer
        $description = null;
        $course = $this->course;
        if ($course) {
            $allDesc = $course->coursedescriptions();
            if ($allDesc) {
                $activeDesc = $allDesc->activedescription($this->YearQuarterID)->first();
                if ($activeDesc) {
                    $description = utf8_encode($activeDesc->Description);
                }
            }
        }

        return [
            'title' => $this->title,
            'subject' => trim($this->Department),
            'courseNumber' => trim($this->CourseNumber),
            'description' => $description,
            'note' => $course->note ?? $this->note ?? null,
            /** @var int */
            'credits' => $this->course->Credits,
            'quarter' => $this->YearQuarterID,
            'strm' => $this->STRM,
            'isVariableCredits' => (bool)$course->isVariableCredits,
            'isCommonCourse' => (bool)$course->isCommonCourse,
            'sections' => [ 'data' => 
                new SectionCollection($this->sections),
            ],
        ];
    }
}
