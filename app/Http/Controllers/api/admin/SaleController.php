<?php

namespace App\Http\Controllers\api\admin;

use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function index(): JsonResponse
    {
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->get();

        return response()->json($sales);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        Sale::create([
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'time_of_sale' => now()
        ]);

        return response()->json(['success' => 'Продажа добавлена']);
    }

    public function destroy($id): JsonResponse
    {
        Sale::destroy($id);

        return response()->json(['success', 'Продажа удалена']);
    }

    public function reports(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|integer',
            'month' => 'required|date_format:Y-m',
            'year' => 'required|digits:4',
            'type' => 'required|in:json,csv',
        ]);

        $categoryId = $request->category_id;
        $month = $request->month;
        $year = $request->year;
        $type = $request->type;

        $categoryIds = $categoryId ? $this->getCategoryAndChildren($categoryId) : [];

        $salesQuery = Sale::with('product');

        if ($categoryId) {
            $salesQuery->whereHas('product', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            });
        }

        $sales = $salesQuery
            ->whereYear('time_of_sale', $year)
            ->whereMonth('time_of_sale', substr($month, 5, 2))
            ->get();

        $reportData = $sales->groupBy('time_of_sale')->map(function ($daySales, $date) {
            $totalAmount = 0;

            $salesData = $daySales->map(function ($sale) use (&$totalAmount) {
                $cost = $sale->price * $sale->quantity;
                $totalAmount += $cost;

                return [
                    'product' => $sale->product_name,
                    'price' => $sale->price,
                    'quantity' => $sale->quantity,
                    'total_cost' => $cost,
                ];
            });

            return [
                'date' => $date,
                'sales' => $salesData,
                'total_amount' => $totalAmount,
            ];
        })->values();

        if ($type === 'json') {
            return response()->json($reportData, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="отчет по продажам.json"',
            ], JSON_PRETTY_PRINT);
        }

        return Excel::download(
            new SalesReportExport($reportData),
            'отчет по продажам.csv',
            \Maatwebsite\Excel\Excel::CSV,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="отчет по продажам.csv"'
            ]
        );
    }

    private function getCategoryAndChildren($categoryId)
    {
        $categories = Category::where('parent_id', $categoryId)->pluck('id')->toArray();

        foreach ($categories as $childId) {
            $categories = array_merge($categories, $this->getCategoryAndChildren($childId));
        }

        return array_merge([$categoryId], $categories);
    }


}
