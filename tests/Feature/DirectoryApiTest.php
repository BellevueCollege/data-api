<?php

namespace Tests\Feature;

use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * Authenticated employee directory summary and detail by AD account name.
 */
class DirectoryApiTest extends ApiFeatureTestCase
{
    public function test_returns_401_when_not_authenticated(): void
    {
        $this->getJson('/api/v1/directory/employee/directory.test')
            ->assertUnauthorized();
    }

    public function test_returns_403_when_client_lacks_read_directory_data_permission(): void
    {
        $headers = $this->withBearerToken([]);

        $this->withHeaders($headers)
            ->getJson('/api/v1/directory/employee/directory.test')
            ->assertForbidden();
    }

    public function test_returns_directory_employee_by_username(): void
    {
        WarehouseFixtures::seedDirectoryEmployee();
        $headers = $this->withBearerToken(['read_directory_data']);

        $this->withHeaders($headers)
            ->getJson('/api/v1/directory/employee/directory.test')
            ->assertOk()
            ->assertJsonFragment(['email' => 'directory.test@example.test']);
    }

    public function test_returns_directory_employee_usernames(): void
    {
        WarehouseFixtures::seedDirectoryEmployee();
        $headers = $this->withBearerToken(['read_directory_data']);

        $this->withHeaders($headers)
            ->getJson('/api/v1/directory/employees')
            ->assertOk()
            ->assertJsonFragment(['username' => 'directory.test']);
    }

    public function test_returns_directory_employees_matching_display_name_substring(): void
    {
        WarehouseFixtures::seedDirectoryEmployee();
        $headers = $this->withBearerToken(['read_directory_data']);

        $this->withHeaders($headers)
            ->getJson('/api/v1/directory/employees/Directory')
            ->assertOk()
            ->assertJsonFragment(['username' => 'directory.test']);
    }

    public function test_returns_empty_collection_when_display_name_substring_does_not_match(): void
    {
        WarehouseFixtures::seedDirectoryEmployee();
        $headers = $this->withBearerToken(['read_directory_data']);

        $this->withHeaders($headers)
            ->getJson('/api/v1/directory/employees/NoMatchHere')
            ->assertOk()
            ->assertJson(['employees' => []]);
    }

    public function test_returns_empty_json_object_for_unknown_directory_employee(): void
    {
        $headers = $this->withBearerToken(['read_directory_data']);

        $this->withHeaders($headers)
            ->getJson('/api/v1/directory/employee/nobody.here')
            ->assertOk()
            ->assertExactJson([]);
    }
}
