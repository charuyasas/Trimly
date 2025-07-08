<?php

namespace App\UseCases\Category;

use App\Models\Category;

class DeleteCategoryInteractor {
    public function execute(Category $category): ?bool {
        return $category->delete();
    }
}
