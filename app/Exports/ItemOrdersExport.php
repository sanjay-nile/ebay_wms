<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemOrdersExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $data;
    private $type;

    public function __construct($data, $type = null){
        $this->data = $data;
        $this->type = $type;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        return collect($this->data);
    }

    public function headings(): array{
        if ($this->type == 'ebay-report') {
            return [
                'Order Ref Number',
                'Item Reference Number',
                'EVTN Number',
                'Date Received in Warehouse',
                'Discrepancy Date In',
                'Discrepancy Date Out',
                'Pallet ID',
                'Pallet Date Creation',
                'Pallet Close Date',
            ];
        } elseif ($this->type == 'pending-item') {
            return [
                'Order Ref Number',
                'Item Reference Number',
                'EVTN Number',
                'Error',
                'Item SKu',
                'Title',
                'Condition',
                'Date Received in Warehouse',
                'Pallet ID',
                'Pallet Type'
            ];
        } elseif ($this->type == 'schedule-item') {
            return [
                'Order Ref Number',
                'Item Reference Number',
                'EVTN Number',
                'Item SKu',
                'Title',
                'Condition',
                'Date Received in Warehouse',
                'Pallet ID',
                'Pallet Type'
            ];
        } else {
            return [
                'Label No / Tracking No',
                'EVTN Number',
                'Order Ref Number',
                'Item Reference Number',
                'Item Inspection Status',
                'Check In Date',
                'Check In Time',
                'Local Check In Date & Time',
                'Check In Username',
                'Inspection Level',
                'Inspection Date',
                'AM / PM',
                'Inspection Time',
                'Local Inspection Date & Time',
                'Inspection Username',
                'Question 1',
                'Expected Qty',
                'Received Qty',
                'Question 2',
                'Question 2 No',
                'Question 3',
                'Question 3 Yes',
                'Question 4',
                'Question 5',
                'Question 5 comments',
                'Pallet ID',
                'Pallet Date In',
                'Pallet Close Date',
                'Pallet Time In',
                'Local Pallet Date In & Time',
                'Pallet Username',
                'Local Time'
            ];
        }
    }
}
