<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TotalAccreditedLibraryProvinceSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        $this->data['province_total'][0]->category = 'Total';
        $this->data['provinces']->push($this->data['province_total'][0]);
        return $this->data['provinces'];
    }

    public function headings(): array
    {
        return [
            ['Report Jumlah Perpustakaan Terakreditasi Terkini'],
            [],
            [
                'Provinsi',
                'Jumlah Perpustakaan',
                'Total Terakreditasi',
                'Belum Akreditasi',
            ],
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

