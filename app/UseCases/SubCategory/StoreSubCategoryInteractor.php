<?php

namespace App\UseCases\SubCategory;

use App\Models\SubCategory;
use App\UseCases\SubCategory\Requests\SubCategoryRequest;

class StoreSubCategoryInteractor {
    public function execute(SubCategoryRequest $request) {
        return SubCategory::create($request->toArray());
    }
}
