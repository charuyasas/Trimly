<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::all();
    }
    
    public function store(Request $request)
    {
        $service = Service::create($request->only(['code', 'description', 'price']));
        return response()->json($service, 201);
    }
    
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);
        $service->update($request->only(['code', 'description', 'price']));
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
