<?php

namespace App\UseCases\Grn;

use App\Constance\AccountsLedgerCodes;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\StockSheet;
use App\UseCases\Grn\Requests\GrnRequest;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\StockSheet\Requests\StockSheetEntryDataRequest;
use App\UseCases\StockSheet\StoreStockSheetInteractor;
use Illuminate\Support\Facades\DB;

class FinalizeGrnInteractor
{
    public function execute(string $id, GrnRequest $request, StoreJournalEntryInteractor $storeJournalEntryInteractor,
                            StoreStockSheetInteractor $storeStockSheetInteractor): array
    {
        DB::beginTransaction();

        try {
            $grn = Grn::with('items')->lockForUpdate()->findOrFail($id);
            $totalBefore = $grn->items->sum('subtotal');
            $totalFOC = $grn->items->sum(fn($item) => $item->final_price * $item->foc);
            $grnNumber = $grn->grn_number;

            $discountInput = $request->discount_amount ?? 0;
            $isPercentage = $request->is_percentage ?? false;

            $calculatedDiscount = $isPercentage
                ? ($totalBefore * $discountInput / 100)
                : $discountInput;

            $grandTotal = round(max(0, $totalBefore - $calculatedDiscount), 2);

            if (empty($grnNumber)) {
                $last = Grn::where('status', true)
                    ->selectRaw('MAX(CAST(grn_number AS UNSIGNED)) as max_no')
                    ->lockForUpdate()
                    ->value('max_no');

                $next = ((int)$last) + 1;
                $grnNumber = str_pad((string)$next, 4, '0', STR_PAD_LEFT);
                $grn->grn_number = $grnNumber;
            }

            $grn->grand_total = $grandTotal;
            $grn->total_before_discount = $totalBefore;
            $grn->total_foc = $totalFOC;
            $grn->discount_amount = $discountInput;
            $grn->is_percentage = $isPercentage;
            $grn->note = $request->note;
            $grn->status = true;
            $grn->save();

            // Update average cost and last GRN price
            foreach ($grn->items as $grnItem) {
                $weightedAverageCost = GrnItem::where('item_id', $grnItem->item_id)
                    ->selectRaw('SUM(qty * price) / NULLIF(SUM(qty), 0) as weighted_avg')
                    ->value('weighted_avg');

                Item::where('id', $grnItem->item_id)->update([
                    'average_cost' => $weightedAverageCost ?? 0,
                    'last_grn_price' => $grnItem->price,
                ]);
            }

            $stockDebitEntryData = collect($request->items)->map(function ($item) use ($grnNumber) {
                $totalQty = ($item->qty ?? 0) + ($item->foc ?? 0);
                return [
                    'item_code'     => $item->item_id ?? '',
                    'ledger_code'   => AccountsLedgerCodes::LEDGER_CODES['MainStore'],
                    'description'   => 'GRN - ' . ($item->item_name ?? ''),
                    'debit'         => $totalQty,
                    'reference_type' => StockSheet::STATUS['GRN'],
                    'reference_id'   => 'GRN - ' . $grnNumber,
                ];
            })->toArray();
            $this->grnItemsToStockTable($storeStockSheetInteractor, $stockDebitEntryData);

            $journalEntries = [
                [
                    'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['MainStore'],
                    'reference_type' => JournalEntry::STATUS['GRN'],
                    'reference_id'   => 'GRN - ' . $grnNumber,
                    'debit'          => $grandTotal,
                    'credit'         => 0,
                ],
                [
                    'ledger_code'    => $request->supplier_ledger_code,
                    'reference_type' => JournalEntry::STATUS['GRN'],
                    'reference_id'   => 'GRN - ' . $grnNumber,
                    'debit'          => 0,
                    'credit'         => $grandTotal,
                ],
            ];

            $this->storeJournalEntries($storeJournalEntryInteractor, $journalEntries);

            DB::commit();

            return [
                'response' => [
                    'message' => 'GRN finalized successfully.',
                    'grn' => $grn->makeHidden(['created_at', 'updated_at', 'deleted_at']),
                ],
                'status' => 200
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'response' => [
                    'message' => 'Failed to finalize GRN.',
                    'error' => $e->getMessage(),
                ],
                'status' => 500
            ];
        }
    }

    public function grnItemsToStockTable(StoreStockSheetInteractor $storeStockSheetInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $stockRequest = StockSheetEntryDataRequest::validateAndCreate($entry);
            $storeStockSheetInteractor->execute($stockRequest);
        }

    }

    public function storeJournalEntries(StoreJournalEntryInteractor $storeJournalEntryInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $journalRequest = JournalEntryRequest::validateAndCreate($entry);
            $storeJournalEntryInteractor->execute($journalRequest);
        }
    }

}

