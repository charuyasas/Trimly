<?php

namespace App\UseCases\Customer;

use App\Models\Customer;
use App\UseCases\Customer\Requests\CustomerRequest;

class UpdateCustomerInteractor
{
    public function execute(Customer $customer, CustomerRequest $data): Customer
    {
        $customer->update($data->except('id')->toArray());
        return $customer;
    }
}
