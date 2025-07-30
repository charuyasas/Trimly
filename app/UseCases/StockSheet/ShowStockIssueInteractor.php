<?php
namespace App\UseCases\StockSheet;

use App\Models\StockSheet;

class ShowStockIssueInteractor {

    public function execute(string $referenceId)
    {
        $fullReferenceId = 'Employee Issue - ' . str_pad($referenceId, 3, '0', STR_PAD_LEFT);

        $stockSheets = StockSheet::with('items')
            ->where('reference_type', 'Employee Issue')
            ->where('reference_id', $fullReferenceId)
            ->where('debit', '<>', 0)
            ->get();

        return $stockSheets->map(function ($stockSheet) {
            return [
                'item_code'        => $stockSheet->items->code ?? '',
                'item_description' => $stockSheet->items->description ?? '',
                'quantity'         => $stockSheet->debit,
            ];
        });
    }

}
