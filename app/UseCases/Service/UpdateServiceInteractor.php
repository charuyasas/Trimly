<?php

namespace App\UseCases\Service;

use App\Models\Service;
use App\UseCases\Service\Requests\ServiceRequest;

 class UpdateServiceInteractor {

    public function execute(Service $service, ServiceRequest $serviceRequest){
        $service->update($serviceRequest->except('id')->toArray());

        return $service;
    }

 }