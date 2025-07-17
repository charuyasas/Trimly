<?php

namespace App\UseCases\Grn;

use App\Models\GrnItem;
use Illuminate\Support\Facades\DB;

class DeleteGrnItemInteractor
{
    public function execute(string $itemId): array
    {
        DB::beginTransaction();

        try {
            $item = GrnItem::findOrFail($itemId);
            $grnId = $item->grn_id;

            $item->delete();

            // Update GRN totals after deletion
            $this->recalculateGrnTotals($grnId);

            DB::commit();

            return [
                'status' => 200,
                'message' => 'Item deleted successfully',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function recalculateGrnTotals(string $grnId): void
    {
        $items = GrnItem::where('grn_id', $grnId)->get();

        $totalBeforeDiscount = $items->sum('subtotal');
        $totalFOC = $items->sum(fn ($item) => $item->foc * $item->final_price);
        $grn = \App\Models\Grn::find($grnId);

        $discount = $grn->discount_amount ?? 0;
        $isPercentage = $grn->is_percentage ? true : false;

        $grandTotal = $isPercentage
            ? $totalBeforeDiscount * (1 - $discount / 100)
            : $totalBeforeDiscount - $discount;

        $grn->update([
            'total_before_discount' => $totalBeforeDiscount,
            'total_foc' => $totalFOC,
            'grand_total' => $grandTotal,
        ]);
    }
}

