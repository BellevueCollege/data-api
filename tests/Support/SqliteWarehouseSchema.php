<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recreates production SQL Server view names as SQLite tables so Eloquent can query them without a warehouse.
 *
 * Each column carries an expected MSSQL type (including length). schema:compare validates those
 * against SQL Server; rebuild() derives SQLite Blueprint lengths from the same annotations.
 */
class SqliteWarehouseSchema
{
    public static function rebuild(): void
    {
        self::dropAllTables();

        foreach (self::columnsByConnection() as $connection => $tables) {
            foreach ($tables as $table => $columns) {
                Schema::connection($connection)->create($table, function (Blueprint $blueprint) use ($columns) {
                    foreach ($columns as $column) {
                        $column->applyTo($blueprint);
                    }
                });
            }
        }
    }

    /**
     * Warehouse objects recreated in SQLite for tests, keyed by connection name.
     *
     * @return array<string, list<string>>
     */
    public static function warehouseObjectsByConnection(): array
    {
        $objects = [];

        foreach (self::columnsByConnection() as $connection => $tables) {
            $objects[$connection] = array_keys($tables);
        }

        return $objects;
    }

    /**
     * Expected warehouse columns for schema:compare (excludes sqlite-only surrogates).
     *
     * @return array<string, array<string, list<WarehouseColumn>>>
     */
    public static function expectedWarehouseColumns(): array
    {
        $expected = [];

        foreach (self::columnsByConnection() as $connection => $tables) {
            foreach ($tables as $table => $columns) {
                $warehouseColumns = array_values(array_filter(
                    $columns,
                    fn (WarehouseColumn $column): bool => ! $column->sqliteOnly
                ));

                if ($warehouseColumns !== []) {
                    $expected[$connection][$table] = $warehouseColumns;
                }
            }
        }

        return $expected;
    }

    public static function dropAllTables(): void
    {
        foreach (self::warehouseObjectsByConnection() as $connection => $connectionTables) {
            Schema::connection($connection)->disableForeignKeyConstraints();

            foreach ($connectionTables as $table) {
                Schema::connection($connection)->dropIfExists($table);
            }

            Schema::connection($connection)->enableForeignKeyConstraints();
        }
    }

