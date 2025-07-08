<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\UseCases\Category\Requests\CategoryRequest;
use App\UseCases\Category\ListCategoryInteractor;
use App\UseCases\Category\StoreCategoryInteractor;
use App\UseCases\Category\ShowCategoryInteractor;
use App\UseCases\Category\UpdateCategoryInteractor;
use App\UseCases\Category\DeleteCategoryInteractor;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(ListCategoryInteractor $interactor) {
        return $interactor->execute();
    }

    public function store(StoreCategoryInteractor $interactor): JsonResponse {
        $category = $interactor->execute(CategoryRequest::validateAndCreate(request()->all()));
        return response()->json($category, 201);
    }

    public function show(Category $category, ShowCategoryInteractor $interactor) {
        return $interactor->execute($category);
    }

    public function update(Category $category, UpdateCategoryInteractor $interactor): JsonResponse {
        return response()->json($interactor->execute($category, CategoryRequest::validateAndCreate(request())));
    }

    public function destroy(Category $category, DeleteCategoryInteractor $interactor): JsonResponse {
        $interactor->execute($category);
        return response()->json(null, 204);
    }

    public function loadCategoryDropdown()
    {
        return response()->json(
            Category::where('name', 'like', '%' . request('search_key') . '%')
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(fn($cat) => [
                    'label' => $cat->name,
                    'value' => $cat->id
                ])
        );
    }
}
