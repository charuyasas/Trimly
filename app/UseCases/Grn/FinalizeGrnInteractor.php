<?php

namespace App\UseCases\Grn;

use App\Models\Grn;
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
            $grn->store_location = $request->store_location;
            $grn->note = $request->note;
            $grn->status = true;
            $grn->save();

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
