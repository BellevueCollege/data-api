<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndex()
    {
        //test subject index
        $response = $this->get('/api/v1/subjects')
            ->assertJsonFragment([
                /*'areas' => [],*/
                'subject' => 'HIST'
            ]);

        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testGetSubject()
    {
        //Try valid subjects
        $response = $this->get('/api/v1/subject/ENGR')
            ->assertJsonFragment([
                'subject' => 'ENGR', 
                'name' => 'Engineering'
            ]);
        
        $this->assertEquals(200, $response->getStatusCode());
            
        $response = $this->get('/api/v1/subject/ACCT&')
            ->assertJsonFragment([
                'subject' => 'ACCT&', 
                'name' => 'Accounting-Transfer'
            ]);
            
        $this->assertEquals(200, $response->getStatusCode());
       
        $response = $this->get('/api/v1/subject/ADFIT')
            ->assertJsonFragment([
                'subject' => 'ADFIT', 
                'name' => 'Adult Fitness'
            ]);
            
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testGetSubjectBadSubject() {
        //try invalid subject, should return empty json
        $response = $this->get('/api/v1/subject/xyzsdf');
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetSubjectsByYearQuarter() 
    {
        //try with valid quarter
        $response = $this->get('/api/v1/subjects/B563');
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testGetSubjectsByYearQuarterBadYQR(){
        //try with invalid quarter, should return empty json
        $response = $this->get('/api/v1/subjects/xyzsdf');
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}