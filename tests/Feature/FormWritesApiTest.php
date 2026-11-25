<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\ApiFeatureTestCase;
use Tests\Support\WarehouseFixtures;

/**
 * PCI and evaluation form write endpoints with HTTP basic auth and client permissions.
 */
class FormWritesApiTest extends ApiFeatureTestCase
{
    /**
     * @param  list<string>  $expectedParams  SQL Server @Name values (e.g. TranStatus, grSID)
     */
    private function assertStoredProcedureCall(string $sql, string $procedure, array $expectedParams): void
    {
        $this->assertStringStartsWith('EXEC ['.$procedure.']', $sql);
        $this->assertSame(1, substr_count($sql, ';'));
        $this->assertStringEndsWith(';', $sql);
        $this->assertStringNotContainsString(',;', $sql);

        preg_match_all('/@([A-Za-z0-9_]+)\s*=\s*:([A-Za-z0-9_]+)/', $sql, $matches, PREG_SET_ORDER);

        $actualNames = array_map(fn (array $match): string => $match[1], $matches);
        sort($actualNames);
        $expectedSorted = $expectedParams;
        sort($expectedSorted);

        $this->assertSame($expectedSorted, $actualNames);
    }

    /**
     * @param  list<string>  $expectedParams
     * @param  array<string, mixed>  $expectedBindings
     */
    private function expectStoredProcedureUpdate(
        string $connectionName,
        string $procedure,
        array $expectedParams,
        array $expectedBindings,
    ): void {
        $connection = Mockery::mock();
        $connection->shouldReceive('update')
            ->once()
            ->with(
                Mockery::on(function (string $sql) use ($procedure, $expectedParams): bool {
                    $this->assertStoredProcedureCall($sql, $procedure, $expectedParams);

                    return true;
                }),
                Mockery::on(function (array $bindings) use ($expectedBindings): bool {
                    foreach ($expectedBindings as $key => $value) {
                        $this->assertArrayHasKey($key, $bindings);
                        $this->assertSame($value, $bindings[$key], "Binding [{$key}] mismatch");
                    }

                    return true;
                })
            )
            ->andReturn(1);

        DB::shouldReceive('connection')->with($connectionName)->andReturn($connection);
    }

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

    public function test_pci_transaction_returns_422_when_id_is_missing_and_does_not_write(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = Mockery::mock();
        $connection->shouldNotReceive('update');
        DB::shouldReceive('connection')->with('pciforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction', [
                'form_id' => 1,
                'status' => 'Settled',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['id']);
    }

    public function test_pci_transaction_writes_all_fields_via_stored_procedure(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'pciforms',
            'usp_InsertTransactionDetails',
            [
                'TranStatus',
                'SettleTime',
                'TransID',
                'Fname',
                'Lname',
                'BillStreet1',
                'BillStreet2',
                'City',
                'State',
                'Zip',
                'PayAmt',
                'FormID',
                'SID',
                'EMPLID',
                'Email',
            ],
            [
                'TransactionID' => 12345,
                'TransactionStatus' => 'Settled',
                'SettlementTime' => '2024-01-15',
                'PaymentAmount' => 99.5,
                'FormID' => 7,
                'FirstName' => 'Pat',
                'LastName' => 'Lee',
                'BillingStreet1' => '1 Main',
                'BillingStreet2' => 'Apt 2',
                'City' => 'Bellevue',
                'State' => 'WA',
                'Zip' => '98004',
                'SID' => '123456789',
                'EMPLID' => '987654321',
                'Email' => 'pat.lee@example.test',
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction', [
                'id' => 12345,
                'status' => 'Settled',
                'settlement_date' => '2024-01-15',
                'amount' => 99.5,
                'form_id' => 7,
                'first_name' => 'Pat',
                'last_name' => 'Lee',
                'billing_address_1' => '1 Main',
                'billing_address_2' => 'Apt 2',
                'billing_city' => 'Bellevue',
                'billing_state' => 'WA',
                'billing_zip' => '98004',
                'sid' => '123456789',
                'emplid' => '987654321',
                'email' => 'pat.lee@example.test',
            ])
            ->assertOk()
            ->assertJsonFragment(['message' => 'Transaction successfully written to database.']);
    }

