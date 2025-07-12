<?php

namespace App\UseCases\Customer;

use App\Models\Customer;

class ListCustomerInteractor
{
    public function execute(): \Illuminate\Database\Eloquent\Collection
    {
        return Customer::all();
    }
}
