<?php

namespace App\UseCases\Invoice;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetDailySalesInteractor
{
    public function execute(string $startDate, string $endDate): array
    {
        // Ensure dates are valid Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Fetch sales grouped by date
        $sales = DB::table('invoices')
            ->selectRaw('DATE(created_at) as day, SUM(grand_total) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Build a complete range of dates
        $result = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayStr = $date->toDateString();
            $result[] = [
                'day' => $dayStr,
                'total' => isset($sales[$dayStr]) ? (float)$sales[$dayStr] : 0,
            ];
        }

        return $result;
    }
}
