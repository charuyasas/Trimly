<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;
use App\UseCases\Invoice\Requests\InvoiceRequest;
use Illuminate\Support\Facades\DB;

class StoreInvoiceInteractor
{
    public function execute(InvoiceRequest $invoiceRequest)
    {
        DB::beginTransaction();

        try {
            $itemIds = collect($invoiceRequest->items)->pluck('item_id');
            if ($itemIds->duplicates()->isNotEmpty()) {
                return [
                    'response' => [
                        'message' => 'Duplicate item(s) detected in invoice items.',
                        'duplicates' => $itemIds->duplicates()->values()
                    ],
                    'status' => 422
                ];
            }

            $grandTotal = collect($invoiceRequest->items)->sum(fn($item) => floatval($item['sub_total']));

            $invoice = Invoice::where('invoice_no', $invoiceRequest->invoice_no)->first();

            if ($invoice) {
                $invoice->update([
                    'employee_no' => $invoiceRequest->employee_no,
                    'customer_no' => $invoiceRequest->customer_no,
                    'grand_total' => $grandTotal,
                ]);
                $invoice->items()->delete();
            } else {
                $last = Invoice::where('invoice_no', 'like', 'INV%')->orderBy('invoice_no', 'desc')->first();
                $nextNumber = $last && preg_match('/INV(\d+)/', $last->invoice_no, $matches)
                ? intval($matches[1]) + 1
                : 1;

                $invoice = Invoice::create([
                    'invoice_no' => 'INV' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
                    'employee_no' => $invoiceRequest->employee_no,
                    'customer_no' => $invoiceRequest->customer_no,
                    'grand_total' => $grandTotal,
                    'discount_percentage' => $invoiceRequest->discount_percentage ?? 0,
                    'discount_amount' => $invoiceRequest->discount_amount ?? 0,
                    'status' => 0,
                ]);
            }

            foreach ($invoiceRequest->items as $item) {
                $invoice->items()->create([
                    'item_id' => $item['item_id'],
                    'item_description' => $item['item_description'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['amount'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'sub_total' => $item['sub_total'],
                ]);
            }

            DB::commit();

            return [
                'response' => [
                    'message' => $invoice->wasRecentlyCreated ? 'Invoice created successfully' : 'Invoice updated.',
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_no' => $invoice->invoice_no,
                        'employee_no' => $invoice->employee_no,
                        'customer_no' => $invoice->customer_no,
                        'token_no' => $invoice->invoice_no,
                        'items' => $invoice->items()->get()->makeHidden(['created_at', 'updated_at', 'deleted_at']),
                        ]
                    ],
                    'status' => $invoice->wasRecentlyCreated ? 201 : 200
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                return [
                    'response' => ['message' => 'Failed to process invoice', 'error' => $e->getMessage()],
                    'status' => 500
                ];
            }
        }
    }
