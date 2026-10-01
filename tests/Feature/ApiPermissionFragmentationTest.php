<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\ApiFeatureTestCase;

/**
 * Ensures each API permission gate rejects clients that hold a different valid permission.
 */
class ApiPermissionFragmentationTest extends ApiFeatureTestCase
{
    /**
     * @param  list<string>  $grantedPermissions
     */
    #[DataProvider('jwtCrossPermissionDenials')]
    public function test_jwt_protected_route_returns_403_when_client_has_different_read_permission(
        array $grantedPermissions,
        string $path,
        bool $useInternalHost,
    ): void {
        $url = $useInternalHost ? $this->internalApiUrl($path) : $path;

        $this->withHeaders($this->withBearerToken($grantedPermissions))
            ->getJson($url)
            ->assertForbidden();
    }

    /**
     * @return array<string, array{0: list<string>, 1: string, 2: bool}>
     */
    public static function jwtCrossPermissionDenials(): array
    {
        return [
            'internal student denied for read_employee_data' => [
                ['read_employee_data'],
                '/api/v1/internal/student/student.test',
                true,
            ],
            'internal student denied for read_directory_data' => [
                ['read_directory_data'],
                '/api/v1/internal/student/student.test',
                true,
            ],
            'internal employee denied for read_student_data' => [
                ['read_student_data'],
                '/api/v1/internal/employee/employee.test',
                true,
            ],
            'internal employee denied for read_directory_data' => [
                ['read_directory_data'],
                '/api/v1/internal/employee/employee.test',
                true,
            ],
            'directory employee denied for read_student_data' => [
                ['read_student_data'],
                '/api/v1/directory/employee/directory.test',
                false,
            ],
            'directory employee denied for read_employee_data' => [
                ['read_employee_data'],
                '/api/v1/directory/employee/directory.test',
                false,
            ],
        ];
    }

    /**
     * @param  list<string>  $grantedPermissions
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('basicAuthCrossPermissionDenials')]
    public function test_basic_auth_route_returns_403_when_client_has_different_write_permission(
        array $grantedPermissions,
        string $path,
        array $payload,
    ): void {
        $client = $this->createApiClient($grantedPermissions);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson($path, $payload)
            ->assertForbidden();
    }

    /**
     * @return array<string, array{0: list<string>, 1: string, 2: array<string, mixed>}>
     */
    public static function basicAuthCrossPermissionDenials(): array
    {
        return [
            'pci test denied for write_user_questions only' => [
                ['write_user_questions'],
                '/api/v1/forms/pci/transaction/test',
                [
                    'id' => 12345,
                    'form_id' => 1,
                ],
            ],
            'copilot test denied for write_transactions only' => [
                ['write_transactions'],
                '/api/v1/copilot/userquestion/test',
                [
                    'question' => 'Hello',
                ],
            ],
        ];
    }
}
