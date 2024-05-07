<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalAccreditationByYearDetailExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        if (!empty($this->data)) {
            $this->data[count($this->data) - 1]->category = 'Total';
        }
        return $this->data;
    }

    public function headings(): array
    {
        $heading = [];
        if (!empty($this->data)) {
            foreach ($this->data[0] as $col => $val) {
                $column = $col == 'category' ? 'jenis perpustakaan' : $col;
                $heading[] = ucwords($column);
            }
        }
        return [
            ['Report Jumlah Akreditasi Berdasarkan Jenis Perpustakaan Dalam Tahun'],
            [],
            $heading,
        ];
    }

    public function map($category): array
    {
        $row = [];
        foreach ($category as $val) {
            $row[] = (string) $val;
        }

        return $row;
    }
}
