<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);

        $position = 0;
        if (!empty($validated['parent_id'])) {
            $parentCategory = Category::find($validated['parent_id']);
            if ($parentCategory) {
                $position = $parentCategory->children()->count();
            }
        }
        else{
            $position = Category::whereNull('parent_id')->count();
        }

        Category::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'position' => $position,
        ]);
        return response()->json(['success' => 'Категория успешно создана']);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $validate['name'],
            'parent_id' => $validate['parent_id'] ?? null,
        ]);
        return response()->json($category);
    }

    public function destroy($id): JsonResponse
    {
        Category::destroy($id);
        return response()->json(['succsess' => 'Категория удалена']);
    }

    public function updatePosition(Request $request, $id): JsonResponse
    {
        $validate = $request->validate([
            'position' => 'required|integer',
        ]);
        $category = Category::findOrFail($id);

        $category->position = $validate['position'];
        $category->save();

        return response()->json($category);
    }
}
