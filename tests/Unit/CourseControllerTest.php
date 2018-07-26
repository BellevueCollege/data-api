<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseControllerTest extends TestCase
{
    /* Comment out test not in use
    public function testIndex() {
        //route not used as it doesn't make sense
        //$this->get('v1/courses')->assertEquals('Hi. Do not try to see that many courses.');
        //$this->assertEquals(200, $this->response->status());
    }
    
    public function testGetCourse() {
        //route not currently used so functionality not complete
        
        //$this->get('v1/courses/ACCT 101');
        
        //$this->assertEquals(200, $this->response->status());
        
    }*/
    
    public function testGetMultipleCourses() {
        //valid course numbers
        $response = $this->get('/api/v1/courses/multiple', [ 'courses[]' => 'ACCT 101', 'courses[]' => 'BTS 293', 'courses[]' => 'ADFIT 020' ]);
        //var_dump($this->response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
        
    }
    
    public function testGetMultipleCoursesBadCourse() {
        //should return empty object so don't need to check json, just need to make sure it doesn't error
        $response = $this->get('/api/v1/courses/multiple', [ 'courses[]' => 'XYZ 101', 'courses[]' => 'BTS 293', 'courses[]' => 'ADFIT 020' ]);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testGetMultipleCoursesNoResults() {
        //should return empty object so don't need to check json, just need to make sure it doesn't error
        $response = $this->get('/api/v1/courses/multiple', [ 'courses[]' => 'XYZ 101', 'courses[]' => 'ABC 123', 'courses[]' => 'RTD 321' ]);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}