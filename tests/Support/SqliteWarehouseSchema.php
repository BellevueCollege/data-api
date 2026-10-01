<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recreates production SQL Server view names as SQLite tables so Eloquent can query them without a warehouse.
 */
class SqliteWarehouseSchema
{
    public static function rebuild(): void
    {
        self::dropAllTables();
        self::createOdsTables();
        self::createDirectoryTables();
        self::createCopilotTables();
    }

    public static function dropAllTables(): void
    {
        $tables = [
            'ods' => [
                'vw_Course',
                'vw_CourseDescription',
                'vw_YearQuarter',
                'vw_PS_CS_SubjectTable',
                'vw_Class',
                'vw_ClassSchedule',
                'vw_Day',
                'vw_WebRegistrationSetting',
                'vw_Student_Unfiltered',
                'vw_Employee',
            ],
            'empdirectory' => ['Employees'],
            'copilot' => ['vw_LinkFound'],
        ];

        foreach ($tables as $connection => $connectionTables) {
            Schema::connection($connection)->disableForeignKeyConstraints();

            foreach ($connectionTables as $table) {
                Schema::connection($connection)->dropIfExists($table);
            }

            Schema::connection($connection)->enableForeignKeyConstraints();
        }
    }

    private static function createOdsTables(): void
    {
        Schema::connection('ods')->create('vw_YearQuarter', function (Blueprint $table) {
            $table->string('YearQuarterID')->nullable();
            $table->string('STRM')->nullable();
            $table->string('Title')->nullable();
            $table->dateTime('FirstClassDay')->nullable();
            $table->dateTime('LastClassDay')->nullable();
        });

        Schema::connection('ods')->create('vw_WebRegistrationSetting', function (Blueprint $table) {
            $table->string('STRM')->primary();
            $table->dateTime('FirstRegistrationDate')->nullable();
            $table->dateTime('LastRegistrationDate')->nullable();
        });

        Schema::connection('ods')->create('vw_PS_CS_SubjectTable', function (Blueprint $table) {
            $table->string('SUBJECT');
            $table->string('DESCR')->nullable();
            $table->string('DESCRFORMAL')->nullable();
            $table->string('DESCRSHORT')->nullable();
            $table->date('EFFDT')->nullable();
            $table->string('EFF_STATUS')->nullable();
        });

        Schema::connection('ods')->create('vw_Course', function (Blueprint $table) {
            $table->string('PSCourseID')->primary();
            $table->string('CourseID')->nullable();
            $table->string('CourseSubject')->nullable();
            $table->string('CatalogNumber')->nullable();
            $table->string('CourseTitle')->nullable();
            $table->string('CourseTitle2')->nullable();
            $table->string('EffectiveYearQuarterEnd')->nullable();
            $table->decimal('Credits', 8, 2)->nullable();
            $table->boolean('VariableCredits')->nullable();
            $table->string('note')->nullable();
        });

        Schema::connection('ods')->create('vw_CourseDescription', function (Blueprint $table) {
            $table->increments('id');
            $table->string('CourseID')->nullable();
            $table->text('Description')->nullable();
            $table->string('EffectiveYearQuarterBegin')->nullable();
        });

        Schema::connection('ods')->create('vw_Class', function (Blueprint $table) {
            $table->string('PSClassID')->nullable();
            $table->string('ClassID')->nullable();
            $table->string('YearQuarterID')->nullable();
            $table->string('STRM')->nullable();
            $table->string('Department')->nullable();
            $table->string('CourseNumber')->nullable();
            $table->string('CourseID')->nullable();
            $table->string('CourseTitle')->nullable();
            $table->string('Section')->nullable();
            $table->string('ItemNumber')->nullable();
            $table->string('ClassNumber')->nullable();
            $table->string('InstructorName')->nullable();
            $table->dateTime('StartDate')->nullable();
            $table->dateTime('EndDate')->nullable();
            $table->string('DayID')->nullable();
            $table->string('Room')->nullable();
            $table->string('RoomDescr')->nullable();
            $table->dateTime('StartTime')->nullable();
            $table->dateTime('EndTime')->nullable();
        });

        Schema::connection('ods')->create('vw_ClassSchedule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('PSClassID')->nullable();
            $table->string('DayID')->nullable();
            $table->string('Room')->nullable();
            $table->string('RoomDescr')->nullable();
            $table->string('InstructorName')->nullable();
            $table->dateTime('StartDate')->nullable();
            $table->dateTime('EndDate')->nullable();
            $table->dateTime('StartTime')->nullable();
            $table->dateTime('EndTime')->nullable();
        });

        Schema::connection('ods')->create('vw_Day', function (Blueprint $table) {
            $table->string('DayID')->primary();
            $table->string('Title')->nullable();
        });

        Schema::connection('ods')->create('vw_Student_Unfiltered', function (Blueprint $table) {
            $table->string('EMPLID')->primary();
            $table->string('SID')->nullable();
            $table->string('AzureID')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('Email')->nullable();
            $table->string('DaytimePhone')->nullable();
            $table->string('EveningPhone')->nullable();
            $table->string('NTUserName')->nullable();
            $table->string('UserPrincipalName')->nullable();
            $table->boolean('PrivateRecord')->nullable();
        });

        Schema::connection('ods')->create('vw_Employee', function (Blueprint $table) {
            $table->string('EMPLID')->primary();
            $table->string('SID')->nullable();
            $table->string('AzureID')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('AliasName')->nullable();
            $table->string('WorkEmail')->nullable();
            $table->string('WorkPhoneNumber')->nullable();
            $table->string('ADUserName')->nullable();
            $table->string('UserPrincipalName')->nullable();
            $table->string('EmployeeStatusCode')->nullable();
        });
    }

    private static function createDirectoryTables(): void
    {
        Schema::connection('empdirectory')->create('Employees', function (Blueprint $table) {
            $table->string('EMPLID')->primary();
            $table->string('ADAccountName')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('AKA')->nullable();
            $table->string('DisplayName')->nullable();
            $table->string('WorkingTitle')->nullable();
            $table->string('OfficialTitle')->nullable();
            $table->string('DepartmentName')->nullable();
            $table->string('BCCEmail')->nullable();
            $table->string('WorkPhone')->nullable();
            $table->string('DisplayPhone')->nullable();
            $table->string('WorkOffice')->nullable();
            $table->string('MailStop')->nullable();
        });
    }

    private static function createCopilotTables(): void
    {
        Schema::connection('copilot')->create('vw_LinkFound', function (Blueprint $table) {
            $table->increments('id');
            $table->string('SourceArea')->nullable();
            $table->string('LinkText')->nullable();
            $table->string('LinkDescr')->nullable();
        });
    }
}
