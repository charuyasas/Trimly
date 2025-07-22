<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;
use App\UseCases\Invoice\Requests\InvoiceRequest;
use Illuminate\Support\Facades\DB;

class FinishInvoiceInteractor
{
    public function execute(string $id, InvoiceRequest $invoiceRequest): array
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($id);
            $baseTotal = $invoice->items->sum('sub_total');

            $discountAmount = $invoiceRequest->discount_amount ?? 0;
            $discountPercentage = $invoiceRequest->discount_percentage ?? 0;
            $received_cash = $invoiceRequest->received_cash;
            $balance = $invoiceRequest->balance;

            if ($discountPercentage > 0) {
                $discountAmount = ($baseTotal * $discountPercentage) / 100;
                $invoice->discount_percentage = $discountPercentage;
                $invoice->discount_amount = 0;
            } elseif ($discountAmount > 0) {
                $invoice->discount_percentage = 0;
                $invoice->discount_amount = $discountAmount;
            }

            $invoice->grand_total = round(max(0, $baseTotal - $discountAmount), 2);
            $invoice->status = Invoice::STATUS['FINISHED'];
            $invoice->received_cash = $received_cash;
            $invoice->balance = $balance;
            $invoice->invoice_no = $this->generateNextInvoiceNo();
            $invoice->save();

            DB::commit();

            return [
                'message' => 'Invoice finalized successfully.',
                'invoice' => $invoice->makeHidden(['created_at', 'updated_at', 'deleted_at']),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'message' => 'Failed to finish invoice.',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function generateNextInvoiceNo(): string
    {
        $last = Invoice::where('invoice_no', 'like', 'INV%')
            ->orderBy('invoice_no', 'desc')
            ->first();

        $next = $last && preg_match('/INV(\d+)/', $last->invoice_no, $matches)
            ? intval($matches[1]) + 1
            : 1;

        return 'INV' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
