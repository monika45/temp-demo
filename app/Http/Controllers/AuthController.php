<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Manager;

class AuthController extends Controller
{
    protected $jwt;
    protected $manager;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt, Manager $manager)
    {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'refresh']]);
        $this->jwt = $jwt;
        $this->manager = $manager;
    }


    public function register(Request $request)
    {

    }



    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->responseJson(['err' => 1, 'msg' => 'Unauthorized'], 401);
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
        $user = auth('api')->user();
        if (empty($user)) {
            return $this->responseSuccess([]);
        }
        $company = $user->company;
        $result = [
            'name' => $user->name,
            'email' => $user->email,
            'company_name' => $company ? $company->name : ''
        ];
        return $this->responseJson($result);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return $this->responseJson(['msg' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $token = $this->manager->refresh($this->jwt->getToken())->get();
        } catch (JWTException $e) {
            return $this->responseError($e->getMessage());
        }
        return $this->respondWithToken($token);
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
        return $this->responseJson([
            'access_token' => $token,
//            'token_type' => 'Bearer',
//            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'expires_at' => time() + auth('api')->factory()->getTTL() * 60,
            'company_id' => $this->authUser()->company_id
        ]);
    }

}
