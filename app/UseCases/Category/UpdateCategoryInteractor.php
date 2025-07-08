<?php

namespace App\UseCases\Category;

use App\Models\Category;
use App\UseCases\Category\Requests\CategoryRequest;

class UpdateCategoryInteractor {
    public function execute(Category $category, CategoryRequest $request) {
        $category->update($request->toArray());
        return $category;
    }
}
