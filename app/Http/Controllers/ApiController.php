<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Response as IlluminateResponse;

class ApiController extends Controller {

    /**
    * Parent controller class to provide basic functions like JSON response
    **/
    protected $statusCode = IlluminateResponse::HTTP_OK;

    /**
    * Return status code
    **/
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
    * Set the status code
    **/
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
    * Return JSON response for data, with optional headers
    **/
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

}