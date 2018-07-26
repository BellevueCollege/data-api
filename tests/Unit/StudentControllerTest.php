<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentControllerTest extends TestCase
{
    
    public function testGetStudentByUsernameNotAuthenticated() {

        //valid student username
        $response = $this->get('/api/v1/student/student.test');

        $this->assertEquals(401, $response->getStatusCode());
        
    }

    public function testGetStudentByUsername() {

        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // valid student
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/student/richard.test');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
        
    }
    
    public function testGetStudentByUsernameNotStudent() {
        
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // invalid student
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/student/nicole.swan');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
        
    }

}