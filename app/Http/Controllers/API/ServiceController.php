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

class ServiceController extends Controller
{
    public function index(ListServiceInteractor $listServiceInteractor)
    {
        return $listServiceInteractor->execute();
    }

    public function store(StoreServiceInteractor $storeServiceInteractor)
    {

        $newService = $storeServiceInteractor->execute(ServiceRequest::validateAndCreate(request()));
        return response()->json($newService , 201);
    }

    public function show(Service $service, ShowServiceInteractor $showServiceInteractor)
    {
        return $showServiceInteractor->execute($service);
    }

    public function update(Service $service, UpdateServiceInteractor $updateServiceInteractor)
    {
        $updateService = $updateServiceInteractor->execute($service, ServiceRequest::validateAndCreate(request()));
        return response()->json($updateService);
    }

    public function destroy(Service $service, DeleteServiceInteractor $deleteServiceInteractor)
    {
        $deleteServiceInteractor->execute($service);
        return response()->json(null, 204);
    }
}
