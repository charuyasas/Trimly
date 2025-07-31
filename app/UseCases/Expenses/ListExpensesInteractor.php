<?php

namespace App\UseCases\Expenses;

use App\Models\Expenses;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ListExpensesInteractor
{
    public function execute(int $userId)
    {
        $user = User::find($userId);

        $currentUser = auth()->user();
        $isAdmin = $currentUser ? $currentUser->hasRole('admin') : false;
//        dd($isAdmin);

        if ($user && $user->hasRole('admin')) {
            $expenses = Expenses::with(['postingAccount', 'user'])->get();
        } else {
            $expenses = Expenses::with(['postingAccount', 'user'])
                ->where('user_id', $userId)
                ->get();
        }

        $mappedExpenses = $expenses->map(function ($expense) {
            $expense->user_name = $expense->user->name ?? null;
            $expense->posting_account_name = $expense->postingAccount->posting_account ?? null;
            return $expense;
        });

        return [
            'is_admin' => $isAdmin,
            'expenses' => $mappedExpenses,
        ];

    }
}