    public function test_pci_transaction_test_endpoint_does_not_write_to_database(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        DB::shouldReceive('connection')->with('pciforms')->never();

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction/test', [
                'id' => 12345,
                'form_id' => 1,
            ])
            ->assertOk()
            ->assertJsonFragment(['NOTHING ACTUALLY WRITTEN TO DATABASE!'])
            ->assertJsonPath('data.id', 12345);
    }

    public function test_pci_transaction_test_endpoint_returns_422_when_id_is_missing(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction/test', [
                'form_id' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['id']);
    }

    public function test_pci_transaction_test_endpoint_returns_422_when_sid_is_not_nine_digits(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction/test', [
                'id' => 12345,
                'form_id' => 1,
                'sid' => '123',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sid']);
    }

    public function test_pci_transaction_returns_503_with_database_error_message_when_procedure_fails(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = Mockery::mock();
        $connection->shouldReceive('update')->once()->andThrow(new \RuntimeException('procedure failed'));
        DB::shouldReceive('connection')->with('pciforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/pci/transaction', [
                'id' => 12345,
                'form_id' => 1,
            ])
            ->assertStatus(503)
            ->assertJsonPath('message', 'Database Error: procedure failed');
    }

    public function test_graduation_application_writes_all_fields_and_parses_program_code(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'evalforms',
            'usp_InsertIntoGradApps',
            [
                'grSID',
                'grReceived',
                'grQuarter',
                'grProgramCode',
                'grConcentration',
                'grDiplomaName',
                'grReqYear',
                'EntryID',
                'IsUpdate',
            ],
            [
                'grSID' => 123456789,
                'grReceived' => '2024-06-01',
                'grQuarter' => 'B901',
                'grProgramCode' => 'ABC123',
                'grConcentration' => 'Data',
                'grDiplomaName' => 'Pat Lee',
                'grReqYear' => '2023-2024',
                'EntryID' => 'entry-1',
                'IsUpdate' => false,
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'sid' => 123456789,
                'received' => '2024-06-01',
                'quarter' => 'B901',
                'program' => 'Bachelor of Science | ABC123',
                'concentration' => 'Data',
                'diploma_name' => 'Pat Lee',
                'requirements_year' => '2023-2024',
                'entry_id' => 'entry-1',
            ])
            ->assertOk();
    }

    public function test_graduation_application_sends_is_update_true_binding(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'evalforms',
            'usp_InsertIntoGradApps',
            [
                'grSID',
                'grReceived',
                'grQuarter',
                'grProgramCode',
                'grConcentration',
                'grDiplomaName',
                'grReqYear',
                'EntryID',
                'IsUpdate',
            ],
            [
                'grSID' => 123456789,
                'grProgramCode' => 'ABC123',
                'IsUpdate' => true,
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'sid' => 123456789,
                'program_code' => 'ABC123',
                'is_update' => true,
            ])
            ->assertOk();
    }

    public function test_graduation_application_resolves_sid_from_email_when_sid_is_omitted(): void
    {
        WarehouseFixtures::seedStudentTestUser();
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'evalforms',
            'usp_InsertIntoGradApps',
            [
                'grSID',
                'grReceived',
                'grQuarter',
                'grProgramCode',
                'grConcentration',
                'grDiplomaName',
                'grReqYear',
                'EntryID',
                'IsUpdate',
            ],
            [
                'grSID' => '123456789',
                'grProgramCode' => 'ABC123',
                'IsUpdate' => false,
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'email' => 'student.test@example.test',
                'program_code' => 'ABC123',
            ])
            ->assertOk();
    }

    public function test_graduation_application_prefers_explicit_sid_over_email_lookup(): void
    {
        WarehouseFixtures::seedStudentTestUser();
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'evalforms',
            'usp_InsertIntoGradApps',
            [
                'grSID',
                'grReceived',
                'grQuarter',
                'grProgramCode',
                'grConcentration',
                'grDiplomaName',
                'grReqYear',
                'EntryID',
                'IsUpdate',
            ],
            [
                'grSID' => 999888777,
                'grProgramCode' => 'ABC123',
                'IsUpdate' => false,
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'email' => 'student.test@example.test',
                'sid' => 999888777,
                'program_code' => 'ABC123',
            ])
            ->assertOk();
    }

    public function test_graduation_application_returns_422_when_sid_is_not_numeric(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = Mockery::mock();
        $connection->shouldNotReceive('update');
        DB::shouldReceive('connection')->with('evalforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/graduation-application', [
                'sid' => 'abc',
                'program_code' => 'ABC123',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sid']);
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

    public function test_transfer_credit_evaluation_writes_all_fields_via_stored_procedure(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'evalforms',
            'usp_InsertIntoCreditReviews',
            [
                'crSID',
                'crReceived',
                'crProgramCode',
                'crMilitary',
                'crInternationalTranscript',
                'EntryID',
            ],
            [
                'crSID' => 123456789,
                'crReceived' => '2024-05-01',
                'crProgramCode' => 'TRN001',
                'crMilitary' => true,
                'crInternationalTranscript' => false,
                'EntryID' => 'xfer-1',
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/transfer-credit-evaluation', [
                'sid' => 123456789,
                'received' => '2024-05-01',
                'program' => 'Transfer | TRN001',
                'military' => true,
                'international_transcript' => false,
                'entry_id' => 'xfer-1',
            ])
            ->assertOk();
    }

    public function test_transfer_credit_evaluation_resolves_sid_from_email_when_sid_is_omitted(): void
    {
        WarehouseFixtures::seedStudentTestUser();
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'evalforms',
            'usp_InsertIntoCreditReviews',
            [
                'crSID',
                'crReceived',
                'crProgramCode',
                'crMilitary',
                'crInternationalTranscript',
                'EntryID',
            ],
            [
                'crSID' => '123456789',
                'crProgramCode' => 'TRN001',
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/transfer-credit-evaluation', [
                'email' => 'student.test@example.test',
                'program_code' => 'TRN001',
            ])
            ->assertOk();
    }

    public function test_transfer_credit_evaluation_prefers_explicit_sid_over_email_lookup(): void
    {
        WarehouseFixtures::seedStudentTestUser();
        $client = $this->createApiClient(['write_transactions']);

        $this->expectStoredProcedureUpdate(
            'evalforms',
            'usp_InsertIntoCreditReviews',
            [
                'crSID',
                'crReceived',
                'crProgramCode',
                'crMilitary',
                'crInternationalTranscript',
                'EntryID',
            ],
            [
                'crSID' => 999888777,
                'crProgramCode' => 'TRN001',
            ]
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/transfer-credit-evaluation', [
                'email' => 'student.test@example.test',
                'sid' => 999888777,
                'program_code' => 'TRN001',
            ])
            ->assertOk();
    }

    public function test_transfer_credit_evaluation_returns_422_when_sid_is_not_numeric(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $connection = Mockery::mock();
        $connection->shouldNotReceive('update');
        DB::shouldReceive('connection')->with('evalforms')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/forms/evaluations/transfer-credit-evaluation', [
                'sid' => 'abc',
                'program_code' => 'TRN001',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sid']);
    }

    public function test_copilot_user_question_returns_403_without_write_user_questions_permission(): void
    {
        $client = $this->createApiClient(['write_transactions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion', ['question' => 'Hello'])
            ->assertForbidden();
    }

    public function test_copilot_user_question_writes_question_via_stored_procedure(): void
    {
        $client = $this->createApiClient(['write_user_questions']);

        $this->expectStoredProcedureUpdate(
            'copilot',
            'usp_InsertUserQuestions',
            ['Question'],
            ['Question' => 'Hello from Copilot']
        );

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion', ['question' => 'Hello from Copilot'])
            ->assertOk()
            ->assertJsonFragment(['message' => 'Transaction successfully written to database.']);
    }

    public function test_copilot_user_question_returns_503_with_database_error_message_when_procedure_fails(): void
    {
        $client = $this->createApiClient(['write_user_questions']);

        $connection = Mockery::mock();
        $connection->shouldReceive('update')->once()->andThrow(new \RuntimeException('procedure failed'));
        DB::shouldReceive('connection')->with('copilot')->andReturn($connection);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion', ['question' => 'Hello'])
            ->assertStatus(503)
            ->assertJsonPath('message', 'Database Error: procedure failed');
    }

    public function test_copilot_user_question_test_endpoint_does_not_write_to_database(): void
    {
        $client = $this->createApiClient(['write_user_questions']);

        DB::shouldReceive('connection')->with('copilot')->never();

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion/test', ['question' => 'Hello'])
            ->assertOk()
            ->assertJsonFragment(['NOTHING ACTUALLY WRITTEN TO DATABASE!'])
            ->assertJsonPath('data.question', 'Hello');
    }

    public function test_copilot_user_question_test_endpoint_returns_422_when_question_is_too_long(): void
    {
        $client = $this->createApiClient(['write_user_questions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion/test', [
                'question' => str_repeat('a', 4001),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['question']);
    }

    public function test_copilot_user_question_returns_422_when_question_is_too_long(): void
    {
        $client = $this->createApiClient(['write_user_questions']);

        $this->withBasicAuth($client->clientid, self::DEFAULT_CLIENT_KEY)
            ->postJson('/api/v1/copilot/userquestion', [
                'question' => str_repeat('a', 4001),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['question']);
    }
}
