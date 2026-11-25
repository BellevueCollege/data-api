<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PDOException;
use Tests\TestCase;

/**
 * Production-shaped exception JSON when APP_DEBUG is falsy via getenv.
 */
class ExceptionHandlerApiTest extends TestCase
{
    private const PDO_PROBE_PATH = '/__testing/pdo-exception';

    private string|false $previousAppDebug = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousAppDebug = getenv('APP_DEBUG');

        Route::get(self::PDO_PROBE_PATH, function (): never {
            throw new PDOException('simulated connection failure');
        });
    }

    protected function tearDown(): void
    {
        if ($this->previousAppDebug === false) {
            putenv('APP_DEBUG');
        } else {
            putenv('APP_DEBUG='.$this->previousAppDebug);
        }

        parent::tearDown();
    }

    public function test_pdo_exception_returns_database_unavailable_json_when_app_debug_is_falsy(): void
    {
        putenv('APP_DEBUG=');

        $this->getJson(self::PDO_PROBE_PATH)
            ->assertStatus(500)
            ->assertExactJson([
                'error' => 500,
                'message' => 'Database unavailable.',
            ]);
    }
}
