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
use App\UseCases\PostingAccount\Requests\PostingAccountRequest;
use App\UseCases\PostingAccount\StorePostingAccountInteractor;

class CustomerController extends Controller
{
    public function index(ListCustomerInteractor $list): \Illuminate\Database\Eloquent\Collection
    {
        return $list->execute();
    }

    public function store(StoreCustomerInteractor $store, StorePostingAccountInteractor $storePostingAccountInteractor): \Illuminate\Http\JsonResponse
    {
        $ledgerCode = $this->createLedgerCode($storePostingAccountInteractor);
        $customerData = array_merge(
            request()->all(),
            ['ledger_code' => $ledgerCode]
        );

        $newCustomer = CustomerRequest::validateAndCreate($customerData);
        return response()->json($store->execute($newCustomer), 201);
    }

    public function show(Customer $customer, ShowCustomerInteractor $show): Customer
    {
        return $show->execute($customer);
    }

    public function update(Customer $customer, UpdateCustomerInteractor $update): \Illuminate\Http\JsonResponse
    {
        $data = CustomerRequest::validateAndCreate(request());
        return response()->json($update->execute($customer, $data));
    }

    public function destroy(Customer $customer, DeleteCustomerInteractor $delete): \Illuminate\Http\JsonResponse
    {
        $delete->execute($customer);
        return response()->json(null, 204);
    }

    public function loadCustomerDropdown(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = $request->get('search_key');
        $customer = Customer::where('phone', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%")
            ->limit(10)
            ->get();

        $results = [];

        foreach ($customer as $cus) {
            $results[] = [
                'label' => $cus->name . ' - ' . $cus->phone,
                'value' => $cus->id
            ];
        }

        return response()->json($results);
    }

    public function createLedgerCode(StorePostingAccountInteractor $storePostingAccountInteractor)
    {
        $data = [
            'posting_code'     => null,
            'posting_account'  => 'Customer Account',
            'main_code'        => 1,
            'heading_code'     => 2,
            'title_code'       => 5,
        ];

        $newPostingAccount = $storePostingAccountInteractor->execute(PostingAccountRequest::validateAndCreate($data));
        return $newPostingAccount['ledger_code'];
    }
}
