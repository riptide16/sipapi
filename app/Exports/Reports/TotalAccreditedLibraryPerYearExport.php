<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalAccreditedLibraryPerYearExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $this->data['year_totals'][0]->category = 'Total';
        $this->data['year_data']->push($this->data['year_totals'][0]);

        return $this->data['year_data'];;
    }

    public function headings(): array
    {
        return [
            ['JUMLAH AKREDITASI BERDASARKAN JENIS PERPUSTAKAAN'],
            [],
            ['Tahun', $this->data['year']],
            [],
            ['Jenis Perpustakaan', 'Jumlah Perpustakaan', 'Total Terakreditasi', 'Belum Akreditasi'],
        ];
    }

    public function map($category): array
    {
        return [
            $category->category,
            (string) $category->total,
            (string) $category->terakreditasi,
            (string) $category->belum_akreditasi,
        ];
    }
}
