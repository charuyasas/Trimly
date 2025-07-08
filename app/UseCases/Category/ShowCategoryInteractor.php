<?php

namespace App\UseCases\Category;

use App\Models\Category;

class ShowCategoryInteractor {
    public function execute(Category $category): Category {
        return $category;
    }
}
