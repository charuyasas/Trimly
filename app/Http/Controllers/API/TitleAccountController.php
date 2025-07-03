<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\TitleAccount\LoadTitleAccountDropdownInteractor;

class TitleAccountController extends Controller
{
    public function loadTitleAccountDropdown($mainAcc, $headingAcc, LoadTitleAccountDropdownInteractor $loadTitleAccountmDropdownInteractor)
    {
        return response()->json($loadTitleAccountmDropdownInteractor->execute(request('q'), $mainAcc, $headingAcc));
    }


}
