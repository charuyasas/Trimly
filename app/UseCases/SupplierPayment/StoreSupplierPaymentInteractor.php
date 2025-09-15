<?php

namespace App\UseCases\SupplierPayment;

use App\Constance\AccountsLedgerCodes;
use App\Models\JournalEntry;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\UseCases\JournalEntry\Requests\JournalEntryRequest;
use App\UseCases\JournalEntry\StoreJournalEntryInteractor;
use App\UseCases\SupplierPayment\Requests\SupplierPaymentRequest;
use Illuminate\Support\Facades\DB;

class StoreSupplierPaymentInteractor
{
    public function execute(
        SupplierPaymentRequest $supplierPaymentRequest,
        StoreJournalEntryInteractor $storeJournalEntryInteractor
    ): SupplierPayment {
        return DB::transaction(function () use ($supplierPaymentRequest, $storeJournalEntryInteractor) {

            $supplier = Supplier::findOrFail($supplierPaymentRequest->supplier_id);

            $payment = SupplierPayment::create([
                'supplier_id'   => $supplierPaymentRequest->supplier_id,
                'payment_type'  => $supplierPaymentRequest->payment_type,
                'amount'        => $supplierPaymentRequest->amount,
                'payments'      => $supplierPaymentRequest->payments?->toArray(),
                'bank_id'     => $supplierPaymentRequest->bank_id,
                'bank_slip_no'  => $supplierPaymentRequest->bank_slip_no,
                'date'          => $supplierPaymentRequest->date ?? now()->toDateString(),
            ]);

            $creditLedgerCode = $supplierPaymentRequest->payment_type === 'bank'
                ? AccountsLedgerCodes::LEDGER_CODES['Banks']
                : AccountsLedgerCodes::LEDGER_CODES['Cash in Hand'];

            $amount = round((float) $supplierPaymentRequest->amount, 2);

            $reference = 'Supplier Payment - ' . $supplier->name;

            $entries = [
                [
                    'ledger_code'    => $supplier->ledger_code,
                    'reference_type' => JournalEntry::STATUS['SupplierPayment'],
                    'reference_id'   => $reference,
                    'debit'          => $amount,
                    'credit'         => 0,
                ],
                [
                    'ledger_code'    => $creditLedgerCode,
                    'reference_type' => JournalEntry::STATUS['SupplierPayment'],
                    'reference_id'   => $reference,
                    'debit'          => 0,
                    'credit'         => $amount,
                ],
            ];

            foreach ($entries as $entry) {
                $storeJournalEntryInteractor->execute(JournalEntryRequest::validateAndCreate($entry));
            }

            return $payment;
        });
    }
}

