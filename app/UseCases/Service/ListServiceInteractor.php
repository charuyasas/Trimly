<?php
namespace App\UseCases\Service;

use App\Models\Service;

 class ListServiceInteractor {

    public function execute(){
        return Service::all();
    }

 }