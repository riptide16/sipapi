<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalAccreditedLibraryWithinYearExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $this->data['totals'][0]->category = 'Total';
        $this->data['year_data']->push($this->data['totals'][0]);

        return $this->data['year_data'];;
    }

    public function headings(): array
    {
        $headers = [
            'Jenis Perpustakaan',
        ];
        foreach ($this->data['years'] as $year) {
            $headers[] = $year;
        }

        return [
            ['Report Jumlah Perpustakaan Terakreditasi Berdasarkan Jenis Perpustakaan Pertahun'],
            [],
            $headers,
        ];
    }

    public function map($category): array
    {
        $map = [$category->category];
        foreach ($this->data['years'] as $year) {
            $map[] = (string) $category->$year;
        }

        return $map;
    }
}
