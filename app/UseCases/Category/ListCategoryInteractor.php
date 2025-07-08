<?php

namespace App\UseCases\Category;

use App\Models\Category;

class ListCategoryInteractor {
    public function execute() {
        return Category::latest()->get();
    }
}
