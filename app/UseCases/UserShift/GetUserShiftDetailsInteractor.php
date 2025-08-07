<?php

namespace App\UseCases\UserShift;

use App\Constance\AccountsLedgerCodes;
use App\Models\JournalEntry;
use App\Models\ShiftDetails;

class GetUserShiftDetailsInteractor
{
    public function execute(int $shiftId): array
    {
        // Fetch shift details with user relation
        $shift = ShiftDetails::with('user')
            ->where('shift_id', $shiftId)
            ->firstOrFail();

        $shiftInTime = $shift->shift_in_time;
        $shiftOffTime = $shift->shift_off_time;

        // Get all sales entries for this shift
        $salesEntries = JournalEntry::where('ledger_code', AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'])
            ->where('debit', '>', 0)
            ->whereBetween('created_at', [$shiftInTime, $shiftOffTime])
            ->get();

        // Get all expense entries for this shift
        $expenseEntries = JournalEntry::where('ledger_code', 'like', '4-8-20-%')
            ->where('debit', '>', 0)
            ->whereBetween('created_at', [$shiftInTime, $shiftOffTime])
            ->get();

        $totalSales = $salesEntries->sum('debit');
        $totalExpenses = $expenseEntries->sum('debit');
        $openingCash = $shift->opening_cash_in_hand;
        $dayEndCash = $shift->day_end_cash_in_hand;

        $cashShortage = null;
        if (!is_null($dayEndCash)) {
            $cashShortage = $dayEndCash - ($openingCash + $totalSales - $totalExpenses);
        }

        return [
            'user_name' => $shift->user->name ?? null,
            'shift_id' => $shift->shift_id,
            'shift_in_time' => $shiftInTime,
            'opening_cash_in_hand' => $openingCash,
            'day_end_cash_in_hand' => $dayEndCash,
            'total_daily_sales' => $totalSales,
            'total_daily_expenses' => $totalExpenses,
            'cash_shortage' => $cashShortage,
            'shift_off_time' => $shift->shift_off_time,
            'sales_entries' => $salesEntries,
            'expense_entries' => $expenseEntries,
        ];
    }
}
