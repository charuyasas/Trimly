<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\HeadingAccount\LoadHeadingAccountDropdownInteractor;

class HeadingAccountController extends Controller
{
    public function loadHeadingAccountDropdown($mainAcc, LoadHeadingAccountDropdownInteractor $loadHeadingAccountmDropdownInteractor)
    {
        return response()->json($loadHeadingAccountmDropdownInteractor->execute(request('q'),$mainAcc));
    }

}
