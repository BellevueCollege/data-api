---
paths:
  - 'tests/**'
---

# Tests

## Base classes

- Extend `Tests\TestCase` for tests that do not need the database or warehouse tables.
- Extend `Tests\DatabaseTestCase` when the test needs Laravel migrations plus warehouse-shaped tables on SQLite.
- Extend `Tests\ApiFeatureTestCase` for authenticated HTTP tests against `/api/...` that use the database.

## Database safety

Tests must never write to production or development warehouse databases.

- `phpunit.xml` must keep `force="true"` on `APP_ENV`, `DB_CONNECTION`, and every `*_DB_DRIVER` / `*_DB_DATABASE` entry so exported shell env vars cannot override SQLite test settings.
- `Tests\TestCase` must keep the pre-`parent::setUp()` SQLite driver check and connection purge for all application connections (`default`, `da`, `ods`, `pciforms`, `evalforms`, `empdirectory`, `copilot`). Do not remove or bypass this guard.
- Warehouse view data in tests comes from `Tests\Support\SqliteWarehouseSchema` and `Tests\Support\WarehouseFixtures`, not from live SQL Server connections.

## API integration tests

Use PHPUnit HTTP tests against `/api/...` with `assertJsonFragment`. Do not assume factories or Mockery unless adding new isolated unit tests.
