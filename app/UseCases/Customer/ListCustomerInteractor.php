<?php

namespace App\UseCases\Customer;

use App\Models\Customer;

class ListCustomerInteractor
{
    public function execute()
    {
        return Customer::all();
    }
}
