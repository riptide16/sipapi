<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TotalAccreditationExportCategorySheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Jenis Perpustakaan';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $perCat = [];
        foreach ($this->data['per_categories'] as $cat => $total) {
            $perCat[] = [
                'category' => $cat,
                'total' => $total,
            ];
        }
        $perCat[] = [
            'category' => 'Total',
            'total' => $this->data['per_categories_total'],
        ];

        return collect($perCat);
    }

    public function headings(): array
    {
        return [
            ['Report Jumlah Akreditasi'],
            [],
            [
                'Jenis Perpustakaan',
                'Total Akreditasi',
            ],
        ];
    }

    public function map($category): array
    {
        return [
            $category['category'],
            (string) $category['total'],
        ];
    }
}

