<?php

namespace App\UseCases\SubCategory;

use App\Models\SubCategory;

class ShowSubCategoryInteractor {
    public function execute(SubCategory $subCategory): SubCategory {
        return $subCategory->load('category');
    }
}
