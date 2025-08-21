<?php

namespace App\UseCases\Reports;

use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class GetSalesSummaryInteractor
{
    public function execute(string $startDate, string $endDate, string $itemType, string $reportType): array
    {
        $invoices = Invoice::with(['items.item', 'employee'])
            ->where('status', Invoice::STATUS['FINISHED'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->get();

        if ($reportType === 'summary') {
            // Employee wise detail report
            $summary = $invoices->map(function ($invoice) use ($itemType) {
                $employeeName = optional($invoice->employee)->name ?? 'Unknown';

                $totalSales = 0;
                $totalDiscount = 0;
                $totalCommission = 0;
                $totalProfit = 0;

                $items = $invoice->items->filter(function ($item) use ($itemType) {
                    return $itemType === 'all' || $item->item_type === $itemType;
                });

                foreach ($items as $item) {
                    $totalSales += $item->sub_total;
                    $totalDiscount += $item->discount_amount;

                    if ($item->item_type === 'service') {
                        $totalCommission += $item->employee_commission ?? 0;
                        $totalProfit += $item->sub_total - ($item->employee_commission ?? 0);
                    }

                    if ($item->item_type === 'item') {
                        $salesItem = Item::find($item->item_id);
                        $lastGRNPrice = $salesItem->last_grn_price ?? 0;
                        $totalCommission += ($lastGRNPrice * $item->quantity) ?? 0;
                        $totalProfit += $item->sub_total - ($lastGRNPrice * $item->quantity);
                    }
                }

                return [
                    'date'             => $invoice->created_at->format('Y-m-d'),
                    'invoice_no'       => $invoice->invoice_no,
                    'employee_id'      => $invoice->employee_no,
                    'employee_name'    => $employeeName,
                    'total_sales'      => round($totalSales, 2),
                    'total_discount'   => round($totalDiscount, 2),
                    'total_commission' => round($totalCommission, 2),
                    'profit'           => round($totalProfit - $totalDiscount, 2),
                ];
            })->filter(fn ($row) => $row['total_sales'] > 0)
                ->values();
        } else {
            // Sales summary aggregated by employee
            $summary = $invoices->groupBy('employee_no')->map(function ($employeeInvoices, $employeeId) use ($itemType) {
                $employeeName = optional($employeeInvoices->first()->employee)->name ?? 'Unknown';

                $totalSales = 0;
                $totalDiscount = 0;
                $totalCommission = 0;
                $totalProfit = 0;

                foreach ($employeeInvoices as $invoice) {
                    $items = $invoice->items->filter(function ($item) use ($itemType) {
                        return $itemType === 'all' || $item->item_type === $itemType;
                    });

                    foreach ($items as $item) {
                        $totalSales += $item->sub_total;
                        $totalDiscount += $item->discount_amount;

                        if ($item->item_type === 'service') {
                            $totalCommission += $item->employee_commission ?? 0;
                            $totalProfit += $item->sub_total - ($item->employee_commission ?? 0);
                        }

                        if ($item->item_type === 'item') {
                            $salesItem = Item::find($item->item_id);
                            $lastGRNPrice = $salesItem->last_grn_price ?? 0;
                            $totalCommission += ($lastGRNPrice * $item->quantity) ?? 0;
                            $totalProfit += $item->sub_total - ($lastGRNPrice * $item->quantity);
                        }
                    }
                }

                return [
                    'employee_id'      => $employeeId,
                    'employee_name'    => $employeeName,
                    'total_sales'      => round($totalSales, 2),
                    'total_discount'   => round($totalDiscount, 2),
                    'total_commission' => round($totalCommission, 2),
                    'profit'           => round($totalProfit - $totalDiscount, 2),
                ];
            })->filter(fn ($row) => $row['total_sales'] > 0)
                ->values();
        }

        return [
            'status' => 200,
            'data'   => $summary,
        ];
    }
}
