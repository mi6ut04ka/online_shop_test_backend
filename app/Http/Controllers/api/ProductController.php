<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function show($id): JsonResponse
    {
        $product = Product::find($id);

        return response()->json($product);
    }
}
