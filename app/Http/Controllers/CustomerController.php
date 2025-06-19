<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display all customers.
     */
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form to create a new customer.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a new customer in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:customers,email',
            'phone'   => 'required|regex:/^0\d{9}$/|max:10',
            'address' => 'required|string|max:255',
        ]);

        Customer::create($request->only(['name', 'email', 'phone', 'address']));

        return redirect()->route('view.customers')->with('message', 'Customer added successfully!');
    }

    /**
     * Show the form to edit an existing customer.
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:customers,email,' . $id,
            'phone'   => 'required|regex:/^0\d{9}$/|max:10',
            'address' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->only(['name', 'email', 'phone', 'address']));

        return redirect()->route('view.customers')->with('message', 'Customer updated successfully!');
    }

    /**
     * Delete the specified customer.
     */
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return redirect()->route('view.customers')->with('message', 'Customer deleted.');
    }
}
