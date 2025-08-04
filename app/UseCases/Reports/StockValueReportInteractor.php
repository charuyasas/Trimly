<?php

namespace App\UseCases\Reports;

use Illuminate\Support\Facades\DB;

class StockValueReportInteractor
{
    public function execute()
    {
        return DB::table('categories')
            ->leftJoin('sub_categories', 'sub_categories.category_id', '=', 'categories.id')
            ->leftJoin('items', 'items.sub_category_id', '=', 'sub_categories.id')
            ->leftJoin('stock_sheets', 'stock_sheets.item_code', '=', 'items.id')
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                'sub_categories.id as sub_category_id',
                'sub_categories.name as sub_category_name',
                'items.id as item_id',
                'items.code',
                'items.description',
                'items.average_cost',
                DB::raw('COALESCE(SUM(stock_sheets.debit), 0) as debit'),
                DB::raw('COALESCE(SUM(stock_sheets.credit), 0) as credit'),
                DB::raw('COALESCE(SUM(stock_sheets.debit), 0) - COALESCE(SUM(stock_sheets.credit), 0) as stock_balance'),
                DB::raw('items.average_cost * (COALESCE(SUM(stock_sheets.debit), 0) - COALESCE(SUM(stock_sheets.credit), 0)) as total_stock_value')
            )
            ->groupBy(
                'categories.id',
                'categories.name',
                'sub_categories.id',
                'sub_categories.name',
                'items.id',
                'items.code',
                'items.description',
                'items.average_cost'
            )
            ->havingRaw('(COALESCE(SUM(stock_sheets.debit), 0) - COALESCE(SUM(stock_sheets.credit), 0)) > 0')
            ->orderBy('categories.name')
            ->orderBy('sub_categories.name')
            ->orderBy('items.code')
            ->get();
    }

}
