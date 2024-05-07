<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TotalAccreditationExportProvinceSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Provinsi';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->data['per_provinces'][] = (object) [
            'name' => 'Total',
            'total' => $this->data['per_provinces_total'],
        ];

        return collect($this->data['per_provinces']);
    }

    public function headings(): array
    {
        return [
            ['Report Jumlah Akreditasi'],
            [],
            [
                'Provinsi',
                'Total Akreditasi',
            ],
        ];
    }

    public function map($category): array
    {
        return [
            $category->name,
            (string) $category->total,
        ];
    }
}

