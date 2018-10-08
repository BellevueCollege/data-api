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

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], function ($router) {

    //Route::post('auth/login', 'AuthController@loginPost');
    Route::get('employee/{username}','EmployeeController@getEmployeeByUsername');
    Route::get('student/{username}','StudentController@getStudentByUsername');

});

/*** Unprotected api endpoints ***/
Route::prefix('v1')->group(function () {

    Route::post('auth/login', [
        'as' => 'login', 'uses' => 'AuthController@login'
    ]);

    Route::get('subject','SubjectController@index');
  
    Route::get('subject/{slug}','SubjectController@getSubject');
      
    Route::get('course/{courseid}', 'CourseController@getCourse');
    Route::get('courses/multiple', 'CourseController@getMultipleCourses');
    //$router->get('courses/{courseid}','CourseController@getCourse');
    
    Route::get('quarters/current', 'YearQuarterController@getCurrentYearQuarter');
    
    //API endpoints specific to ModoLabs requirements
    Route::get('catalog/terms', 'YearQuarterController@getViewableYearQuarters');
    Route::get('catalog/terms/{yqrid}', 'YearQuarterController@getYearQuarter');
    Route::get('catalog/catalogAreas/{yqrid}', 'SubjectController@getSubjectsByYearQuarter');
    Route::get('catalog/{yqrid}/{subjectid}', 'CourseYearQuarterController@getCourseYearQuartersBySubject');
    Route::get('catalog/{yqrid}/{subjectid}/{coursenum}', 'CourseYearQuarterController@getCourseYearQuarter');
    
    //Route::get('employee/{username}','EmployeeController@getEmployeeByUsername');

    //Route::get('student/{username}','StudentController@getStudentByUsername');
});