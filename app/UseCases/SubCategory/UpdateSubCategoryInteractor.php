<?php

namespace App\UseCases\SubCategory;

use App\Models\SubCategory;
use App\UseCases\SubCategory\Requests\SubCategoryRequest;

class UpdateSubCategoryInteractor {
    public function execute(SubCategory $subCategory, SubCategoryRequest $request) {
        $subCategory->update($request->toArray());
        return $subCategory;
    }
}
