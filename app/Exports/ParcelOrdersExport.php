<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParcelOrdersExport implements FromCollection, WithHeadings
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
        if ($this->type == 'parcel-excel') {
            return [
                'Date_Received_in_Warehouse',
                'Order_Status',
                'Ref_Number',
                'EVTN_Number',
                'Package Weight (LBS)',
                'Customer_Name',
                'Tracking_Number',
                'Customer_Address',
                'Customer_City',
                'Customer_State',
                'Customer_PinCode',
                'Price',
                'Invoiced',
                'Date_Invoiced',
                'Invoice_Number',
                'BoxTop_ref_number',
                'Pallet_ID',
            ];
        } elseif ($this->type == 'item-excel') {
            return [
                'Date_Received_in_Warehouse',
                'Item_Status',
                'Order_Ref_Number',
                'Item_Ref_Number',
                'EVTN_Number',
                'Package Weight (LBS)',
                'eBay_Order_ID',
                'Customer_Name',
                'Tracking_Number',
                'Original_Sales_Incoterm',
                'Customer_Address',
                'Customer_City',
                'Customer_State',
                'Customer_PinCode',
                'Item_Unit_Price',
                'Item_Unit_Currency',
                'Item_Sku',
                'Item_ID',
                'Hs_Code',
                'COO',
                'SC_Main_Category',
                'Category_Tier_1',
                'Level',
                'Received_Condition',
                'Listing_Condition',
                'Description',
                'Expected_Qty',
                'Received_Qty',
                'Original_eBay_Listing_Qty',
                'Unsized_Packages',
                'Empty_Box',
                'Pallet_ID',
                'Pallet_Type',
                'Pallet_Close_Date',
                'Pallet_Received_Condition',
                'Discrepancy_Date_In',
                'Discrepancy_Date_Out',
                'Invoiced',
                'Date_Invoiced',
                'Invoice_Number',
                'BoxTop_ref_number',
                'Total_Cost',
                'Reason_Of_Return',
                'Size',
                'Color',
                'eBay Image',
                'eBay Comment',
                'Sent To Scheduled',
                'Inspectors Pictures',
                'Inspectors Comment'
            ];
        } else {
            return [
                'Date_Received_in_Warehouse',
                'Pallet_ID',
                'Discrepancy_Date_In',
                'Discrepancy_Date_Out',
                'Discrepancy_Status',
                'Order_Ref_Number',
                'EVTN_Number',
                'Customer_Name',
                'eBay_Order_ID',
                'Tracking_Number',
                'Customer_Address',
                'Customer_City',
                'Customer_State',
                'Customer_PinCode',
                'Item_Unit_Price',
                'Item_Unit_Currency',
                'Hs_Code',
                'COO',
                'SC_Main_Category',
                'Category_Tier_1',
                'Level',
                'Inspection_Status',
                'Remark',
                'Comment'
            ];
        }        
    }
}
