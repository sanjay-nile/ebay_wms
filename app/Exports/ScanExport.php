<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ScanExport implements FromCollection, WithHeadings
{
    private $data;
    private $type;

    public function __construct($data, $type = 'scan'){
        $this->data = $data;
        $this->type = $type;
    }

    public function collection() {
        return collect($this->data);
    }

    public function headings(): array {
        return [
            "ID",
            "Scan IN Date",
            "eBay Order Date",
            "Scan IN User",
            "Assigned Operator",
            "Package ID",
            "eBay ID",
            "Location ID",
            "Location Title",
            "Tracking Number",
            "Scan Out Date",
            "Dispatch Date",
            "Weight",
            "Dims",
            "Reason For Cancellation",
            "Status",
        ];
    }
}
