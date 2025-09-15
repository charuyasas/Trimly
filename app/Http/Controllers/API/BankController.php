<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function loadBanksDropdown(Request $request): JsonResponse
    {
        $search = $request->get('search_key');
        $banks = Bank::where('bank_name', 'like', "%$search%")
            ->limit(10)
            ->get();

        $results = [];

        foreach ($banks as $bank) {
            $results[] = [
                'label' => $bank->bank_code . ' - ' . $bank->bank_name,
                'value' => $bank->id
            ];
        }

        return response()->json($results);
    }
}
