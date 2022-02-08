<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseYearQuarterControllerTest extends TestCase
{
    /* Currently no route for this function
    public function testIndex()
    {

    }*/

    public function testGetCourseYearQuarter()
    {
       //catalog/{yqrid}/{subjectid}/{coursenum}

       //try valid offering
       $response = $this->get('/api/v1/classes/C123/ABE/53')
            ->assertJsonFragment([
                'subject' => 'ABE',
                'courseNumber' => '53'
            ]);
       $this->assertEquals(200, $response->getStatusCode());

       $response = $this->get('/api/v1/classes/C124/ENGL&/101')
            ->assertJsonFragment([
                'subject' => 'ENGL&',
                'courseNumber' => '101'
            ]);
       $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCourseSTRM()
    {
       //catalog/{yqrid}/{subjectid}/{coursenum}

       //try valid offering
       $response = $this->get('/api/v1/classes/2221/ABE/53?format=strm')
            ->assertJsonFragment([
                'subject' => 'ABE',
                'courseNumber' => '53'
            ]);
       $this->assertEquals(200, $response->getStatusCode());

       $response = $this->get('/api/v1/classes/2223/ENGL&/101?format=strm')
            ->assertJsonFragment([
                'subject' => 'ENGL&',
                'courseNumber' => '101'
            ]);
       $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCourseYearQuarterBadYQR(){
       //try invalid term
       //should return empty object so don't need to check json, just need to make sure it doesn't error
       $response = $this->get('/api/v1/classes/xysdf/ADFIT/020');
       $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCourseYearQuarterBadSubject() {
       //try invalid subject
       //should return empty object so don't need to check json, just need to make sure it doesn't error
       $response = $this->get('/api/v1/classes/B561/xysdf/020');
       $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCourseYearQuarterBadCourseNumber() {
       //try invalid coursenum
       //should return empty object so don't need to check json, just need to make sure it doesn't error
       $response = $this->get('/api/v1/classes/B561/ADFIT/xysdf');
       $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCourseYearQuartersBySubject() {
        //catalog/{yqrid}/{subjectid}

        //try valid term and subject
        $response = $this->get('/api/v1/classes/C124/ABE')
            ->assertJsonFragment([
                'subject' => 'ABE',
            ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCourseYearQuartersBySubjectBadTerm() {
       //try invalid term
       //should return empty object so don't need to check json, just need to make sure it doesn't error
       $response = $this->get('/api/v1/classes/xysdf/ADFIT');
       $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetCourseYearQuartersBySubjectBadSubject() {
       //try invalid subject
       //should return empty object so don't need to check json, just need to make sure it doesn't error
       $response = $this->get('/api/v1/classes/B561/xysdf');
       $this->assertEquals(200, $response->getStatusCode());
        //var_dump($response->content());
    }

}
