<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RiskOpportunityPdfExport implements FromView
{
    protected $formattedData;

    public function __construct($formattedData)
    {
        $this->formattedData = $formattedData;
    }

    public function view(): View
    {
        return view('exports.pdf', [
            'formattedData' => $this->formattedData,
        ]);
    }
}
