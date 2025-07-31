<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Expenses\GetCashBalanceInteractor;
use App\UseCases\Expenses\ListExpensesInteractor;
use App\UseCases\Expenses\LoadExpensesAccountDropdownInteractor;
use App\UseCases\Expenses\Requests\ExpensesRequest;
use App\UseCases\Expenses\StoreExpensesInteractor;
use Illuminate\Http\JsonResponse;

class ExpensesController extends Controller
{
    public function index(ListExpensesInteractor $listExpensesInteractor): array
    {
        return $listExpensesInteractor->execute(auth()->id());
    }

    public function store(StoreExpensesInteractor $storeExpensesInteractor): JsonResponse
    {
        request()->merge(['user_id' => auth()->id()]);
        $category = $storeExpensesInteractor->execute(ExpensesRequest::validateAndCreate(request()->all()));
        return response()->json($category, 201);
    }

    public function loadExpensesAccountDropdown(LoadExpensesAccountDropdownInteractor $loadExpensesAccountDropdownInteractor): JsonResponse
    {
        return response()->json($loadExpensesAccountDropdownInteractor->execute(request('search_key')));
    }

    public function getCashBalance(GetCashBalanceInteractor $getCashBalanceInteractor): JsonResponse
    {
        return response()->json($getCashBalanceInteractor->execute());
    }

}
