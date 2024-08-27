<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/v1/auth/customer/register",
     *     summary="Register a new Customer",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/RegisterUserRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error registering user"
     *     )
     * )
     */
    public function customerRegister(RegisterUserRequest $request)
    {        
        $registerResultCustomer = $this->authService->customerRegister($request->validated());
        
        if (!$registerResultCustomer) {
            return $this->error("Error al registrar el usuario.", 400, $registerResultCustomer);
        }

        return $this->success($registerResultCustomer, "Usuario registrado exitosamente!", 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/customer/login",
     *     summary="Login a Customer",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/LoginUserRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your_jwt_token"),
     *             @OA\Property(property="message", type="string", example="Login successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function customerLogin(LoginUserRequest $request)
    {        
        $loginResultCustomer = $this->authService->customerLogin($request->validated());
        
        if (!$loginResultCustomer['success']) {
            return $this->error($loginResultCustomer['message'], 400, $loginResultCustomer['data']);
        }

        return $this->success($loginResultCustomer['data'], $loginResultCustomer['message'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/admin/register",
     *     summary="Register a new Admin",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/RegisterUserRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error registering admin"
     *     )
     * )
     */
    public function adminRegister(RegisterUserRequest $request)
    {   
        $registratedAdminUser = $this->authService->adminRegister($request->validated());
        
        if (!$registratedAdminUser) {
            return $this->error("", 400, $registratedAdminUser);
        }

        return $this->success($registratedAdminUser, "Usuario registrado exitosamente!", 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/admin/login",
     *     summary="Login an Admin",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/LoginUserRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your_jwt_token"),
     *             @OA\Property(property="message", type="string", example="Login successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function adminLogin(LoginUserRequest $request)
    {
        
        $loginResultAdmin = $this->authService->adminLogin($request->validated());
        
        if (!$loginResultAdmin) {
            return $this->error("", 400, $loginResultAdmin);
        }

        return $this->success($loginResultAdmin['data'], $loginResultAdmin['message'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     summary="Logout the user (revoke the token)",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        //$user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/customer/logout",
     *     summary="Logout a customer (revoke the token)",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function customerLogout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Cliente deslogueado.']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/admin/logout",
     *     summary="Logout an admin (revoke the token)",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function adminLogout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Admin deslogueado.']);
    }
}
