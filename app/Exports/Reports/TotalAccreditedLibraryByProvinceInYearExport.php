<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalAccreditedLibraryByProvinceInYearExport implements FromCollection, WithHeadings, WithMapping
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
        $this->data['province_totals'][0]->name = 'Total';
        $this->data['provinces']->push($this->data['province_totals'][0]);

        return $this->data['provinces'];;
    }

    public function headings(): array
    {
        $headers = [
            'Provinsi',
        ];
        foreach ($this->data['years'] as $year) {
            $headers[] = $year;
        }

        return [
            ['JUMLAH AKREDITASI BERDASARKAN PROVINSI DALAM TAHUN'],
            [],
            $headers,
        ];
    }

    public function map($category): array
    {
        $map = [$category->name];
        foreach ($this->data['years'] as $year) {
            $map[] = $category->$year;
        }

        return $map;
    }
}
