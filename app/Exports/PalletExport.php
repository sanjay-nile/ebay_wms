<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PalletExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $data;
    private $type;

    public function __construct($data, $type){
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
        if ($this->type == 'Shipped') {
            return [
                'S no.',
                'EVTN Number',
                'Reference No',
                'Order No',
                'SKU Bar code',
                'Supplier code',
                'Pallet ID',
                'From Warehouse',
                'To Warehouse',
                'Shipper Name',
                'MAWB #',
                'HAWB #',
                'Master Tracking ID',
                'Custom Duty',
                'Taxes',
                'Pallet Type',
                'Parcel Tracking id',
                'Title',
                'Price',
                'Country Of Origin',
                'Hs Code',
                'Package Count',
                'Length(In)',
                'Width(In)',
                'Height(In)',
                'Weight(Kg)',
                'Charged Weight(Kg)',
                'Shipment Status',
                'Delivery Date',
                'Delivery Time',
                'Delivery Signature',
                'Category',
                'Sub Category'
            ];
        } else {
            return [
                'S no.',
                'Pallet ID',
                'From Warehouse',
                'To Warehouse',
                'Pallet Type',
                'EVTN Number',
                'Reference No',
                'Order No',
                'SKU Bar code',
                'Supplier code',
                'Parcel Tracking id',
                'Title',
                'Price',
                'Country Of Origin',
                'Hs Code',
                'Package Count',
                'Length(In)',
                'Width(In)',
                'Height(In)',
                'Weight(Kg)',
                'Charged Weight(Kg)',
                'Shipment Status',
                'Category',
                'Sub Category'
            ];
        }        
    }
}
