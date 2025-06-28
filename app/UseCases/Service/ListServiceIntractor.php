<?php
namespace App\UseCases\Service;

use App\Models\Service;

 class ListServiceIntractor {

    public function execute(){
        return Service::all();
    }

 }