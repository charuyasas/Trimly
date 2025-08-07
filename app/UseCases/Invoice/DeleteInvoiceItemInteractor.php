<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class DeleteInvoiceItemInteractor
{
    public function execute(string $invoiceId, string $itemId): array
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($invoiceId);

            $item = $invoice->items()->where('item_id', $itemId)->first();

            if (!$item) {
                return [
                    'response' => [
                        'message' => 'Item not found in the invoice.',
                    ],
                    'status' => 404,
                ];
            }

            $item->delete();

            // Recalculate the invoice grand total
            $newGrandTotal = $invoice->items()->sum(DB::raw('(quantity * amount) - IFNULL(discount_amount, 0)'));

            $invoice->update([
                'grand_total' => $newGrandTotal,
            ]);

            DB::commit();

            return [
                'response' => [
                    'message' => 'Item deleted successfully.',
                    'invoice' => [
                        'id' => $invoice->id,
                        'employee_no' => $invoice->employee_no,
                        'customer_no' => $invoice->customer_no,
                        'token_no' => $invoice->token_no,
                        'items' => $invoice->items()->get()->makeHidden(['created_at', 'updated_at', 'deleted_at']),
                        'grand_total' => $invoice->grand_total,
                    ],
                ],
                'status' => 200,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'response' => [
                    'message' => 'Failed to delete item.',
                    'error' => $e->getMessage(),
                ],
                'status' => 500,
            ];
        }
    }
}
