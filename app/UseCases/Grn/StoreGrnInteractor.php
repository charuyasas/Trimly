<?php

namespace App\UseCases\Grn;

use App\Models\Grn;
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

            $grn = null;

            if (!empty($request->id)) {
                $grn = Grn::where('id', $request->id)->first();
            }

            if (!$grn && !empty($request->token_no)) {
                $grn = Grn::where('token_no', $request->token_no)
                    ->where('status', false)
                    ->first();
            }

            if ($grn) {
                $grn->update([
                    'grn_date' => $request->grn_date,
                    'supplier_id' => $request->supplier_id,
                    'supplier_invoice_number' => $request->supplier_invoice_number,
                    'grn_type' => $request->grn_type,
                    'note' => $request->note,
                    'total_before_discount' => $totalBefore,
                    'total_foc' => $totalFOC,
                    'grand_total' => $grandTotal,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'is_percentage' => $request->is_percentage,
                ]);

                $grn->items()->delete();
            } else {
                $tokenNo = $this->nextTokenNo();

                $grn = Grn::create([
                    'token_no' => $tokenNo,
                    'grn_number' => null,
                    'grn_date' => $request->grn_date,
                    'supplier_id' => $request->supplier_id,
                    'supplier_invoice_number' => $request->supplier_invoice_number,
                    'grn_type' => $request->grn_type,
                    'note' => $request->note,
                    'total_before_discount' => $totalBefore,
                    'total_foc' => $totalFOC,
                    'grand_total' => $grandTotal,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'is_percentage' => $request->is_percentage,
                    'status' => false,
                ]);
            }

            foreach ($request->items as $item) {
                $grn->items()->create([
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'qty' => $item->qty,
                    'foc' => $item->foc ?? 0,
                    'price' => $item->price,
                    'margin' => $item->margin ?? null,
                    'discount' => $item->discount ?? null,
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
                        'token_no' => $grn->token_no,
                        'grn_number' => $grn->grn_number,
                        'grn_date' => $request->grn_date,
                        'supplier_id' => $request->supplier_id,
                        'supplier_invoice_number' => $request->supplier_invoice_number,
                        'supplier_ledger_code' => $request->supplier_ledger_code,
                        'grn_type' => $request->grn_type,
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

    private function nextTokenNo(): string
    {
        $last = Grn::withTrashed()
            ->selectRaw('MAX(CAST(token_no AS UNSIGNED)) as max_token')
            ->lockForUpdate()
            ->value('max_token');

        $next = ((int)$last) + 1;
        return str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }
}

