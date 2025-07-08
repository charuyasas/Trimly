<?php

namespace App\UseCases\Category;

use App\Models\Category;
use App\UseCases\Category\Requests\CategoryRequest;

class StoreCategoryInteractor {
    public function execute(CategoryRequest $request) {
        return Category::create($request->toArray());
    }
}
