<?php

namespace App\UseCases\SubCategory;

use App\Models\SubCategory;

class ListSubCategoryInteractor {
    public function execute() {
        return SubCategory::select('id', 'name')->latest()->get();
    }
}
