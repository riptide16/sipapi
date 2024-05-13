<?php

namespace App\Exports\Evaluations;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NilaiAkhirExport implements FromView
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('templates.nilai-akhir', [
            'data' => $this->data
        ]);
    }
}
