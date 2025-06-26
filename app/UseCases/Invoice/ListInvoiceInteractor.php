<?php

namespace App\UseCases\Invoice;

use App\Models\Invoice;

class ListInvoiceInteractor
{
    public function execute()
    {
        return Invoice::query()
        ->select([
            'invoices.id',
            'invoices.invoice_no',
            'invoices.discount_percentage',
            'invoices.discount_amount',
            'invoices.grand_total',
            'employees.name as employee_name',
            'customers.name as customer_name',
            ])
            ->join('employees', 'invoices.employee_no', '=', 'employees.id')
            ->join('customers', 'invoices.customer_no', '=', 'customers.id')
            ->where('status', '=', "1")
            ->orderBy('invoices.created_at', 'desc')
            ->get();
        }
    }

