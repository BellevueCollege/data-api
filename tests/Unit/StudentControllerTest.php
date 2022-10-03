<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentControllerTest extends TestCase
{
    
    public function testGetStudentByUsernameNotAuthenticated() {

        //valid student username
        $response = $this-> json('GET','https://' . config('dataapi.api_internal_domain') . '/api/v1/internal/student/student.test');

        $this->assertEquals(401, $response->getStatusCode());
        
    }

    /*** These tests will go away once we move these endpoints to subdomain accessible only ***/
    public function testGetStudentByUsername() {

        //authenticate - make sure .env clientId/key is filled in
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // valid student
        $response = $this->withHeaders($headers)->json('GET','https://' . config('dataapi.api_internal_domain') . '/api/v1/internal/student/student.test');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
        
    }
    
    public function testGetStudentByUsernameNotStudent() {
        
        //authenticate - make sure .env clientId/key is filled in
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // invalid student
        $response = $this->withHeaders($headers)->json('GET', 'https://' . config('dataapi.api_internal_domain') . '/api/v1/internal/student/student.test');
        //var_dump($this->response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
        
    }
    /*** End test that will go away ***/

    /*** Subdomain-specific endpoint tests ***/
    public function testGetStudentByUsernameAvailableAtConfiguredInternalSubdomain() {
        
        //authenticate - make sure .env clientId/key is filled in
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // try getting student info using allowed internal subdomain
        $response = $this->withHeaders($headers)->json('GET', 'https://' . config('dataapi.api_internal_domain') . '/api/v1/internal/student/student.test');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
            
    }

    public function testGetStudentByUsernameNotAvailableOnOtherSubdomain() {
        
        //authenticate - make sure .env clientId/key is filled in
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // try getting student info using alternate subdomain that is not allowed internal subdomain
        $response = $this->withHeaders($headers)->json('GET', 'https://' . env('TEST_NOTALLOWED_SUBDOMAIN') . '/api/v1/internal/student/student.test');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(404, $response->getStatusCode());
            
    }

    public function testGetStudentByUsernameNotAvailableAtBaseDomain() {
        
        //authenticate - make sure .env clientId/key is filled in
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // try getting student info using base domain, which should not allow access to internal endpoints
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/internal/student/student.test');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(404, $response->getStatusCode());
            
    }
}