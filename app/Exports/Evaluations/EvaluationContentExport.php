<?php

namespace App\Exports\Evaluations;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EvaluationContentExport implements WithMultipleSheets
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new NilaiAkhirExport($this->data),
            new OnthespotExport($this->data),
            new RekomendasiExport($this->data),
            new BeritaAcaraExport($this->data),
        ];
    }
}