    /**
     * @return array<string, array<string, list<WarehouseColumn>>>
     */
    public static function columnsByConnection(): array
    {
        return [
            'ods' => [
                'vw_YearQuarter' => [
                    new WarehouseColumn('YearQuarterID', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('STRM', sqlServer: 'varchar(4)'),
                    new WarehouseColumn('Title', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('FirstClassDay', sqlServer: 'date'),
                    new WarehouseColumn('LastClassDay', sqlServer: 'date'),
                ],
                'vw_WebRegistrationSetting' => [
                    new WarehouseColumn('STRM', sqlServer: 'varchar(4)', nullable: false, primary: true),
                    new WarehouseColumn('FirstRegistrationDate', sqlServer: 'date'),
                    new WarehouseColumn('LastRegistrationDate', sqlServer: 'date'),
                ],
                'vw_PS_CS_SubjectTable' => [
                    new WarehouseColumn('SUBJECT', sqlServer: 'varchar(8)', nullable: false),
                    new WarehouseColumn('DESCR', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('DESCRFORMAL', sqlServer: 'varchar(50)'),
                    new WarehouseColumn('DESCRSHORT', sqlServer: 'varchar(10)'),
                    new WarehouseColumn('EFFDT', sqlServer: 'date'),
                    new WarehouseColumn('EFF_STATUS', sqlServer: 'varchar(1)'),
                ],
                'vw_Course' => [
                    new WarehouseColumn('PSCourseID', sqlServer: 'varchar(6)', nullable: false, primary: true),
                    new WarehouseColumn('CourseID', sqlServer: 'varchar(11)'),
                    new WarehouseColumn('CourseSubject', sqlServer: 'varchar(8)'),
                    new WarehouseColumn('CatalogNumber', sqlServer: 'varchar(10)'),
                    new WarehouseColumn('CourseTitle', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('CourseTitle2', sqlServer: 'varchar(100)'),
                    new WarehouseColumn('EffectiveYearQuarterEnd', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('Credits', sqlServer: 'numeric(5,2)'),
                    new WarehouseColumn('VariableCredits', sqlServer: 'int'),
                    new WarehouseColumn('note', sqlServer: 'varchar(255)'),
                ],
                'vw_CourseDescription' => [
                    new WarehouseColumn('id', sqliteOnly: true),
                    new WarehouseColumn('CourseID', sqlServer: 'varchar(8)'),
                    new WarehouseColumn('Description', sqlServer: 'varchar(4000)'),
                    new WarehouseColumn('EffectiveYearQuarterBegin', sqlServer: 'varchar(30)'),
                ],
                'vw_Class' => [
                    new WarehouseColumn('PSClassID', sqlServer: 'varchar(10)'),
                    new WarehouseColumn('ClassID', sqlServer: 'varchar(50)'),
                    new WarehouseColumn('YearQuarterID', sqlServer: 'char(4)'),
                    new WarehouseColumn('STRM', sqlServer: 'char(4)'),
                    new WarehouseColumn('Department', sqlServer: 'varchar(8)'),
                    new WarehouseColumn('CourseNumber', sqlServer: 'varchar(10)'),
                    new WarehouseColumn('CourseID', sqlServer: 'varchar(11)'),
                    new WarehouseColumn('CourseTitle', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('Section', sqlServer: 'varchar(4)'),
                    new WarehouseColumn('ItemNumber', sqlServer: 'varchar(5)'),
                    new WarehouseColumn('ClassNumber', sqlServer: 'varchar(5)'),
                    new WarehouseColumn('InstructorName', sqlServer: 'varchar(12)'),
                    new WarehouseColumn('StartDate', sqlServer: 'datetime'),
                    new WarehouseColumn('EndDate', sqlServer: 'datetime'),
                    new WarehouseColumn('DayID', sqlServer: 'varchar(4)'),
                    new WarehouseColumn('Room', sqlServer: 'varchar(10)'),
                    new WarehouseColumn('RoomDescr', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('StartTime', sqlServer: 'datetime'),
                    new WarehouseColumn('EndTime', sqlServer: 'datetime'),
                ],
                'vw_ClassSchedule' => [
                    new WarehouseColumn('id', sqliteOnly: true),
                    new WarehouseColumn('PSClassID', sqlServer: 'varchar(10)'),
                    new WarehouseColumn('DayID', sqlServer: 'varchar(4)'),
                    new WarehouseColumn('Room', sqlServer: 'varchar(10)'),
                    new WarehouseColumn('RoomDescr', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('InstructorName', sqlServer: 'varchar(50)'),
                    new WarehouseColumn('StartDate', sqlServer: 'datetime'),
                    new WarehouseColumn('EndDate', sqlServer: 'datetime'),
                    new WarehouseColumn('StartTime', sqlServer: 'datetime'),
                    new WarehouseColumn('EndTime', sqlServer: 'datetime'),
                ],
                'vw_Day' => [
                    new WarehouseColumn('DayID', sqlServer: 'varchar(4)', nullable: false, primary: true),
                    new WarehouseColumn('Title', sqlServer: 'varchar(10)'),
                ],
                'vw_Student_Unfiltered' => [
                    new WarehouseColumn('EMPLID', sqlServer: 'varchar(11)', nullable: false, primary: true),
                    new WarehouseColumn('SID', sqlServer: 'char(9)'),
                    new WarehouseColumn('AzureID', sqlServer: 'varchar(36)'),
                    new WarehouseColumn('FirstName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('LastName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('Email', sqlServer: 'varchar(70)'),
                    new WarehouseColumn('DaytimePhone', sqlServer: 'varchar(24)'),
                    new WarehouseColumn('EveningPhone', sqlServer: 'varchar(24)'),
                    new WarehouseColumn('NTUserName', sqlServer: 'varchar(50)'),
                    new WarehouseColumn('UserPrincipalName', sqlServer: 'varchar(767)'),
                    new WarehouseColumn('PrivateRecord', sqlServer: 'bit'),
                ],
                'vw_Employee' => [
                    new WarehouseColumn('EMPLID', sqlServer: 'varchar(11)', nullable: false, primary: true),
                    new WarehouseColumn('SID', sqlServer: 'char(9)'),
                    new WarehouseColumn('AzureID', sqlServer: 'varchar(36)'),
                    new WarehouseColumn('FirstName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('LastName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('AliasName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('WorkEmail', sqlServer: 'varchar(200)'),
                    new WarehouseColumn('WorkPhoneNumber', sqlServer: 'varchar(24)'),
                    new WarehouseColumn('ADUserName', sqlServer: 'varchar(100)'),
                    new WarehouseColumn('UserPrincipalName', sqlServer: 'varchar(767)'),
                    new WarehouseColumn('EmployeeStatusCode', sqlServer: 'char(1)'),
                ],
            ],
            'empdirectory' => [
                'Employees' => [
                    new WarehouseColumn('EMPLID', sqlServer: 'varchar(11)', nullable: false, primary: true),
                    new WarehouseColumn('ADAccountName', sqlServer: 'varchar(100)'),
                    new WarehouseColumn('FirstName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('LastName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('AKA', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('DisplayName', sqlServer: 'varchar(61)'),
                    new WarehouseColumn('WorkingTitle', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('OfficialTitle', sqlServer: 'varchar(8000)'),
                    new WarehouseColumn('DepartmentName', sqlServer: 'varchar(30)'),
                    new WarehouseColumn('BCCEmail', sqlServer: 'varchar(200)'),
                    new WarehouseColumn('WorkPhone', sqlServer: 'varchar(24)'),
                    new WarehouseColumn('DisplayPhone', sqlServer: 'varchar(24)'),
                    new WarehouseColumn('WorkOffice', sqlServer: 'varchar(50)'),
                    new WarehouseColumn('MailStop', sqlServer: 'varchar(50)'),
                ],
            ],
            'copilot' => [
                'vw_LinkFound' => [
                    new WarehouseColumn('id', sqliteOnly: true),
                    new WarehouseColumn('SourceArea', sqlServer: 'varchar(4000)'),
                    new WarehouseColumn('LinkText', sqlServer: 'varchar(4000)'),
                    new WarehouseColumn('LinkDescr', sqlServer: 'varchar(4000)'),
                ],
            ],
        ];
    }
}
