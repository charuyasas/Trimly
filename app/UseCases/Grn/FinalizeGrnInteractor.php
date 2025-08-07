<?php

namespace App\UseCases\Grn;

use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\Item;
use App\UseCases\Grn\Requests\GrnRequest;
use Illuminate\Support\Facades\DB;

class FinalizeGrnInteractor
{
    public function execute(string $id, GrnRequest $request): array
    {
        DB::beginTransaction();

        try {
            $grn = Grn::with('items')->findOrFail($id);
            $totalBefore = $grn->items->sum('subtotal');
            $totalFOC = $grn->items->sum(fn($item) => $item->final_price * $item->foc);

            $discountInput = $request->discount_amount ?? 0;
            $isPercentage = $request->is_percentage ?? false;

            $calculatedDiscount = $isPercentage
                ? ($totalBefore * $discountInput / 100)
                : $discountInput;

            $grandTotal = round(max(0, $totalBefore - $calculatedDiscount), 2);

            $grn->grand_total = $grandTotal;
            $grn->total_before_discount = $totalBefore;
            $grn->total_foc = $totalFOC;
            $grn->discount_amount = $discountInput; // Save raw input value
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
}
