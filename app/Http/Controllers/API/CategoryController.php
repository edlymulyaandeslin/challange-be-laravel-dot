<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $categoryQuery = Category::query();

            $categoryQuery->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });

            $categories = $categoryQuery->with(['products'])->get();

            return $this->sendResponse("Get all categories", $categories);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validators = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'slug' => 'required|max:255'
            ]);

            if ($validators->fails()) {
                return $this->sendError("Validation error", $validators->errors());
            }

            $category = Category::create($validators->validated());

            return $this->sendResponse("Category created successfully", $category);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", $th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $category = Category::with(['products'])->find($id);

            if (!$category) {
                return $this->sendError("Category not found", []);
            }

            return $this->sendResponse("Detail category", $category);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->sendError("Category not found", []);
            }

            $validators = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'slug' => 'required|max:255'
            ]);

            if ($validators->fails()) {
                return $this->sendError("Validation error", $validators->errors());
            }

            $category->update($validators->validated());

            return $this->sendResponse("Category updated successfully", $category);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->sendError("Category not found", []);
            }

            $category->delete();

            return $this->sendResponse("Category deleted successfully", $category);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }
}
