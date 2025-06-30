<?php
namespace App\UseCases\Service;

use App\Models\Service;

 class DeleteServiceInteractor {

    public function execute(Service $service){
        return $service->delete();
    }

 }