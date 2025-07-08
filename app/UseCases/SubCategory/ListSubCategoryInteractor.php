<?php

namespace App\UseCases\SubCategory;

use App\Models\SubCategory;

class ListSubCategoryInteractor {
    public function execute() {
        return SubCategory::with('category')->latest()->get();
    }
}
