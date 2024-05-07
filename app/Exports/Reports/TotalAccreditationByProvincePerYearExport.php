<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalAccreditationByProvincePerYearExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $this->data['per_provinces_total'][0]->name = 'Total';
        $this->data['per_provinces']->push($this->data['per_provinces_total'][0]);

        return $this->data['per_provinces'];;
    }

    public function headings(): array
    {
        return [
            ['DATA AKREDITASI BERDASARKAN PROVINSI DAN JENIS PERPUSTAKAAN PER TAHUN'],
            [],
            ['Tahun', $this->data['year']],
            [],
            ['Provinsi', 'A', 'B', 'C', 'Total Akreditasi'],
        ];
    }

    public function map($category): array
    {
        return [
            $category->name,
            $category->A,
            $category->B,
            $category->C,
            (string) $category->total,
        ];
    }
}
