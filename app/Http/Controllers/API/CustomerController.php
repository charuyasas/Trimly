<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:10|unique:customers,phone',
            'address' => 'nullable|string|max:255',
        ]);

        return Customer::create($validated);
    }

    public function show(string $id)
    {
        return Customer::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('customers', 'email')->ignore($id)],
            'phone' => ['nullable', 'string', 'max:10', Rule::unique('customers', 'phone')->ignore($id)],
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($validated);

        return $customer;
    }

    public function destroy(string $id)
    {
        Customer::destroy($id);
        return response()->json(null, 204);
    }
}

