<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;

/**
 * Seeds minimal ODS, directory, and copilot rows for API and model tests.
 *
 * Quarter IDs, STRM values, and dates must stay consistent with ApiFeatureTestCase::travelTo().
 */
class WarehouseFixtures
{
    public const CURRENT_YEAR_QUARTER_ID = 'C124';

    public const CURRENT_STRM = '2241';

    public static function seedCurrentYearQuarter(): void
    {
        DB::connection('ods')->table('vw_YearQuarter')->insert([
            'YearQuarterID' => self::CURRENT_YEAR_QUARTER_ID,
            'STRM' => self::CURRENT_STRM,
            'Title' => 'SPRING 2024',
            'FirstClassDay' => '2024-01-08 00:00:00',
            'LastClassDay' => '2024-12-15 00:00:00.000',
        ]);
    }

    public static function seedViewableYearQuarterWithRegistration(): void
    {
        self::seedCurrentYearQuarter();

        DB::connection('ods')->table('vw_WebRegistrationSetting')->insert([
            'STRM' => self::CURRENT_STRM,
            'FirstRegistrationDate' => '2024-01-01 00:00:00',
            'LastRegistrationDate' => '2024-12-01 00:00:00',
        ]);
    }

    public static function seedSubjectAcct(): void
    {
        DB::connection('ods')->table('vw_PS_CS_SubjectTable')->insert([
            'SUBJECT' => 'ACCT',
            'DESCR' => 'Accounting',
            'DESCRFORMAL' => 'Accounting Department',
            'DESCRSHORT' => 'ACCT',
            'EFFDT' => '2020-01-01',
            'EFF_STATUS' => 'A',
        ]);
    }

    public static function seedSubjectEngr(): void
    {
        DB::connection('ods')->table('vw_PS_CS_SubjectTable')->insert([
            'SUBJECT' => 'ENGR',
            'DESCR' => 'Engineering',
            'DESCRFORMAL' => 'Engineering',
            'DESCRSHORT' => 'ENGR',
            'EFFDT' => '2020-01-01',
            'EFF_STATUS' => 'A',
        ]);
    }

