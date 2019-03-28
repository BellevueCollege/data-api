<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeControllerTest extends TestCase
{
    
    /** End point requires authentication so should fail */
    public function testGetEmployeeByUsernameNotAuthenticated() {
        //valid employee username
        $response = $this->get('/api/v1/employee/tlr.exempt');
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
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/employee/tlr.classified');
        
        //var_dump($response);
        $this->assertEquals(200, $response->status());
        
    }

    /*** Subdomain-specific endpoint tests ***/
    public function testGetEmployeeByUsernameAvailableAtConfiguredInternalSubdomain() {
    
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // try getting employee info using allowed subdomain for internal endpoints
        $response = $this->withHeaders($headers)->json('GET', 'https://' . config('dataapi.api_internal_domain') . '/api/v1/internal/employee/tlr.exempt');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
            
    }

    public function testGetEmployeeByUsernameNotAvailableOnOtherSubdomain() {
    
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // try getting employee info using subdomain that doesn't match the allowed subdomain for internal endpoints
        $response = $this->withHeaders($headers)->json('GET', 'https://' . env('TEST_NOTALLOWED_SUBDOMAIN') . '/api/v1/internal/employee/tlr.exempt');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(404, $response->getStatusCode());
            
    }

    public function testGetEmployeeByUsernameNotAvailableAtBaseDomain() {
    
        $creds = [ 'clientid' => env('TEST_CLIENTID'), 'password' => env('TEST_CLIENTKEY') ];
        $token = auth()->guard('api')->attempt($creds);
        $headers = [ 'Authorization' => 'Bearer ' . $token ];
    
        // try getting employee info using the base domain, which won't match the allowed subdomain for internal endpoints
        $response = $this->withHeaders($headers)->json('GET', '/api/v1/internal/employee/tlr.exempt');

        //var_dump($this->response->getStatusCode());
        $this->assertEquals(404, $response->getStatusCode());
            
    }

}