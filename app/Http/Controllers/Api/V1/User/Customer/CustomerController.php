<?php

namespace App\Http\Controllers\Api\V1\User\Customer;

use App\Http\Controllers\Controller;
use App\Services\User\customer\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customer",
     *     summary="Display the authenticated user's details",
     *     @OA\Response(
     *         response=200,
     *         description="Returns the details of the authenticated user",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-26T00:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-26T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show()
    {
        $user = $this->customerService->showMe();
        return response()->json($user);
    }
}
