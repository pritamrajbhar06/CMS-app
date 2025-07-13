<?php

namespace App\Services;
use App\Models\Category;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update($id, array $data): Category
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            throw new \Exception('Category not found');
        }

        $category->update($data);
        return $category;
    }

    public function getCategoryById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function delete(int $id): bool
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            throw new \Exception('Category not found');
        }

        return $category->delete();
    }

    public function getAllCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::all();
    }
}