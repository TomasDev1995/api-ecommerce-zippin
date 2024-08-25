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
     * Register a new Customer.
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
     * Login a Customer.
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
     * Register a new Admin.
     */
    public function adminRegister(RegisterUserRequest $request)
    {
        $validatedData = $request->validated();
        
        $registratedAdminUser = $this->authService->adminRegister($validatedData);
        
        if (!$registratedAdminUser) {
            return $this->error("", 400, $registratedAdminUser);
        }

        return $this->success($registratedAdminUser, "Usuario registrado exitosamente!", 201);
    }

    /**
     * Login a admin.
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
     * Logout the user (revoke the token).
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        //$user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
