<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getCategories();

        return response()->json([
            'status' => 200,
            'message' => 'Categories Fetched Successfully',
            'categories' => $categories,
        ], 200);
    }

    public function totalCategories()
    {
        $totalCategories = $this->categoryService->getTotalCategories();

        return response()->json([
            'status' => 200,
            'message' => 'Total Categories Fetched Successfully',
            'totalCategories' => $totalCategories,
        ], 200);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->storeCategory($request->validated());

        return response([
            'status' => 200,
            'message' => 'Category Created Successfully',
            'category' => $category
        ], 200);
    }

    public function edit($id)
    {
        $category = $this->categoryService->getCategoryById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Category Fetched Successfully',
            'category' => $category
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->getCategoryById($id);

        $updateCategory = $this->categoryService->updateCategory($category, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Category Updated Successfully',
            'category' => $updateCategory
        ], 200);
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);

        return response()->json([
            'status' => 200,
            'message' => 'Category Deleted Succesfully',
        ], 200);
    }
}
