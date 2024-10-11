<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $productQuery = Product::query();

            $productQuery->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });

            $products = $productQuery->with(['category'])->get();

            return $this->sendResponse("Get all products", $products);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validators = Validator::make($request->all(), [
                'category_id' => 'required|numeric|exists:categories,id',
                'name' => 'required|max:255',
                'price' => 'required|numeric|min:2000',
                'stok' => 'required|numeric|min:1',
            ]);

            if ($validators->fails()) {
                return $this->sendError("Validation error", $validators->errors());
            }

            $product = Product::create($validators->validated());

            return $this->sendResponse("Product created successfully", $product);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", $th->getMessage());
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $product = Product::with(['category'])->find($id);

            if (!$product) {
                return $this->sendError("Product not found", []);
            }

            return $this->sendResponse("Detail product", $product);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->sendError("Product not found", []);
            }

            $validators = Validator::make($request->all(), [
                'category_id' => 'required|numeric|exists:categories,id',
                'name' => 'required|max:255',
                'price' => 'required|numeric|min:2000',
                'stok' => 'required|numeric|min:1',
            ]);

            if ($validators->fails()) {
                return $this->sendError("Validation error", $validators->errors());
            }

            $product->update($validators->validated());

            return $this->sendResponse("Product updated successfully", $product);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->sendError("Product not found", []);
            }

            $product->delete();

            return $this->sendResponse("Product deleted successfully", $product);
        } catch (\Throwable $th) {
            return $this->sendError("Internal server error", []);
        }
    }
}
