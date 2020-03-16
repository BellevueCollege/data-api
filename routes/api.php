<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*** Protected api endpoints ***/
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/** 
 * Protected endpoints accessible only internally
 * 
 * Protected by JSON Web Token Auth
 **/
Route::group(['domain' => config('dataapi.api_internal_domain'), 'middleware' => 'auth:api', 'prefix' => 'v1'], function ($router) {

    Route::get('internal/employee/{username}','EmployeeController@getEmployeeByUsername');
    Route::get('internal/student/{username}','StudentController@getStudentByUsername');

});

/** Unprotected endpoints accessible only internally **/
Route::group(['domain' => config('dataapi.api_internal_domain'), 'prefix' => 'v1'], function ($router) {

    Route::post('internal/auth/login', [
        'as' => 'login', 'uses' => 'AuthController@login'
    ]);

});

/**
 * Form Data Endpoints
 * 
 * Protected by Basic Auth
**/
Route::prefix('v1')->middleware('auth.basic:api-basic,clientid')->group(function () {
    Route::prefix('forms/pci')->group(function () {

        Route::get('transactions','TransactionController@getTransactions');

        Route::post('transactions','TransactionController@postTransaction');

    });

    Route::prefix('forms/evaluations')->group(function () {

        // Route::get('transactions','TransactionController@getTransactions');

        Route::post('graduation-application','Forms\GraduationApplicationController@post');

    });
});

/*** Unprotected api endpoints ***/
Route::prefix('v1')->group(function () {

    Route::get('subject/{slug}','SubjectController@getSubject');
    Route::get('subjects','SubjectController@index');
    Route::get('subjects/{yqrid}', 'SubjectController@getSubjectsByYearQuarter');

    Route::get('course/{courseid}', 'CourseController@getCourse');
    Route::get('course/{subject}/{coursenum}', 'CourseController@getCourseBySubjectAndNumber');
    Route::get('courses/multiple', 'CourseController@getMultipleCourses');
    Route::get('courses/{subject}', 'CourseController@getCoursesBySubject');

    Route::get('quarters', 'YearQuarterController@getViewableYearQuarters');
    Route::get('quarter/current', 'YearQuarterController@getCurrentYearQuarter');
    Route::get('quarter/{yqrid}', 'YearQuarterController@getYearQuarter');

    Route::get('classes/{yqrid}/{subjectid}', 'CourseYearQuarterController@getCourseYearQuartersBySubject');
    Route::get('classes/{yqrid}/{subjectid}/{coursenum}', 'CourseYearQuarterController@getCourseYearQuarter');

    //API endpoints specific to ModoLabs requirements
    /*Route::get('catalog/terms', 'YearQuarterController@getViewableYearQuarters');
    Route::get('catalog/terms/{yqrid}', 'YearQuarterController@getYearQuarter');
    Route::get('catalog/catalogAreas/{yqrid}', 'SubjectController@getSubjectsByYearQuarter');
    Route::get('catalog/{yqrid}/{subjectid}', 'CourseYearQuarterController@getCourseYearQuartersBySubject');
    Route::get('catalog/{yqrid}/{subjectid}/{coursenum}', 'CourseYearQuarterController@getCourseYearQuarter');*/
});