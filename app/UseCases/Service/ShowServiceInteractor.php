<?php
namespace App\UseCases\Service;

use App\Models\Service;

 class ShowServiceInteractor {

    public function execute(Service $service){
        return $service;
    }

 }