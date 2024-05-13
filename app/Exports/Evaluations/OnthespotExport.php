<?php

namespace App\Exports\Evaluations;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OnthespotExport implements FromView
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('templates.on-the-spot', [
            'data' => $this->data
        ]);
    }
}
