<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class YearQuarterControllerTest extends TestCase
{
    /* Currently no route for this function
    public function testIndex()
    {

    }*/
    
    public function testGetYearQuarter()
    {
        //try valid terms
        $response = $this->get('/api/v1/quarter/B563')
            ->assertJsonFragment([
                'quarter' => 'B563', 
                'title' => 'Win 2016'
            ]);
        $this->assertEquals(200, $response->getStatusCode());
          
        $response = $this->get('/api/v1/quarter/B671')
            ->assertJsonFragment([
                'quarter' => 'B671', 
                'title' => 'Sum 2016'
            ]);
            
       $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testGetYearQuarterBadYQR() {
       //should return empty object so don't need to check json, just need to make sure it doesn't error
       $response = $this->get('/api/v1/quarter/xysdf');
       
       $this->assertEquals(200, $response->getStatusCode()); 
    }
    
    public function testGetCurrentYearQuarter() {
        //test valid YQR
        $response = $this->get('/api/v1/quarter/current');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        //var_dump($response->content());
    }
    
    public function testGetViewableYearQuarters() {
        //test endpoint to get viewable YQRs
        $response = $this->get('/api/v1/quarters');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        //var_dump($response->content());
    }
}