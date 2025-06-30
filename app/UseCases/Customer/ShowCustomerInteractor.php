<?php

namespace App\UseCases\Customer;

use App\Models\Customer;

class ShowCustomerInteractor
{
    public function execute(Customer $customer)
    {
        return $customer;
    }
}
