<?php

namespace App\Services\User\customer;

use App\Repositories\User\Customer\CustomerRepository;
use Illuminate\Support\Facades\Auth;

class CustomerService {

    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function showMe()
    {
        $user = Auth::user();
        return $this->customerRepository->findByEmail($user->email);
    }
}