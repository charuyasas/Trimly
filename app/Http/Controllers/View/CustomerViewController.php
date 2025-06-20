<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerViewController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|regex:/^0\d{9}$/|max:10',
            'address' => 'required|string|max:255',
        ]);

        Customer::create($request->all());

        return response()->json([
        'message' => 'Customer added successfully!',
        'redirect' => route('add.customer')
    ]);

    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $id,
            'phone' => 'required|regex:/^0\d{9}$/|max:10',
            'address' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return response()->json([
        'message' => 'Customer updated successfully!',
        'redirect' => route('view.customers')
    ]);
    }

public function destroy($id)
{
    $customer = Customer::findOrFail($id);
    $customer->delete();

    return response()->json(['message' => 'Customer deleted successfully.']);
}

}

