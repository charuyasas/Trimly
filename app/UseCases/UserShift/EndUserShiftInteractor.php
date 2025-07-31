<?php

namespace App\UseCases\UserShift;

use App\Constance\AccountsLedgerCodes;
use App\Models\JournalEntry;
use App\Models\ShiftDetails;
use App\UseCases\UserShift\Requests\ShiftDetailsRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EndUserShiftInteractor
{
    public function execute(ShiftDetailsRequest $shiftDetailsRequest): array
    {
        return DB::transaction(function () use ($shiftDetailsRequest) {
            $shift = ShiftDetails::with('user')
                ->where('user_id', $shiftDetailsRequest->user_id)
                ->where('status', true)
                ->firstOrFail();

            $shiftInTime = $shift->shift_in_time;

            $salesEntries = JournalEntry::where('ledger_code', AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'])
                ->where('debit', '>', 0)
                ->whereBetween('created_at', [$shiftInTime, now()])
                ->get();

            $expenseEntries = JournalEntry::where('ledger_code', 'like', '4-8-20-%')
                ->where('debit', '>', 0)
                ->whereBetween('created_at', [$shiftInTime, now()])
                ->get();

            $totalSales = $salesEntries->sum('debit');
            $totalExpenses = $expenseEntries->sum('debit');

            $openingCash = $shift->opening_cash_in_hand;
            $dayEndCash = $shiftDetailsRequest->day_end_cash_in_hand;

            $cashShortage = $dayEndCash - ($openingCash + $totalSales - $totalExpenses);

            $shift->update([
                'status' => false,
                'shift_off_time' => Carbon::now(),
                'day_end_cash_in_hand' => $dayEndCash,
                'total_daily_sales' => $totalSales,
                'total_daily_expenses' => $totalExpenses,
                'cash_shortage' => $cashShortage,
            ]);

            return [
                'user_name' => $shift->user->name ?? null,
                'shift_id' => $shift->shift_id,
                'shift_in_time' => $shift->shift_in_time,
                'opening_cash_in_hand' => $openingCash,
                'day_end_cash_in_hand' => $dayEndCash,
                'total_daily_sales' => $totalSales,
                'total_daily_expenses' => $totalExpenses,
                'cash_shortage' => $cashShortage,
                'shift_off_time' => $shift->shift_off_time,
                'sales_entries' => $salesEntries,
                'expense_entries' => $expenseEntries,
            ];
        });
    }
}
