<?php

namespace App\UseCases\Grn;

use App\Models\GrnItem;
use Illuminate\Support\Facades\DB;

class UpdateGrnItemInteractor
{
    public function execute(string $id, array $data): array
    {
        DB::beginTransaction();

        try {
            $item = GrnItem::findOrFail($id);
            $item->update([
                'item_name'    => $data['item_name'],
                'qty'          => $data['qty'],
                'foc'          => $data['foc'] ?? 0,
                'price'        => $data['price'],
                'margin'       => $data['margin'] ?? null,
                'discount'     => $data['discount'] ?? null,
                'final_price'  => $data['final_price'],
                'subtotal'     => $data['subtotal'],
            ]);

            // Recalculate and update average cost in item table
            $averageCost = GrnItem::where('item_id', $item->item_id)->avg('price');
            \App\Models\Item::where('id', $item->item_id)->update([
                'average_cost' => $averageCost,
            ]);

            // Recalculate GRN totals
            $this->recalculateGrnTotals($item->grn_id);

            DB::commit();

            return [
                'status' => 200,
                'item' => $item
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'status' => 500,
                'error' => $e->getMessage()
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