    public static function seedCourseAcct101(): void
    {
        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-ACCT-101',
            'CourseID' => 'ACCT 101',
            'CourseSubject' => 'ACCT',
            'CatalogNumber' => '101',
            'CourseTitle' => 'Intro Accounting',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 5,
            'VariableCredits' => 0,
            'note' => null,
        ]);

        DB::connection('ods')->table('vw_CourseDescription')->insert([
            'CourseID' => 'ACCT 101',
            'Description' => 'Introduction to accounting.',
            'EffectiveYearQuarterBegin' => 'B500',
        ]);
    }

    public static function seedTransferInCourse(): void
    {
        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-ACCT-TR',
            'CourseID' => 'ACCT 199',
            'CourseSubject' => 'ACCT',
            'CatalogNumber' => '199',
            'CourseTitle' => 'Transferred-In Course',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 1,
            'VariableCredits' => 0,
            'note' => null,
        ]);
    }

    public static function seedClassOfferingAbe53(): void
    {
        self::seedCurrentYearQuarter();

        DB::connection('ods')->table('vw_Course')->insert([
            'PSCourseID' => 'PS-ABE-53',
            'CourseID' => 'ABE 53',
            'CourseSubject' => 'ABE',
            'CatalogNumber' => '53',
            'CourseTitle' => 'ABE Literacy',
            'CourseTitle2' => '',
            'EffectiveYearQuarterEnd' => null,
            'Credits' => 3,
            'VariableCredits' => 0,
            'note' => null,
        ]);

        DB::connection('ods')->table('vw_Class')->insert([
            'PSClassID' => 'PS-CLASS-ABE-53',
            'ClassID' => '1001',
            'YearQuarterID' => self::CURRENT_YEAR_QUARTER_ID,
            'STRM' => self::CURRENT_STRM,
            'Department' => 'ABE',
            'CourseNumber' => '53',
            'CourseID' => 'ABE 53',
            'CourseTitle' => 'ABE Literacy',
            'Section' => '01',
            'ItemNumber' => '1',
            'ClassNumber' => '1001',
            'InstructorName' => 'Test Instructor',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'DayID' => 'MW',
            'Room' => '  R101  ',
            'RoomDescr' => 'Room 101',
            'StartTime' => '2024-03-01 09:00:00',
            'EndTime' => '2024-03-01 10:30:00',
        ]);
    }

    public static function seedClassSchedule(): void
    {
        DB::connection('ods')->table('vw_ClassSchedule')->insert([
            'PSClassID' => 'PS-SCHED-1',
            'DayID' => 'MW',
            'Room' => ' R202 ',
            'RoomDescr' => 'Room 202',
            'InstructorName' => 'Schedule Instructor',
            'StartDate' => '2024-03-01 00:00:00',
            'EndDate' => '2024-06-01 00:00:00',
            'StartTime' => '2024-03-01 13:00:00',
            'EndTime' => '2024-03-01 14:30:00',
        ]);
    }

    public static function seedStudentTestUser(): void
    {
        DB::connection('ods')->table('vw_Student_Unfiltered')->insert([
            'EMPLID' => '100000001',
            'SID' => '123456789',
            'AzureID' => 'azure-student-1',
            'FirstName' => 'Test',
            'LastName' => 'Student',
            'Email' => 'student.test@example.test',
            'DaytimePhone' => '555-0100',
            'EveningPhone' => null,
            'NTUserName' => 'student.test',
            'UserPrincipalName' => 'student.test@example.test',
            'PrivateRecord' => 0,
        ]);
    }

    public static function seedEmployeeTestUser(): void
    {
        DB::connection('ods')->table('vw_Employee')->insert([
            'EMPLID' => '200000001',
            'SID' => '987654321',
            'AzureID' => 'azure-employee-1',
            'FirstName' => 'Test',
            'LastName' => 'Employee',
            'AliasName' => null,
            'WorkEmail' => 'employee.test@example.test',
            'WorkPhoneNumber' => '555-0200',
            'ADUserName' => 'employee.test',
            'UserPrincipalName' => 'employee.test@example.test',
            'EmployeeStatusCode' => 'A',
        ]);
    }

    public static function seedInactiveEmployee(): void
    {
        DB::connection('ods')->table('vw_Employee')->insert([
            'EMPLID' => '200000002',
            'SID' => '987654322',
            'AzureID' => null,
            'FirstName' => 'Inactive',
            'LastName' => 'Employee',
            'AliasName' => null,
            'WorkEmail' => 'inactive.test@example.test',
            'WorkPhoneNumber' => null,
            'ADUserName' => 'inactive.test',
            'UserPrincipalName' => 'inactive.test@example.test',
            'EmployeeStatusCode' => 'I',
        ]);
    }

    public static function seedDirectoryEmployee(): void
    {
        DB::connection('empdirectory')->table('Employees')->insert([
            'EMPLID' => '300000001',
            'ADAccountName' => 'directory.test',
            'FirstName' => 'Directory',
            'LastName' => 'User',
            'AKA' => null,
            'DisplayName' => 'Directory User',
            'WorkingTitle' => 'Analyst',
            'OfficialTitle' => 'Analyst',
            'DepartmentName' => 'IT',
            'BCCEmail' => 'directory.test@example.test',
            'WorkPhone' => '555-0300',
            'DisplayPhone' => '555-0300',
            'WorkOffice' => 'A1',
            'MailStop' => 'MS-1',
        ]);
    }

    public static function seedLinks(): void
    {
        DB::connection('copilot')->table('vw_LinkFound')->insert([
            'SourceArea' => 'Financial Aid',
            'LinkText' => 'Apply for aid',
            'LinkDescr' => 'Aid application',
        ]);
    }

    public static function seedActiveCreditCatalog(): void
    {
        self::seedCurrentYearQuarter();
        self::seedSubjectAcct();
        self::seedCourseAcct101();
    }

    public static function seedStandardCatalog(): void
    {
        self::seedActiveCreditCatalog();
        self::seedSubjectEngr();
        self::seedClassOfferingAbe53();
        self::seedClassSchedule();
        self::seedStudentTestUser();
        self::seedEmployeeTestUser();
        self::seedDirectoryEmployee();
        self::seedLinks();
        self::seedViewableYearQuarterWithRegistration();
    }
}
