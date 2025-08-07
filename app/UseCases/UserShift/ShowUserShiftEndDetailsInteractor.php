<?php

namespace App\UseCases\UserShift;

use App\Constance\AccountsLedgerCodes;
use App\Models\JournalEntry;
use App\Models\ShiftDetails;
use Illuminate\Http\JsonResponse;

class ShowUserShiftEndDetailsInteractor
{
     public function execute($userId): JsonResponse
     {
         $shiftDetails = ShiftDetails::with('user')
             ->where('user_id', $userId)
             ->where('status', 0)
             ->first();

         $shiftInTime       = $shiftDetails->shift_in_time;

         $salesEntries = JournalEntry::where('ledger_code', AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'])
             ->where('debit', '>', 0)
             ->whereBetween('created_at', [$shiftInTime, now()])
             ->get();

         $expenseEntries = JournalEntry::where('ledger_code', 'like', '4-8-20-%')
             ->where('debit', '>', 0)
             ->whereBetween('created_at', [$shiftInTime, now()])
             ->get();

         return response()->json([
             'user_name' => $shiftDetails->user->name,
             'shift_id' => $shiftDetails->shift_id,
             'shift_in_time' => $shiftDetails->shift_in_time,
             'opening_cash_in_hand' => $shiftDetails->opening_cash_in_hand,
             'day_end_cash_in_hand' => $shiftDetails->day_end_cash_in_hand,
             'cash_shortage' => $shiftDetails->cash_shortage,
             'shift_off_time' => $shiftDetails->shift_off_time,

             'sales_entries' => $salesEntries,
             'expense_entries' => $expenseEntries,
         ]);


     }
}
