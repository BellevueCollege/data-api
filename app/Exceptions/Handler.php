<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //only return prettified errors if we're not in debug mode
        if ( !getenv('APP_DEBUG') ) {
    
            //provide more helpful errors for most likely exception types
            if ( $exception instanceof PDOException ) {
                return response()->json(['error' => 500, 'message' => "Database unavailable."], 500);
            } elseif ( $exception instanceof NotFoundHttpException ) {
                return response()->json(['error' => $exception->getStatusCode(), 'message' => $exception->getMessage()?: "API path does not exist."], 404);
            } elseif ( $exception instanceof MissingParameterException ){
                return response()->json(['error' => 400, 'message' => $exception->getMessage()?:"Invalid parameter provided."], 400);
            } elseif ( $exception instanceof Exception ) {
                return response()->json(['error' => 400, 'message' => "Misc error occurred."], 400);
            }
        }
        
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        //if ($request->expectsJson()) {
        if ( $request->is('*api/*') ) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    
        return redirect()->guest(route('admin.login'));
    }
}
