<?php

namespace App\UseCases\Customer;

use App\Models\Customer;

class DeleteCustomerInteractor
{
    public function execute(Customer $customer): bool|int|null
    {
        return $customer->delete();
    }
}
