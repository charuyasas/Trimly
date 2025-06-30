<?php

namespace App\UseCases\Customer;

use App\Models\Customer;
use App\UseCases\Customer\Requests\CustomerRequest;

class StoreCustomerInteractor
{
    public function execute(CustomerRequest $data)
    {
        return Customer::create($data->toArray());
    }
}
