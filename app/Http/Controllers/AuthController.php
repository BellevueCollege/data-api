<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AuthTokenResource;

/**
 * Authentication Controller
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['internalLogin', 'publicLogin']]);
    }

    /**
     * Login on Internal API Server
     * 
     * Pass in the clientid and clientkey as parameters, and a token will be returned.
     * 
     * **Note**: This endpoint is only available on the internal API server.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return AuthTokenResource
     */
    public function internalLogin(Request $request)
    {
        $validated = $request->validate([
            /** @query */
            'clientid' => ['required', 'string'],
            /** @query */
            'clientkey' => ['required', 'string'],
        ]);
        return $this->performLogin($request);
    }

    /**
     * Login on Public API Server
     * 
     * Pass in the clientid and clientkey as parameters, and a token will be returned.
     * 
     * **Note**: This endpoint is only available on the public API server.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return AuthTokenResource
     */
    public function publicLogin(Request $request)
    {
        $validated = $request->validate([
            /** @query */
            'clientid' => ['required', 'string'],
            /** @query */
            'clientkey' => ['required', 'string'],
        ]);
        return $this->performLogin($request);
    }

    /**
     * Shared Login Method
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return AuthTokenResource
     */
    public function performLogin(Request $request)
    {
        try {
            $req_creds = $request->only('clientid', 'clientkey');
            $creds = [ 'clientid' => $req_creds['clientid'], 'password' => $req_creds['clientkey'] ];

            if (! $token = auth()->guard('api')->attempt($creds)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], $e->getStatusCode());
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return AuthTokenResource
     */
    protected function respondWithToken($token)
    {
        return new AuthTokenResource([
            'access_token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
