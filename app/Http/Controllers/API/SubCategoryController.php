<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\UseCases\SubCategory\Requests\SubCategoryRequest;
use App\UseCases\SubCategory\ListSubCategoryInteractor;
use App\UseCases\SubCategory\StoreSubCategoryInteractor;
use App\UseCases\SubCategory\ShowSubCategoryInteractor;
use App\UseCases\SubCategory\UpdateSubCategoryInteractor;
use App\UseCases\SubCategory\DeleteSubCategoryInteractor;
use Illuminate\Http\JsonResponse;

class SubCategoryController extends Controller
{
    public function index(ListSubCategoryInteractor $interactor) {
        return $interactor->execute();
    }

    public function store(StoreSubCategoryInteractor $interactor): JsonResponse {
        $data = SubCategoryRequest::validateAndCreate(request()->all());
        return response()->json($interactor->execute($data), 201);
    }

    public function show(SubCategory $subCategory, ShowSubCategoryInteractor $interactor) {
        return $interactor->execute($subCategory);
    }

    public function update(SubCategory $subCategory, UpdateSubCategoryInteractor $interactor): JsonResponse {
        $data = SubCategoryRequest::validateAndCreate(request());
        return response()->json($interactor->execute($subCategory, $data));
    }

    public function destroy(SubCategory $subCategory, DeleteSubCategoryInteractor $interactor): JsonResponse {
        $interactor->execute($subCategory);
        return response()->json(null, 204);
    }
}

