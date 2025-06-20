<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(Customer::latest()->get(), 200);
    }

    public function store(Request $request)
    {
      $validated = $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:customers,email',
        'phone' => 'required|string|max:15',
        'address' => 'nullable|string|max:255',
      ]);

      Customer::create($validated);

      return response()->json([
        'message' => 'Customer added successfully!'
      ], 200); // 🔁 make sure status code is 200
   }


    public function show($id)
    {
        return response()->json(Customer::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('customers')->ignore($id)],
            'phone' => 'required|regex:/^0\d{9}$/|max:10',
            'address' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return response()->json(['message' => 'Customer updated', 'data' => $customer]);
    }

    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return response()->json(['message' => 'Customer deleted']);
    }
}
