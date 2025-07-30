<?php

namespace App\UseCases\Reports;

use Illuminate\Support\Facades\DB;

class StockValueReportInteractor
{
    public function execute()
    {
        return DB::table('items')
            ->leftJoin('stock_sheets', 'stock_sheets.item_code', '=', 'items.id')
            ->select(
                'items.id',
                'items.code',
                'items.description',
                'items.average_cost',
                DB::raw('COALESCE(SUM(stock_sheets.debit), 0) - COALESCE(SUM(stock_sheets.credit), 0) as stock_balance'),
                DB::raw('items.average_cost * (COALESCE(SUM(stock_sheets.debit), 0) - COALESCE(SUM(stock_sheets.credit), 0)) as total_stock_value')
            )
            ->groupBy('items.id', 'items.code', 'items.description', 'items.average_cost')
            ->havingRaw('(COALESCE(SUM(stock_sheets.debit), 0) - COALESCE(SUM(stock_sheets.credit), 0)) > 0')  //show only items with stock balance > 0
            ->orderBy('items.code')
            ->get();
    }
}
