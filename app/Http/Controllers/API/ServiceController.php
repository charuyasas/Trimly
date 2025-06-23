<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::all();
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
            'code' => 'required|string|unique:services,code',
            'description' => 'required|string|max:255',
            'price' => 'nullable|numeric',
        ]);

        $service = Service::create($validated);
        return response()->json($service, 201);
    }

     public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'code' => [
            'required',
            'string',
            Rule::unique('services', 'code')->ignore($id),
        ],
            'description' => 'required|string|max:255',
            'price' => 'nullable|numeric',
        ]);

        $service = Service::findOrFail($id);
        $service->update($validated);
        return response()->json($service);
    }

    public function show(string $id)
    {
        return Service::findOrFail($id);
    }


    public function destroy(string $id)
    {
        Service::destroy($id);
        return response()->json(null, 204);
    }
}
