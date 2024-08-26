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
     * Display the authenticated user's details.
     */
    public function show()
    {
        $user = $this->customerService->showMe();
        return response()->json($user);
    }
}
