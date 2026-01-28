<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public static $wrap = 'course';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //filter to get the active course description
        $all_desc = $this->coursedescriptions();
        $cd_desc = null;

        if (!is_null($all_desc)) {
            $cd_active = $all_desc->activedescription()->first();

            if (!empty($cd_active)) {
                $cd_desc = utf8_encode($cd_active->Description);
            }
        }

        return [
            'title'             => $this->title,
            'subject'           => $this->CourseSubject,
            'courseNumber'      => $this->CatalogNumber,
            'courseId'          => $this->CourseID,
            'ctcCourseId'       => $this->PSCourseID,
            'description'       => $cd_desc,
            'note'              => $this->note,
            'credits'           => $this->Credits,
            'isVariableCredits' => (bool)$this->VariableCredits,
            'isCommonCourse'    => (bool)$this->isCommonCourse,
        ];
    }
}
