<?php

namespace App\Services\User\customer;

use App\Repositories\User\Customer\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CustomerService {

    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function showMe()
    {
        $user = Auth::user();
        return $this->customerRepository->findByEmail($user->email);
    }
}