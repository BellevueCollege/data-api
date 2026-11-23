<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Bearer-protected internal employee lookup by username or UPN.
 *
 * Requests must use the internal API host; the same paths on the public host return 404.
 */
class InternalEmployeeApiTest extends ApiFeatureTestCase
{
    public function test_returns_401_when_not_authenticated(): void
    {
        $this->getJson($this->internalApiUrl('/api/v1/internal/employee/employee.test'))
            ->assertUnauthorized();
    }

    public function test_returns_403_when_client_lacks_read_employee_data_permission(): void
    {
        $headers = $this->withBearerToken([]);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/employee/employee.test'))
            ->assertForbidden();
    }

    public function test_returns_employee_by_username(): void
    {
        WarehouseFixtures::seedEmployeeTestUser();
        $headers = $this->withBearerToken(['read_employee_data']);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/employee/employee.test'))
            ->assertOk()
            ->assertJsonFragment(['username' => 'employee.test']);
    }

    public function test_returns_employee_by_upn_when_username_contains_at_sign(): void
    {
        WarehouseFixtures::seedEmployeeTestUser();
        $headers = $this->withBearerToken(['read_employee_data']);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/employee/employee.test@example.test'))
            ->assertOk()
            ->assertJsonFragment(['UPN' => 'employee.test@example.test']);
    }

    public function test_returns_empty_json_object_for_inactive_employee(): void
    {
        WarehouseFixtures::seedInactiveEmployee();
        $headers = $this->withBearerToken(['read_employee_data']);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/employee/inactive.test'))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_returns_empty_json_object_for_unknown_employee(): void
    {
        $headers = $this->withBearerToken(['read_employee_data']);

        $this->withHeaders($headers)
            ->getJson($this->internalApiUrl('/api/v1/internal/employee/nobody.here'))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_returns_404_on_public_host_for_internal_employee_route(): void
    {
        $headers = $this->withBearerToken(['read_employee_data']);

        $this->withHeaders($headers)
            ->getJson('/api/v1/internal/employee/employee.test')
            ->assertNotFound();
    }
}
