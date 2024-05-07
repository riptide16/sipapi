<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalAccreditedLibraryByProvincePerYearExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $this->data['year_totals'][0]->name = 'Total';
        $this->data['year_data']->push($this->data['year_totals'][0]);

        return $this->data['year_data'];;
    }

    public function headings(): array
    {
        return [
            ['DATA AKREDITASI BERDASARKAN PROVINSI DAN JENIS PERPUSTAKAAN PER TAHUN'],
            [],
            ['Tahun', $this->data['year']],
            [],
            ['Provinsi', 'Jumlah Perpustakaan', 'Total Terakreditasi', 'Belum Akreditasi'],
        ];
    }

    public function map($category): array
    {
        return [
            $category->name,
            (string) $category->total,
            (string) $category->terakreditasi,
            (string) $category->belum_akreditasi,
        ];
    }
}
