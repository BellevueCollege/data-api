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
        $response = $this->get('/api/v1/catalog/terms/B563')
            ->assertJsonFragment([
                'code' => 'B563', 
                'description' => 'Win 2016'
            ]);
        $this->assertEquals(200, $response->getStatusCode());
          
        $response = $this->get('/api/v1/catalog/terms/B671')
            ->assertJsonFragment([
                'code' => 'B671', 
                'description' => 'Sum 2016'
            ]);
            
       $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testGetYearQuarterBadYQR() {
       //should return empty object so don't need to check json, just need to make sure it doesn't error
       $response = $this->get('/api/v1/catalog/terms/xysdf');
       
       $this->assertEquals(200, $response->getStatusCode()); 
    }
    
    public function testGetCurrentYearQuarter() {
        //test valid YQR
        $response = $this->get('/api/v1/quarters/current');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        //var_dump($response->content());
    }
    
    public function testGetViewableYearQuarters() {
        //test endpoint to get viewable YQRs
        $response = $this->get('/api/v1/catalog/terms');
        
        $this->assertEquals(200, $response->getStatusCode());
        
        //var_dump($response->content());
    }
}