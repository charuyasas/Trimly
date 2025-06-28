<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Http\Controllers\Controller;
use App\UseCases\Service\ListServiceIntractor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\UseCases\Service\Requests\ServiceRequest;
use App\UseCases\Service\StoreServiceInteractor;

class ServiceController extends Controller
{
    public function index(ListServiceIntractor $listEmployeeIntractor)
    {
        return $listEmployeeIntractor->execute();
    }

    public function store(StoreServiceInteractor $storeServiceInteractor)
    {

        $newService = $storeServiceInteractor->execute(ServiceRequest::validateAndCreate(request()));
        return response()->json($newService , 201);
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
