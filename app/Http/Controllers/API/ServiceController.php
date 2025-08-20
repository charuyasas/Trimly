<?php

namespace App\Http\Controllers\API;

use App\Models\Service;
use App\Http\Controllers\Controller;
use App\UseCases\Service\DeleteServiceInteractor;
use App\UseCases\Service\ListServiceInteractor;
use App\UseCases\Service\Requests\ServiceRequest;
use App\UseCases\Service\ShowServiceInteractor;
use App\UseCases\Service\StoreServiceInteractor;
use App\UseCases\Service\UpdateServiceInteractor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(ListServiceInteractor $listServiceInteractor): Collection
    {
        return $listServiceInteractor->execute();
    }

    public function store(StoreServiceInteractor $storeServiceInteractor): JsonResponse
    {

        $newService = $storeServiceInteractor->execute(ServiceRequest::validateAndCreate(request()));
        return response()->json($newService , 201);
    }

    public function show(Service $service, ShowServiceInteractor $showServiceInteractor): Service
    {
        return $showServiceInteractor->execute($service);
    }

    public function update(Service $service, UpdateServiceInteractor $updateServiceInteractor): JsonResponse
    {
        $updateService = $updateServiceInteractor->execute($service, ServiceRequest::validateAndCreate(request()));
        return response()->json($updateService);
    }

    public function destroy(Service $service, DeleteServiceInteractor $deleteServiceInteractor): JsonResponse
    {
        $deleteServiceInteractor->execute($service);
        return response()->json(null, 204);
    }

    public function loadServiceDropdown(Request $request): JsonResponse
    {
       $search = $request->get('search_key');

       $services = Service::where('description', 'like', "%$search%")
                    ->limit(10)
                    ->get();

       $results = [];

        foreach ($services as $srv) {
         $results[] = [
            'label' => $srv->description . ' - Rs.' . number_format($srv->price, 2),
            'value' => $srv->id
         ];
        }

        return response()->json($results);
    }


}
