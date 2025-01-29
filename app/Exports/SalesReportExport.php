<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class SalesReportExport implements FromCollection, WithHeadings, WithTitle, WithCustomCsvSettings
{

    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->reportData)->flatMap(function ($day) {
            return collect($day['sales'])->map(function ($sale) use ($day) {
                return [
                    'date' => $day['date'],
                    'product' => $sale['product'],
                    'price' => number_format($sale['price'], 2),
                    'quantity' => $sale['quantity'],
                    'total_cost' => number_format($sale['total_cost'], 2),
                ];
            });
        });
    }

    public function headings(): array
    {
        return ['Дата', 'Товар', 'Цена', 'Количество', 'Общая стоимость'];
    }

    public function title(): string
    {
        return 'Отчет о продажах';
    }
    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
            'output_encoding' => 'UTF-8',
            'enclosure' => '"',
            'delimiter' => ";",
            'line_ending' => PHP_EOL,
        ];
    }
}
