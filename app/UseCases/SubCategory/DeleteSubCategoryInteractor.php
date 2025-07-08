<?php

namespace App\UseCases\SubCategory;

use App\Models\SubCategory;

class DeleteSubCategoryInteractor {
    public function execute(SubCategory $subCategory): ?bool {
        return $subCategory->delete();
    }
}
