<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Public class schedule rows for a PeopleSoft class identifier.
 */
class ClassScheduleApiTest extends ApiFeatureTestCase
{
    public function test_returns_schedules_for_class_id(): void
    {
        WarehouseFixtures::seedClassSchedule();

        $this->get('/api/v1/schedules/PS-SCHED-1')
            ->assertOk()
            ->assertJsonFragment([
                'instructor' => 'Schedule Instructor',
            ]);
    }

    public function test_returns_empty_class_schedules_for_unknown_class_id(): void
    {
        $this->get('/api/v1/schedules/unknown-class-id')
            ->assertOk()
            ->assertJson(['classSchedules' => []]);
    }
}
