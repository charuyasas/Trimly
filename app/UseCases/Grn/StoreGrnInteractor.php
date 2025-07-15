<?php

namespace App\UseCases\Grn;

use App\Models\Grn;
use App\Models\GrnItem;
use App\UseCases\Grn\Requests\GrnRequest;
use Illuminate\Support\Facades\DB;

class StoreGrnInteractor
{
    public function execute(GrnRequest $request): array
    {
        DB::beginTransaction();

        try {
            $totalBefore = 0;
            $totalFOC = 0;

            foreach ($request->items as $item) {
                $totalBefore += $item->subtotal;
                $totalFOC += $item->final_price * $item->foc;
            }

            $grandTotal = $totalBefore;
            if ($request->discount_amount > 0) {
                $grandTotal = $request->is_percentage
                    ? $grandTotal * (1 - ($request->discount_amount / 100))
                    : $grandTotal - $request->discount_amount;
            }

            $grn = Grn::where('grn_number', $request->grn_number)->first();

            if ($grn) {
                $grn->update([
                    'grn_date' => $request->grn_date,
                    'supplier_id' => $request->supplier_id,
                    'supplier_invoice_number' => $request->supplier_invoice_number,
                    'grn_type' => $request->grn_type,
                    'total_before_discount' => $totalBefore,
                    'total_foc' => $totalFOC,
                    'grand_total' => $grandTotal,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'is_percentage' => $request->is_percentage,
                ]);

                $grn->items()->delete();
            } else {
                $grn = Grn::create([
                    'grn_number' => $request->grn_number,
                    'grn_date' => $request->grn_date,
                    'supplier_id' => $request->supplier_id,
                    'supplier_invoice_number' => $request->supplier_invoice_number,
                    'grn_type' => $request->grn_type,
                    'total_before_discount' => $totalBefore,
                    'total_foc' => $totalFOC,
                    'grand_total' => $grandTotal,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'is_percentage' => $request->is_percentage,
                ]);
            }

            foreach ($request->items as $item) {
                $grn->items()->create([
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'qty' => $item->qty,
                    'foc' => $item->foc,
                    'price' => $item->price,
                    'margin' => $item->margin,
                    'discount' => $item->discount,
                    'final_price' => $item->final_price,
                    'subtotal' => $item->subtotal,
                ]);
            }

            DB::commit();

            return [
                'response' => [
                    'message' => 'GRN saved successfully.',
                    'grn' => [
                        'id' => $grn->id,
                        'grn_number' => $grn->grn_number,
                        'grn_date' => $request->grn_date,
                        'supplier_id' => $request->supplier_id,
                        'supplier_invoice_number' => $request->supplier_invoice_number,
                        'supplier_ledger_code' => $request->supplier_ledger_code,
                        'grn_type' => $request->grn_type,
                        'store_location' => $request->store_location,
                        'note' => $request->note,
                        'discount_amount' => $request->discount_amount ?? 0,
                        'is_percentage' => $request->is_percentage,
                        'items' => $grn->items->map(function ($item) {
                            return [
                                'id' => $item->id ?? '',
                                'grn_id' => $item->grn_id ?? '',
                                'item_id' => $item->item_id ?? '',
                                'item_name' => $item->item_name ?? '',
                                'qty' => $item->qty ?? '',
                                'foc' => $item->foc ?? '',
                                'price' => $item->price ?? '',
                                'margin' => $item->margin ?? '',
                                'discount' => $item->discount ?? '',
                                'final_price' => $item->final_price ?? '',
                                'subtotal' => $item->subtotal ?? '',
                            ];
                        }),
                    ],
                ],
                'status' => 201
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'response' => ['message' => 'Failed to store GRN', 'error' => $e->getMessage()],
                'status' => 500
            ];
        }
    }
}

