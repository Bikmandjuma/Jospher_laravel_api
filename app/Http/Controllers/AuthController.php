<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT Authorization"
 * )
 */

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Jospher",
 *         version="1.0.0",
 *         description="Jospher_API Description"
 *     )
 * )
 */

class AuthController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login','register']]);
        $this->middleware('guest', ['except' => ['email','password']]);
    }

    /**
     *  * @OA\Post(
     * path="/api/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Authentication"}, 
     *      @OA\Parameter(
     *          name="email",
     *          description="use email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *       ),
     *      @OA\Parameter(
     *          name="password",
     *          description="use password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfull logged in."
     *     ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad user Input",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */

    public function login(Request $request){

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        $credentials = $request->only('email', 'password');
        
        // Debugging log to see what credentials are being passed
        \Log::info('Login credentials:', $credentials);

        $admin_token = auth::guard('admin')->attempt($credentials);

        if ($admin_token) {
            $admin = auth::guard('admin')->user();
            return response()->json([
                'status' => 'Admin success login',
                'admin_data' => $admin,
                'authorisation' => [
                    'token' => $admin_token,
                    'type' => 'bearer',
                ]
            ], 200);
        }  else {
            return response()->json([
                'status' => 'error',
                'wrong_Cred' => 'Wrong credentials, try again!',
            ], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        
        return response()->json([
            'status' => 'success',
            'logout_message' => 'Successfully logged out',
        ]);
    }

}