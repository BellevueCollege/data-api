<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeControllerTest extends TestCase
{
    
    /** End point requires authentication so should fail */
    public function testGetEmployeeByUsernameNotAuthenticated() {
        //valid employee username
        $response = $this->get('/api/v1/employee/nicole.swan');
        //var_dump($response);
        $this->assertEquals(401, $response->status());
        
    }
    
    public function testGetEmployeeByUsernameNotEmployee() {
        
        //authenticate
        $headers = [];
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers['Authorization'] = 'Bearer ' . $token;

        $response = $this->get('/api/v1/employee/student.test', [], $headers);
        
        // should be empty json returned
        $this->assertEquals(200, $response->getStatusCode());
        
    }

    public function testGetEmployeeByUsername() {

        //authenticate
        $headers = [];
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers['Authorization'] = 'Bearer ' . $token;
    
        //valid employee username
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/employee/nicole.swan');
        
        //var_dump($response);
        $this->assertEquals(200, $response->status());
        
    }

}