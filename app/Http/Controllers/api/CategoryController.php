<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with(['products', 'childrenRecursive'])
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();

        return response()->json($categories);
    }

    public function getProducts($id): JsonResponse
    {
        $products = Product::where('category_id', $id)->orderBy('position')->get();
        return response()->json($products);
    }

    public function show($id): JsonResponse
    {
        $category = Category::find($id);
        return response()->json($category);
    }
}
