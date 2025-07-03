<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\MainAccount\LoadMainAccountDropdownInteractor;

class MainAccountController extends Controller
{
    public function loadMainAccountDropdown(LoadMainAccountDropdownInteractor $loadMainAccountmDropdownInteractor)
    {
        return response()->json($loadMainAccountmDropdownInteractor->execute(request('q')));
    }


}
