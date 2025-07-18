<?php

namespace App\Services;

use App\Models\Category;

/**
 * Class CategoryService.
 */
class CategoryService
{
    public function getCategories()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();

        return $categories;
    }

    public function storeCategory(array $categoryData)
    {
        $category = Category::create($categoryData);

        return $category;
    }

    public function getCategoryById($id)
    {
        $category = Category::findOrFail($id);

        return $category;
    }


    public function updateCategory(Category $category, array $categoryData)
    {
        $category->category_name = $categoryData['category_name'] ?? $category->category_name;

        $category->save();

        return $category;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
    }
}
