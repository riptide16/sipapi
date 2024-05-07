<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class TotalAccreditationExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new TotalAccreditationExportCategorySheet($this->data),
            new TotalAccreditationExportProvinceSheet($this->data),
        ];
    }
}
