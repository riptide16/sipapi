<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TotalAccreditedLibraryCategorySheet implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        $this->data['library_total'][0]->category = 'Total';
        $this->data['libraries']->push($this->data['library_total'][0]);
        return $this->data['libraries'];
    }

    public function headings(): array
    {
        return [
            ['Report Jumlah Perpustakaan Terakreditasi Terkini'],
            [],
            [
                'Jenis Perpustakaan',
                'Jumlah Perpustakaan',
                'Total Terakreditasi',
                'Belum Akreditasi',
            ],
        ];
    }

    public function map($category): array
    {
        return [
            'Perpustakaan '.$category->category,
            (string) $category->total,
            (string) $category->terakreditasi,
            (string) $category->belum_akreditasi,
        ];
    }
}

