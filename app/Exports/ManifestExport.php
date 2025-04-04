<?php

namespace App\Exports;

use App\Models\ReverseLogisticWaybill;
use App\Models\PalletDeatil;
use App\Models\OrderData;
use App\Models\OrderItem;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ManifestExport implements FromCollection, WithHeadings
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
        if ($this->type == 'export_europe') {
            # code...
            return [
                'ParcelID',
                'TrackingRef',
                'Supplier Code',
                'Order No',
                'ShipperName',
                'ShippersVAT',
                'CPC:CustomsBonded',
                'CPC:NonCustomsBonded',
                'CustomerId',
                'CustomerName',
                'Delivery.AddressLine1',
                'Delivery.AddressLine2',
                'Delivery.Postcode',
                'Delivery.CountryCode',
                'Name',
                'Item ref',
                'TotalQty',
                'Country of Origin',
                'Customs Code',
                'ItemWeightKg',
                'ParcelWeightKg',
                'Dimensions',
                'Billing.Currency',
                'Price',
                'CPC',
                'Import Entry Number',
                'Import Entry Date',
                'VAT Paid',
                'Duty Paid'
            ];
        } elseif ($this->type == 'vat_return') {
            # code...
            return [
                'DATE OF  RETURN',
                'PKG NO',
                'Order Number',
                'CUSTOMER NAME',
                'CONSIGNEE ADDRESS',
                'SUPPLIER NAMES',
                'SKU#',
                'ITEM DESCRIPTION',
                'COUNTRY OF ORIGIN',
                'COMMENTS',
                'DATE  RETURNED',
                'CONF ORDER',
                'AWB NUMBER',
                'Pallet NUMBER',
                'RG Reference Number',
                'RG TO COMPLETE ENTRY NO',
                'ENTRY DATE',
                'HSCODE',
                'VALUE',
                'RATE OF EXCHANGE',
                'VALUE  EUR',
                'DUTY RATE',
                'DUTY',
                'VALUE + DUTY',
                'VAT',
                'TOTAL  RECLAIM',
                'TYPE OF VALUE',
                'IMPORT  TARIFF CPC'
            ];
        } elseif ($this->type == 'export_uk') {
            # code...
            return [
                'ParcelID',
                'TrackingRef',
                'ShipperName',
                'ShippersVAT',
                'CPC:CustomsBonded',
                'CPC:NonCustomsBonded',
                'CustomerId',
                'CustomerName',
                'Delivery.AddressLine1',
                'Delivery.AddressLine2',
                'Delivery.Postcode',
                'Delivery.CountryCode',
                'Name',
                'Item ref',
                'TotalQty',
                'Country of Origin',
                'Customs Code',
                'ItemWeightKg',
                'ParcelWeightKg',
                'Dimensions',
                'Billing.Currency',
                'Price',
                'CPC',
                'Export Declaration Number',
                'Export Declaration Date'
            ];
        } elseif ($this->type == 'custom_broker') {
            # code...
            return [
                'DeclarationDate',
                'DeclarationType',
                'AdditionalDeclarationType',
                'CustomsProcedure',
                'AdditionalProcedure',
                'GoodsItemNumber',
                'CountryOriginCode',
                'ConsignorName',
                'ConsignorStreetAndNr',
                'ConsignorCity',
                'ConsignorPostcode',
                'ConsignorCountry',
                'ConsigneeName',
                'ConsigneeStreetAndNr',
                'ConsigneePostcode',
                'ConsigneeCity',
                'ConsigneeCountry',
                'ConsigneeID',
                'INCOTerm',
                'InvoiceCurrency',
                'ItemPrice_Amount',
                'UNLOcode',
                'NetMassKg',
                'GrossMassKg',
                'CommodityCodeCombinedNomenclatureCode',
                'DescriptionGoods',
                'TypePackage',
                'NumberPackages',
                'LRN',
                'TrackingNumber',
                'UseAverageCustomsValue',
                'UniqueIDNumber',
                'ContainerIDNumber',
                'SellerItemReference',
                'InternetHyperTextLinkItem',
                'EmailConsignee',
                'IDMotherPackage',
                'ConsigneeStatus',
                'MethodPayment',
                'PostalMarking'
            ];
        } elseif ($this->type == 'import_uk') {
            # code...
            return [
                'Manifest Date',
                'Pallet ID',
                'MAWB Number',
                'HAWB#',
                'Manifest #',
                'Flight Date',
                'Flight Number',
                'Return Import Entry Number',
                'Return Import Entry Date',
                'Export Declaration Number',
                'Export Declaration Date',
                'Exchange Rate',
                'Line Number',
                'Order Number',
                'Product Code',
                'SKU',
                'HS Code',
                'Dest Cntry',
                'Disp Cntry',
                'Orig Cntry',
                'Goods Description',
                'Commodity Code',
                'Item Gross Mass',
                'CPC',
                'Item Net Mass',
                'Quantity',
                'Value',
                'Currency',
                'Selling Price',
                'Item Third Quantity',
                'Item Stat Val.',
                'UN Dangerous Goods Code',
                'Packages Number',
                'Packages Kind',
                'Packages Marks and Numbers',
                'Document Code',
                'Document Reference',
                'Document Status',
                'Consignor ID',
                'Consignor Name',
                'Consignor Street',
                'Consignor City',
                'Consignor Postcode',
                'Consignor Country',
                'Consignee ID',
                'Consignee Name',
                'Consignee Street',
                'Consignee City',
                'Consignee Postcode',
                'Consignee Country',
                'AI Statement Code',
                'AI Statement Text',
                'AI Statement Code 2',
                'AI Statement Text 2',
                'AI Statement Code 3',
                'AI Statement Text 3',
                'AI Statement Code 4',
                'AI Statement Text 4',
                'AI Statement Code 5',
                'AI Statement Text 5',
                'Prev Doc Class 1',
                'Prev Doc Type 1',
                'Prev Doc Reference 1',
                'Prev Doc Class 2',
                'Prev Doc Type 2',
                'Prev Doc Reference 2',
                'Prev Doc Class 3',
                'Prev Doc Type 3',
                'Prev Doc Reference 3',
                'Commodity Add Code',
                'Serial no.',
                'Purchase Order',
                'Invoice Number',
                'Customer Defined 1',
                'Customer Defined 2',
                'Document Code 2',
                'Document Reference 2',
                'Document Status 2',
                'Document Code 3',
                'Document Reference 3',
                'Document Status 3',
                'Document Code 4',
                'Document Reference 4',
                'Document Status 4',
                'Document Code 5',
                'Document Reference 5',
                'Document Status 5'
            ];
        }
    }
}
