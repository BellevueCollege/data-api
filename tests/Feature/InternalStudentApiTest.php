<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Bearer-protected internal student lookup by username or UPN.
 *
 * Requests must use the internal API host; the same paths on the public host return 404.
 */
class InternalStudentApiTest extends ApiFeatureTestCase
{
    public function test_returns_401_when_not_authenticated(): void
    {
        $this->getJson($this->internalApiUrl('/api/v1/internal/student/student.test'))
            ->assertUnauthorized();
    }

    public function test_returns_403_when_client_lacks_read_student_data_permission(): void
    {
        $headers = $this->withBearerToken([]);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/student/student.test'))
            ->assertForbidden();
    }

    public function test_returns_student_by_username(): void
    {
        WarehouseFixtures::seedStudentTestUser();
        $headers = $this->withBearerToken(['read_student_data']);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/student/student.test'))
            ->assertOk()
            ->assertJsonFragment(['username' => 'student.test']);
    }

    public function test_returns_student_by_upn_when_username_contains_at_sign(): void
    {
        WarehouseFixtures::seedStudentTestUser();
        $headers = $this->withBearerToken(['read_student_data']);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/student/student.test@example.test'))
            ->assertOk()
            ->assertJsonFragment(['UPN' => 'student.test@example.test']);
    }

    public function test_returns_empty_json_object_for_unknown_student(): void
    {
        $headers = $this->withBearerToken(['read_student_data']);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/student/nobody.here'))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_returns_404_on_public_host_for_internal_student_route(): void
    {
        $headers = $this->withBearerToken(['read_student_data']);

        $this->withHeaders($headers)
            ->getJson('/api/v1/internal/student/student.test')
            ->assertNotFound();
    }
}
