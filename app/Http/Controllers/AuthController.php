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

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="jwtAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     in="header"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="basicAuth",
 *     scheme="basic",
 *     in="header",
 * )
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
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        /*$credentials = request(['clientid', 'clientkey']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }*/

        try {
            //Config::set('auth.providers.users.model', \App\Models\Client::class);
            $req_creds = request()->only('clientid', 'clientkey');
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
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    /*
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function loginPost(Request $request)
    {
        $this->validate($request, [
            'clientid'    => 'required',
            'clientkey' => 'required',
        ]);

        try {
            $req_creds = $request->only('clientid', 'clientkey');
            $creds = [ 'clientid' => $req_creds['clientid'], 'password' => $req_creds['clientkey'] ];

            if (! $token = $this->auth->attempt($creds)) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], $e->getStatusCode());
        }

        return response()->json(compact('token'));
    }
}*/
