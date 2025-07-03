<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\TitleAccount\LoadTitleAccountDropdownInteractor;

class TitleAccountController extends Controller
{
    public function loadTitleAccountDropdown($mainAccount, $headingAccount, LoadTitleAccountDropdownInteractor $loadTitleAccountmDropdownInteractor)
    {
        return response()->json($loadTitleAccountmDropdownInteractor->execute(request('search_key'), $mainAccount, $headingAccount));
    }


}
