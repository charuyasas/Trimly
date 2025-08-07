<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;
use App\Models\SystemConfiguration;
use App\UseCases\Invoice\Requests\InvoiceRequest;
use Illuminate\Support\Facades\DB;

class StoreInvoiceInteractor
{
    public function execute(InvoiceRequest $invoiceRequest): array
    {
        $config = SystemConfiguration::where('configuration_name', 'Discount Percentages')->first();
        $maxDiscount = $config?->configuration_data['Maximum Discount Percentage'] ?? null;

        DB::beginTransaction();

        try {
            $itemIds = collect($invoiceRequest->items)->pluck('item_id');

            if ($itemIds->duplicates()->isNotEmpty()) {
                return [
                    'response' => [
                        'message' => 'Duplicate item(s) detected in invoice items.',
                        'duplicates' => $itemIds->duplicates()->values(),
                    ],
                    'status' => 422,
                ];
            }

            // 🔹 Validate invoice-level discount
            if ($maxDiscount !== null && $invoiceRequest->discount_percentage > $maxDiscount) {
                return [
                    'response' => [
                        'message' => "Invoice discount percentage ({$invoiceRequest->discount_percentage}%) exceeds allowed maximum of {$maxDiscount}%.",
                    ],
                    'status' => 422,
                ];
            }

            // 🔹 Validate each item discount
            foreach ($invoiceRequest->items as $item) {
                if ($maxDiscount !== null && $item->discount_percentage > $maxDiscount) {
                    return [
                        'response' => [
                            'message' => "Item '{$item->item_description}' discount percentage ({$item->discount_percentage}%) exceeds allowed maximum of {$maxDiscount}%.",
                        ],
                        'status' => 422,
                    ];
                }
            }

            // Recalculate subtotal instead of trusting request
            $grandTotal = collect($invoiceRequest->items)->sum(function ($item) {
                return ($item->quantity * $item->amount) - ($item->discount_amount ?? 0);
            });

            $invoice = Invoice::where('id', $invoiceRequest->token_no)->first();

            if ($invoice) {
                $invoice->update([
                    'employee_no' => $invoiceRequest->employee_no,
                    'customer_no' => $invoiceRequest->customer_no,
                    'grand_total' => $grandTotal,
                    'discount_percentage' => $invoiceRequest->discount_percentage ?? 0,
                    'discount_amount' => $invoiceRequest->discount_amount ?? 0,
                ]);

                $invoice->items()->delete();
            } else {
                $invoice = Invoice::create([
                    'token_no' => $this->generateNextTokenNo(),
                    'employee_no' => $invoiceRequest->employee_no,
                    'customer_no' => $invoiceRequest->customer_no,
                    'grand_total' => $grandTotal,
                    'discount_percentage' => $invoiceRequest->discount_percentage ?? 0,
                    'discount_amount' => $invoiceRequest->discount_amount ?? 0,
                    'status' => Invoice::STATUS['PENDING'],
                ]);
            }

            foreach ($invoiceRequest->items as $item) {
                $subTotal = $item->quantity * $item->amount;
                $invoice->items()->create([
                    'item_id' => $item->item_id,
                    'item_description' => $item->item_description,
                    'item_type' => $item->item_type,
                    'quantity' => $item->quantity,
                    'amount' => $item->amount,
                    'discount_percentage' => $item->discount_percentage ?? 0,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'sub_total' => ($item->quantity * $item->amount) - ($item->discount_amount ?? 0),
                ]);
            }

            DB::commit();

            return [
                'response' => [
                    'message' => $invoice->wasRecentlyCreated ? 'Invoice created successfully' : 'Invoice updated.',
                    'invoice' => [
                        'id' => $invoice->id,
                        'employee_no' => $invoice->employee_no,
                        'customer_no' => $invoice->customer_no,
                        'token_no' => $invoice->token_no,
                        'items' => $invoice->items()->get()->makeHidden(['created_at', 'updated_at', 'deleted_at']),
                    ],
                ],
                'status' => $invoice->wasRecentlyCreated ? 201 : 200,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'response' => [
                    'message' => 'Failed to process invoice',
                    'error' => $e->getMessage(),
                ],
                'status' => 500,
            ];
        }
    }

    private function generateNextTokenNo(): string
    {
        $last = Invoice::orderBy('token_no', 'desc')->first();
        $next = $last && is_numeric($last->token_no)
            ? intval($last->token_no) + 1
            : 1;

        return str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
