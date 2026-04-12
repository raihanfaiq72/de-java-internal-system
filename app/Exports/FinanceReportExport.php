<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FinanceReportExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;
    protected $account;
    protected $start;
    protected $end;

    public function __construct($data, $account, $start, $end)
    {
        $this->data = $data;
        $this->account = $account;
        $this->start = $start;
        $this->end = $end;
    }

    public function view(): View
    {
        return view('Exports.finance_report', [
            'data' => $this->data,
            'account' => $this->account,
            'start' => $this->start,
            'end' => $this->end
        ]);
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }
}
