<?php

namespace App\UseCases\Service;

use App\Models\Service;
use App\UseCases\Service\Requests\ServiceRequest;


 class StoreServiceInteractor {

    public function execute(ServiceRequest $serviceRequest){
        $service = Service::create($serviceRequest->toArray());
        return $service->toArray();
    }

 }