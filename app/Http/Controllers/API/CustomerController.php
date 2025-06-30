<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\UseCases\Customer\Requests\CustomerRequest;
use App\UseCases\Customer\ListCustomerInteractor;
use App\UseCases\Customer\StoreCustomerInteractor;
use App\UseCases\Customer\ShowCustomerInteractor;
use App\UseCases\Customer\UpdateCustomerInteractor;
use App\UseCases\Customer\DeleteCustomerInteractor;

class CustomerController extends Controller
{
    public function index(ListCustomerInteractor $list)
    {
        return $list->execute();
    }

    public function store(StoreCustomerInteractor $store)
    {
        $data = CustomerRequest::validateAndCreate(request());
        return response()->json($store->execute($data), 201);
    }

    public function show(Customer $customer, ShowCustomerInteractor $show)
    {
        return $show->execute($customer);
    }

    public function update(Customer $customer, UpdateCustomerInteractor $update)
    {
        $data = CustomerRequest::validateAndCreate(request());
        return response()->json($update->execute($customer, $data));
    }

    public function destroy(Customer $customer, DeleteCustomerInteractor $delete)
    {
        $delete->execute($customer);
        return response()->json(null, 204);
    }
}
