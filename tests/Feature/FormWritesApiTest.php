<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * PCI and evaluation form write endpoints with HTTP basic auth and client permissions.
 */
class FormWritesApiTest extends ApiFeatureTestCase
{
    public function test_pci_transaction_returns_401_without_basic_auth(): void
    {
        $this->postJson('/api/v1/forms/pci/transaction', [])
            ->assertUnauthorized();
    }

    public function test_pci_transaction_returns_403_without_write_transactions_permission(): void
    {
        $client = $this->createApiClient([]);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction', [
                'id' => 12345,
                'form_id' => 1,
            ])
            ->assertForbidden();
    }

    public function test_pci_transaction_returns_422_when_required_fields_are_missing(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction', [])
            ->assertStatus(422);
    }

    public function test_pci_transaction_test_endpoint_does_not_write_to_database(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction/test', [
                'id' => 12345,
                'form_id' => 1,
            ])
            ->assertOk()
            ->assertJsonFragment(['NOTHING ACTUALLY WRITTEN TO DATABASE!']);
    }

    public function test_pci_transaction_returns_200_when_stored_procedure_succeeds(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = \Mockery::mock();
        $connection->shouldReceive('update')->once()->andReturn(1);
        DB::shouldReceive('connection')->with('pciforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction', [
                'id' => 12345,
                'form_id' => 1,
            ])
            ->assertOk()
            ->assertJsonFragment(['message' => 'Transaction successfully written to database.']);
    }

    public function test_pci_transaction_returns_503_when_stored_procedure_fails(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = \Mockery::mock();
        $connection->shouldReceive('update')->once()->andThrow(new \RuntimeException('procedure failed'));
        DB::shouldReceive('connection')->with('pciforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction', [
                'id' => 12345,
                'form_id' => 1,
            ])
            ->assertStatus(503);
    }

    public function test_graduation_application_parses_program_code_from_program_string(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = \Mockery::mock();
        $connection->shouldReceive('update')->once()->andReturn(1);
        DB::shouldReceive('connection')->with('evalforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'sid' => 123456789,
                'program' => 'Bachelor of Science | ABC123',
            ])
            ->assertOk();
    }

    public function test_graduation_application_resolves_sid_from_email_when_sid_is_omitted(): void
    {
        WarehouseFixtures::seedStudentTestUser();
        $client = $this->createApiClient(['write_transactions']);

        $connection = \Mockery::mock();
        $connection->shouldReceive('update')->once()->andReturn(1);
        DB::shouldReceive('connection')->with('evalforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'email' => 'student.test@example.test',
                'program_code' => 'ABC123',
            ])
            ->assertOk();
    }

    public function test_graduation_application_errors_when_email_has_no_matching_student(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'email' => 'missing@example.test',
                'program_code' => 'ABC123',
            ])
            ->assertStatus(500);
    }

    public function test_transfer_credit_evaluation_writes_via_stored_procedure(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = \Mockery::mock();
        $connection->shouldReceive('update')->once()->andReturn(1);
        DB::shouldReceive('connection')->with('evalforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/transfer-credit-evaluation', [
                'sid' => 123456789,
                'program' => 'Transfer | TRN001',
            ])
            ->assertOk();
    }

    public function test_copilot_user_question_returns_403_without_write_user_questions_permission(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion', ['question' => 'Hello'])
            ->assertForbidden();
    }

    public function test_copilot_user_question_test_endpoint_does_not_write_to_database(): void
    {
        $client = $this->createApiClient(['write_user_questions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion/test', ['question' => 'Hello'])
            ->assertOk();
    }

    public function test_copilot_user_question_returns_200_when_stored_procedure_succeeds(): void
    {
        $client = $this->createApiClient(['write_user_questions']);

        $connection = \Mockery::mock();
        $connection->shouldReceive('update')->once()->andReturn(1);
        DB::shouldReceive('connection')->with('copilot')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion', ['question' => 'Hello'])
            ->assertOk()
            ->assertJsonFragment(['message' => 'Transaction successfully written to database.']);
    }
}
