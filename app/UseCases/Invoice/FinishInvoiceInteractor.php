<?php

namespace App\UseCases\Invoice;

use App\Constance\AccountsLedgerCodes;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Service;
use App\Models\StockSheet;
use App\Models\SystemConfiguration;
use App\UseCases\Invoice\Requests\InvoiceRequest;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\StockSheet\GetAvailableStockInteractor;
use App\UseCases\StockSheet\Requests\StockSheetEntryDataRequest;
use App\UseCases\StockSheet\StoreStockSheetInteractor;
use Illuminate\Support\Facades\DB;

class FinishInvoiceInteractor
{
    public function execute(
        string $id,
        InvoiceRequest $invoiceRequest,
        StoreJournalEntryInteractor $storeJournalEntryInteractor,
        StoreStockSheetInteractor $storeStockSheetInteractor,
        GetAvailableStockInteractor $getAvailableStockInteractor
    ): array {
        DB::beginTransaction();

        try {
            // Get max discount configuration
            $config = SystemConfiguration::where('configuration_name', 'Discount Percentages')->first();
            $maxDiscount = $config?->configuration_data['Maximum Discount Percentage'] ?? null;

            $invoice = Invoice::with('items')->findOrFail($id);
            $baseTotal = $invoice->items->sum('sub_total');

            $discountAmount = $invoiceRequest->discount_amount ?? 0;
            $discountPercentage = $invoiceRequest->discount_percentage ?? 0;
            $received_cash = $invoiceRequest->received_cash;
            $balance = $invoiceRequest->balance;

            // Validate invoice-level discount
            if ($maxDiscount !== null && $discountPercentage > $maxDiscount) {
                return [
                    'message' => "Invoice discount percentage ({$discountPercentage}%) exceeds allowed maximum of {$maxDiscount}%.",
                    'status'  => 422,
                ];
            }

            // Validate item-level discounts
            foreach ($invoice->items as $item) {
                if ($maxDiscount !== null && $item->discount_percentage > $maxDiscount) {
                    return [
                        'message' => "Item '{$item->item_description}' discount percentage ({$item->discount_percentage}%) exceeds allowed maximum of {$maxDiscount}%.",
                        'status'  => 422,
                    ];
                }
            }

            // Apply discount preference
            if ($discountPercentage > 0) {
                $discountAmount = ($baseTotal * $discountPercentage) / 100;
                $invoice->discount_percentage = $discountPercentage;
                $invoice->discount_amount = 0;
            } elseif ($discountAmount > 0) {
                $invoice->discount_percentage = 0;
                $invoice->discount_amount = $discountAmount;
            }

            // Stock availability check
            $insufficientItem = collect($invoice->items)->first(function ($item) use ($getAvailableStockInteractor) {
                if ($item->item_type === 'item') {
                    $requiredQty = $item->quantity ?? 0;
                    $availableQty = $getAvailableStockInteractor->execute(
                        $item->item_id,
                        AccountsLedgerCodes::LEDGER_CODES['MainStore']
                    );
                    return $availableQty < $requiredQty;
                }
                return false;
            });

            if ($insufficientItem) {
                $required = $insufficientItem->quantity ?? 0;
                $available = $getAvailableStockInteractor->execute(
                    $insufficientItem->item_id,
                    AccountsLedgerCodes::LEDGER_CODES['MainStore']
                );

                throw new \Exception("Insufficient stock for item: {$insufficientItem->item_description}. Required: {$required}, Available: {$available}");
            }

            $invoiceNumber = $this->generateNextInvoiceNo();

            //Stock sheet credit entries
            $stockCreditEntryData = collect($invoice->items)->map(function ($item) use ($invoiceNumber) {
                if ($item->item_type === 'item') {
                    return [
                        'item_code'      => $item->item_id ?? '',
                        'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['MainStore'],
                        'description'    => 'Invoice - ' . ($item->item_description ?? ''),
                        'credit'         => $item->quantity ?? 0,
                        'reference_type' => StockSheet::STATUS['Sale'],
                        'reference_id'   => 'Invoice - ' . $invoiceNumber,
                    ];
                }
                return null;
            })->filter()->values()->toArray();

            if (!empty($stockCreditEntryData)) {
                $this->invoiceItemsToStockTable($storeStockSheetInteractor, $stockCreditEntryData);
            }

            // Sales totals
            $serviceCount = 0;
            $itemCount = 0;
            $totals = collect($invoice->items)->reduce(function ($carry, $item) use (&$itemCount, &$serviceCount) {
                $amount = ($item->quantity * $item->amount) - ($item->discount_amount ?? 0);

                if ($item->item_type === 'service') {
                    $carry['service_amount'] += $amount;
                    $serviceCount++;
                }

                if ($item->item_type === 'item') {
                    $carry['item_sales_amount'] += $amount;
                    $carry['item_cost'] += ($item->item->average_cost ?? 0) * $item->quantity;
                    $itemCount++;
                }

                return $carry;
            }, [
                'service_amount' => 0,
                'item_sales_amount' => 0,
                'item_cost' => 0,
            ]);

            // Journal entries
            $journalEntries = [];
            $employee = Employee::findOrFail($invoiceRequest->employee_no);

//            dd($invoice->items);
            foreach ($invoice->items as $item) {

                if ($item->item_type == 'service' && !empty($item->employee_commission)) {
                    $commission = round($item->employee_commission, 2);
                    $service = Service::findOrFail($item->item_id);

                    if ($commission > 0) {
                        $journalEntries[] = [
                            'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['Commission Expenses'],
                            'reference_type' => JournalEntry::STATUS['Expenses'],
                            'reference_id'   => "Commission Expenses-{$invoiceNumber}-{$service->code}",
                            'debit'          => $commission,
                            'credit'         => 0,
                        ];

                        // Credit employee posting ledger
                        $journalEntries[] = [
                            'ledger_code'    => $employee->ledger_code,
                            'reference_type' => JournalEntry::STATUS['Expenses'],
                            'reference_id'   => "Commission Expenses-{$invoiceNumber}-{$service->code}",
                            'debit'          => 0,
                            'credit'         => $commission,
                        ];
                    }
                }
            }

            if ($totals['service_amount'] > 0) {
                $discount = $itemCount == 0 ? $discountAmount : 0;
                $journalEntries[] = [
                    'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'],
                    'reference_type' => JournalEntry::STATUS['Sale'],
                    'reference_id'   => 'Service Invoice - ' . $invoiceNumber,
                    'debit'          => round(max(0, $totals['service_amount'] - $discount), 2),
                    'credit'         => 0,
                ];
                $journalEntries[] = [
                    'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['Income'],
                    'reference_type' => JournalEntry::STATUS['Sale'],
                    'reference_id'   => 'Service Invoice - ' . $invoiceNumber,
                    'debit'          => 0,
                    'credit'         => round(max(0, $totals['service_amount'] - $discount), 2),
                ];
            }

            if ($totals['item_sales_amount'] > 0) {
                $discount = $serviceCount == 0 ? $discountAmount : 0;
                $journalEntries[] = [
                    'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'],
                    'reference_type' => JournalEntry::STATUS['Sale'],
                    'reference_id'   => 'Item Invoice - ' . $invoiceNumber,
                    'debit'          => round(max(0, $totals['item_sales_amount'] - $discount), 2),
                    'credit'         => 0,
                ];
                $journalEntries[] = [
                    'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['Sales Revenue'],
                    'reference_type' => JournalEntry::STATUS['Sale'],
                    'reference_id'   => 'Item Invoice - ' . $invoiceNumber,
                    'debit'          => 0,
                    'credit'         => round(max(0, $totals['item_sales_amount'] - $discount), 2),
                ];
                $journalEntries[] = [
                    'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['Income'],
                    'reference_type' => JournalEntry::STATUS['Sale'],
                    'reference_id'   => 'Item Invoice - ' . $invoiceNumber,
                    'debit'          => round(max(0, $totals['item_cost']), 2),
                    'credit'         => 0,
                ];
                $journalEntries[] = [
                    'ledger_code'    => AccountsLedgerCodes::LEDGER_CODES['MainStore'],
                    'reference_type' => JournalEntry::STATUS['Sale'],
                    'reference_id'   => 'Item Invoice - ' . $invoiceNumber,
                    'debit'          => 0,
                    'credit'         => round(max(0, $totals['item_cost']), 2),
                ];
            }

            $this->storeJournalEntries($storeJournalEntryInteractor, $journalEntries);

            $invoice->grand_total = round(max(0, $baseTotal - $discountAmount), 2);
            $invoice->status = Invoice::STATUS['FINISHED'];
            $invoice->received_cash = $received_cash;
            $invoice->balance = $balance;
            $invoice->invoice_no = $invoiceNumber;
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

    public function storeJournalEntries(StoreJournalEntryInteractor $storeJournalEntryInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $journalRequest = JournalEntryRequest::validateAndCreate($entry);
            $storeJournalEntryInteractor->execute($journalRequest);
        }
    }

    public function invoiceItemsToStockTable(StoreStockSheetInteractor $storeStockSheetInteractor, array $entries): void
    {
        foreach ($entries as $entry) {
            $stockRequest = StockSheetEntryDataRequest::validateAndCreate($entry);
            $storeStockSheetInteractor->execute($stockRequest);
        }
    }
}
