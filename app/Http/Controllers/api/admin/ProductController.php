<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::find($validated['category_id']);
        $position = $category->products()->count();

        Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'position' => $position,
        ]);

        return response()->json(['success' => 'Продукт создан']);
    }
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::find($id);

        if($product->category_id != $validated['category_id']){
            $category = Category::find($validated['category_id']);
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'price' => $validated['price'],
                'position' => $category->products()->count(),
            ]);
        }else{
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'price' => $validated['price']
            ]);
        }

        return response()->json(['success', 'Продукт обновлен']);
    }

    public function updatePosition(Request $request, $id): JsonResponse
    {
        $validate = $request->validate([
            'position' => 'required|integer',
        ]);

        $product = Product::findOrFail($id);

        $product->position = $validate['position'];
        $product->save();

        return response()->json($product);

    }

    public function destroy($id): JsonResponse
    {
        Product::destroy($id);

        return response()->json(['success', 'Продукт удален']);
    }
}
