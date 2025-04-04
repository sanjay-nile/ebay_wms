<?php
namespace App\Helpers;

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;
use App\Models\OrderData;
use App\Models\OrderItem;
use App\Models\ReverseLogisticWaybill;
use App\Models\PackageDetail;
use App\Models\OrderCarrierData;
use App\Models\OrderCarrierStatus;

use Illuminate\Support\Facades\Log;
use DB;
use Config;

class ProcessMessageHelper
{
    protected $queueUrl;
    protected $qa_queueUrl;
    protected $statusUrl;
    protected $rtnStatusUrl;
    protected $client;
    protected $carrierUrl;

    // protected $region = Config('constants.sqsRegion');
    protected $key = '';
    protected $secret = '';

    /**
    *
    * Load default setting parameter
    */
    public function __construct()
    {
        $con_dt = Config('constants.liveSQS');

        # order receive url...
        /*$this->queueUrl = "https://sqs.eu-west-1.amazonaws.com/789244868863/mgsap-prod-piapp-p-RP-SalesOrder";
        $this->statusUrl = "https://sqs.eu-west-1.amazonaws.com/789244868863/mgsap-prod-piapp-p-RP-OrderStatus";
        $this->rtnStatusUrl = "https://sqs.eu-west-1.amazonaws.com/789244868863/mgsap-prod-piapp-p-RP-ReturnOrder";*/

        $this->queueUrl = $con_dt['SalesOrderUrl'];
        $this->statusUrl = $con_dt['OrderStatusUrl'];
        $this->rtnStatusUrl = $con_dt['ReturnOrderUrl'];
        $this->carrierUrl = $con_dt['CarrierMgmtUrl'];

        # create a client Credentials...
        $this->client  = new SqsClient([
            'region' => Config('constants.sqsRegion'),
            'version' => 'latest',
            'credentials' => array(
                'key' => $con_dt['Accesskey'],
                'secret' => $con_dt['Secretkey'],
            )
        ]);
    }

    /**
    *
    * Recive orders from the sqs
    */
    public function handle(){
        $processSales = $processStatus = true;
        $limit = 40;
        $count = 0;

        $str = '#################### Order Process Start ################################';
        Log::channel('sqs_orders')->info($str);

        while ($processSales == true && $count < $limit) {
            # code...
            $processSales = $this->processMessages();            
            if ($processSales) {
                # code...
                $count++;
            }
        }

        if ($count == 0) {
            # code...
            Log::channel('sqs_orders')->info('No Sales order process available');
        } else{
            Log::channel('sqs_orders')->info('Sales order process count ='.$count.' and max message ='.$count * 10);
        }
        
        $str = '#################### Order Status Process Start #########################';
        Log::channel('sqs_orders')->info($str);

        $count = 0;
        while ($processStatus == true && $count < $limit) {
            # code...
            $processStatus = $this->orderStatus();
            if ($processStatus) {
                # code...
                $count++;
            }
        }

        if ($count == 0) {
            # code...
            Log::channel('sqs_orders')->info('No Sales order status available');
        } else{
            Log::channel('sqs_orders')->info('Sales order status count ='.$count.' and max message ='.$count * 10);
        }
    }

    public function processMessages(){
        $msg_available = true;
        try {
            $result = $this->client->receiveMessage(array(
                'AttributeNames'        => ['All'],
                'MaxNumberOfMessages'   => 10,
                'MessageAttributeNames' => ['All'],
                'QueueUrl'              => $this->queueUrl, // REQUIRED
                // 'WaitTimeSeconds'       => 10,
            ));
            
            if ($result->get('Messages') != null && count($result->get('Messages')) > 0) {
                foreach( $result->get('Messages') as $message ){
                    $xml = htmlentities($message['Body']);  
                    # load xml in to array...                  
                    $order = $this->loadXmlString($xml);
                    if (is_array($order) && isset($order['SalesOrderMessage'])) {
                        # create order and order item...
                        $sales_order = $this->storeOrderData($order);
                        if ($sales_order) {
                            # code...
                            $result = $this->client->deleteMessage([
                                'QueueUrl' => $this->queueUrl,
                                'ReceiptHandle' => $message['ReceiptHandle']
                            ]);

                            $salesOrder = $order['SalesOrderMessage']['tns1_SalesOrder'];
                            $order_id = $salesOrder['tns1_OrderId'];
                            
                            // $str = 'SQS Order Successfully with: '.$message['MessageId'];
                            $str = 'SQS Order Successfully with: '.$order_id;
                            // Log::channel('sqslog')->debug($str);
                            Log::channel('sqslog')->info($str);
                        } else {
                            $str = 'SQS Order not saved.';
                            Log::channel('sqs_orders')->info($str);
                        }
                    }
                }
            } else {
                $msg_available = false;
            }

        } catch (AwsException $e) {
            $str = $e->getMessage();
            Log::channel('sqs_orders')->info($str);
        }

        return $msg_available;
    }

    /**
    *
    * Single xml load to array
    */
    public function loadXmlString($xmlstring){
        try {
            libxml_use_internal_errors(true);
            $xmlNode = simplexml_load_string(html_entity_decode($xmlstring), "SimpleXMLElement", LIBXML_NOCDATA);
            if ($xmlNode === false) {
            	/*foreach(libxml_get_errors() as $error) {
            	    echo "\t", $error->message;
            	}*/

                $str = '"Failed loading XML.';
                Log::channel('sqs_orders')->info($str);
                return "Failed loading XML\n";                
            }

            $arrayData = $this->xmlToArray($xmlNode);
            $json = json_encode($arrayData);
            $order = json_decode($json, true);

            return $order;
        } catch (AwsException $e) {
            return $e->getMessage();
        }        
    }

    /**
    *
    * Xml to Array parse
    */
    public function xmlToArray($xml, $options = array()) {
        $defaults = array(
            'namespaceSeparator' => '_',//you may want this to be something other than a colon
            'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(),   //array of xml tag names which should always become arrays
            'autoArray' => true,        //only create arrays for tags which appear more than once
            'textContent' => '$',       //key used for the text content of elements
            'autoText' => true,         //skip textContent key if node has no attributes or child nodes
            'keySearch' => false,       //optional search and replace on tag and attribute names
            'keyReplace' => false       //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace
     
        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                        str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                        . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                        . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }
     
        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = $this->xmlToArray($childXml, $options);
                // dd($childArray);
                // list($childTagName, $childProperties) = $childArray;

                foreach ($childArray as $key => $item) {
                    $childTagName = $key;
                    $childProperties = $item;
                }
     
                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                        str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
     
                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                            in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }
     
        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;
     
        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
                ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
     
        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }

    /**
    *
    * order data and order items
    */
    public function storeOrderData($order){
        DB::beginTransaction();
        try {
            if (is_array($order) && isset($order['SalesOrderMessage'])) {
                # code...                
                if (is_array($order['SalesOrderMessage']['tns1_SalesOrder']) && isset($order['SalesOrderMessage']['tns1_SalesOrder'])) {
                    $salesOrder = $order['SalesOrderMessage']['tns1_SalesOrder'];
                    
                    # update or create order data....
                    $order_data = OrderData::updateOrCreate(array('order_id' => $salesOrder['tns1_OrderId']));
                    $order_data->order_type = $salesOrder['tns1_OrderType'];
                    $order_data->doc_type = $salesOrder['tns1_DocType'];
                    $order_data->order_taken_date = $salesOrder['tns1_OrderTakenDate'];
                    $order_data->company = $salesOrder['tns1_Company'];
                    $order_data->sales_org = $salesOrder['tns1_SalesOrg'];
                    $order_data->site = $salesOrder['tns1_Site'];
                    $order_data->channel = $salesOrder['tns1_Channel'];
                    $order_data->local = $salesOrder['tns1_Locale'];

                    if (isset($salesOrder['tns1_CustomerDetails']) && is_array($salesOrder['tns1_CustomerDetails'])) {
                        $order_data->customer_id = $this->checkStringOrNot($salesOrder['tns1_CustomerDetails']['tns1_CustomerId']);
                        $order_data->customer_name = $this->checkStringOrNot($salesOrder['tns1_CustomerDetails']['tns1_CustomerName']);
                        $order_data->dob = $this->checkStringOrNot($salesOrder['tns1_CustomerDetails']['tns1_DOB']);
                        $order_data->customer_email = $this->checkStringOrNot($salesOrder['tns1_CustomerDetails']['tns1_ContactDetails']['tns1_ContactEmail']);
                        $order_data->contact_name = $this->checkStringOrNot($salesOrder['tns1_CustomerDetails']['tns1_ContactDetails']['tns1_ContactName']);
                        $order_data->contact_phone = $this->checkStringOrNot($salesOrder['tns1_CustomerDetails']['tns1_ContactDetails']['tns1_ContactPhone']);
                    }
                    
                    $slsorderDetail = $salesOrder['tns1_SalesOrderDetails'];
                    if (isset($slsorderDetail['tns1_SalesOrderTotals']) && is_array($slsorderDetail['tns1_SalesOrderTotals'])) {
                        $orderDetail = $slsorderDetail['tns1_SalesOrderTotals'];
                        $order_data->sales_order_total = $this->checkStringOrNot($orderDetail['tns1_Total']);
                        $order_data->tax_amount = $this->checkStringOrNot($orderDetail['tns1_TaxAmount']);
                        $order_data->shipping_amount = $this->checkStringOrNot($orderDetail['tns1_ShippingAmount']);
                        $order_data->discount_amount = $this->checkStringOrNot($orderDetail['tns1_DiscountAmount']);
                        // $order_data->discount_type = $orderDetail['tns1_CustomerDetails']['tns1_CustomerId'];
                    }

                    if (isset($slsorderDetail['tns1_PaymentDetails']) && is_array($slsorderDetail['tns1_PaymentDetails'])) {
                        $payment = $slsorderDetail['tns1_PaymentDetails'];
                        $order_data->total = $payment['tns1_TotalAmount'];
                        $order_data->total_amount = $payment['tns1_TotalAmount'];
                        $order_data->transaction_type = $this->checkStringOrNot($payment['tns1_TransactionType']);
                        $order_data->tender_type = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_TenderType']);
                        $order_data->aquirer = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_Aquirer']);
                        $order_data->amount = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_Amount']);
                        $order_data->tender_currency = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_TenderCurrency']);
                        $order_data->transaction_id = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_TransactionId']);
                        $order_data->transaction_date = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_TransactionDate']);
                        $order_data->card_type = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_CardType']);
                        
                        if(isset($payment['tns1_TenderDetails']['tns1_BillingAddress'])){
                            // $order_data->billing_title = $payment['tns1_TenderDetails']['tns1_BillingAddress'];
                            // $order_data->billing_company = $payment['tns1_TenderDetails']['tns1_BillingAddress'];
                            // $order_data->billing_town = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_Town']);
                            // $order_data->billing_country = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_County']);

                            $order_data->billing_address2 = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_AddressLine2']);
                            $order_data->billing_name = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_Name']);
                            $order_data->billing_address1 = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_AddressLine1']);
                            $order_data->billing_town = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_County']);
                            $order_data->billing_country = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_CountryCode']);
                            $order_data->billing_country_code = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_CountryCode']);
                            $order_data->billing_postcode = $this->checkStringOrNot($payment['tns1_TenderDetails']['tns1_BillingAddress']['tns1_Postcode']);
                        }
                    }

                    $order_data->billing_currency = $salesOrder['tns1_Currency'];
                    $order_data->billing_local_cuurency = $salesOrder['tns1_LocaleCurrency'];

                    if (isset($salesOrder['tns1_DeliveryDetails']) && is_array($salesOrder['tns1_SalesOrderDetails'])) {
                        // $order_data->delivery_title = $salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress'];
                        // $order_data->delivery_company = $salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress'];
                        // $order_data->delivery_town = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_Town']);
                        // $order_data->delivery_country = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_County']);

                        $order_data->delivery_address2 = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_AddressLine2']);
                        $order_data->package_type = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_PackageType']);
                        $order_data->delivery_name = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_Name']);
                        $order_data->delivery_address1 = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_AddressLine1']);
                        $order_data->delivery_town = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_County']);
                        $order_data->delivery_country = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_CountryCode']);
                        $order_data->delivery_postcode = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_Postcode']);
                        $order_data->delivery_country_code = $this->checkStringOrNot($salesOrder['tns1_DeliveryDetails']['tns1_DeliveryAddress']['tns1_CountryCode']);
                    }

                    if(!$order_data->save()){
                        $str = 'Inserting sales order error.';
                        Log::channel('sqs_orders')->info($str);
                        throw new \Exception($str);
                    }

                    # order item save...
                    if (isset($slsorderDetail['tns1_SaleOrderItems']) && is_array($slsorderDetail['tns1_SaleOrderItems'])) {
                        $itemDetail = $slsorderDetail['tns1_SaleOrderItems']['tns1_Item'];
                        $itemArr = [];
                        if(isset($itemDetail['tns1_SKU']) && isset($itemDetail['tns1_ItemId'])){
                            $itemArr[] = $itemDetail;
                        } else {
                            $itemArr = $itemDetail;
                        }
                        
                        foreach ($itemArr as $key => $value) {                            
                            # code...
                            if (isset($value['tns1_SKU']) && $value['tns1_SKU'] == '10081188') {
                                # code...
                                continue;
                            }                            
                            if (isset($value['tns1_SKU']) && isset($value['tns1_ItemId'])) {
                                # code...
                                $sku = $value['tns1_SKU'];
                                $item_id = $value['tns1_ItemId'];
                                $order_item = OrderItem::updateOrCreate(array('order_data_id' => $order_data->id, 'item_id' => $item_id, 'sku' => $sku));

                                // $order_item->shipping_amount = $value['tns1_SalesItemTotal']['tns1_ShippingAmount'];
                                // $order_item->discount_type = $value['tns1_SalesItemTotal']['tns1_DiscountType'];
                                $order_item->name = $this->checkStringOrNot($value['tns1_Name']);
                                $order_item->order_id = $this->checkStringOrNot($salesOrder['tns1_OrderId']);
                                $order_item->price = $this->checkStringOrNot($value['tns1_Price']);
                                $order_item->size = $this->checkStringOrNot($value['tns1_Size']);
                                $order_item->tax_amount = $this->checkStringOrNot($value['tns1_SalesItemTotal']['tns1_TaxAmount']);
                                $order_item->discount_amount = $this->checkStringOrNot($value['tns1_SalesItemTotal']['tns1_DiscountAmount']);
                                $order_item->total = $this->checkStringOrNot($value['tns1_SalesItemTotal']['tns1_Total']);
                                $order_item->ordered_qty = $this->checkStringOrNot($value['tns1_SalesItemTotal']['tns1_TotalQty']);

                                $item_property = $value['tns1_ItemProperties'];
                                $item_property_arr = [];
                                $hs_code = $country_of_origin = '';

                                if(isset($item_property['tns2_property']['tns2_name']) && $item_property['tns2_property']['tns2_name'] == 'HarmonisedCode'){
                                    if (isset($item_property['tns2_property']['tns2_value'])) {
                                        # code...
                                        $hs_code = $this->checkStringOrNot($item_property['tns2_property']['tns2_value']);
                                    }
                                } else {
                                    foreach ($item_property['tns2_property'] as $key => $item) {
                                        # code...
                                        if (isset($item['tns2_name']) && $item['tns2_name'] == 'HarmonisedCode') {
                                            # code...
                                            if (isset($item['tns2_value'])) {
                                                # code...
                                                $hs_code = $this->checkStringOrNot($item['tns2_value']);
                                            }                                            
                                        }

                                        if (isset($item['tns2_name']) && $item['tns2_name'] == 'CountryOfOrigin') {
                                            # code...
                                            if (isset($item['tns2_value'])) {
                                                # code...
                                                $country_of_origin = $this->checkStringOrNot($item['tns2_value']);
                                            }                                            
                                        }
                                    }
                                }

                                // $hs_code = $this->checkStringOrNot($value['tns1_ItemProperties']['tns2_property']['tns2_value']);
                                $order_item->hs_code = $this->checkStringOrNot($hs_code);
                                $order_item->country_of_origin = $this->checkStringOrNot($country_of_origin);

                                if(!$order_item->save()){
                                    $str = 'Inserting sales order item error.';
                                    Log::channel('sqs_orders')->info($str);
                                    throw new \Exception($str);
                                }
                            } else {
                                $str = 'Missing sku or line item id.';
                                Log::channel('sqs_orders')->info($str);
                                throw new \Exception($str);
                            }
                        }
                    }

                    DB::commit();
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            DB::rollback();
            $str = "Exception Processing Order. Statement Rolledback Reason:" .$e->getMessage();
            Log::channel('sqs_orders')->info($str);
            return false;
        }        
    }

    public function checkStringOrNot($key){
        if (is_array($key)) {
            # code...
            return null;
        }

        return $key;
    }

    /**
    * Read Order Status xml
    *
    */
    public function orderStatus(){
        $msg_available = true;
        try {
            $result = $this->client->receiveMessage(array(
                'AttributeNames'        => ['All'],
                'MaxNumberOfMessages'   => 10,
                'MessageAttributeNames' => ['All'],
                'QueueUrl'              => $this->statusUrl, // REQUIRED
                // 'WaitTimeSeconds'       => 10,
            ));            

            if ($result->get('Messages') != null && count($result->get('Messages')) > 0) {
                foreach( $result->get('Messages') as $message ){
                    DB::beginTransaction();
                    $order_status = $this->parse_xml_into_array(html_entity_decode($message['Body']));
                    try {
                        $this->processOrderStatus($order_status, $message);
                        DB::commit();
                    } catch (\Exception $e) {
                        $str = 'Order Status:- '.$e->getMessage();
                        Log::channel('sqs_orders')->info($str);
                        DB::rollback();
                    }                    
                }
            } else {
                // $str = 'No Sales Order Status messages in queue';
                // Log::channel('sqs_orders')->info($str);
                $msg_available = false;
            }
        } catch (AwsException $e) {
            $str = 'Order Status:- '.$e->getMessage();
            Log::channel('sqs_orders')->info($str);
        }

        return $msg_available;
    }

    /**
    * Read Order Status xml
    *
    */
    public function processOrderStatus($order_status, $message){
        if (isset($order_status['ns2_OrderStatusEvent']) && is_array($order_status['ns2_OrderStatusEvent'])) {
            # code...
            $status = $order_status['ns2_OrderStatusEvent']['ns2_RecordType'];
            $orderId = $order_status['ns2_OrderStatusEvent']['ns2_OrderId'];

            if (isset($order_status['ns2_OrderStatusEvent']['ns2_OrderStatusEventLine'])) {
                # code...
                $orderEvent = $order_status['ns2_OrderStatusEvent']['ns2_OrderStatusEventLine'];
                if (isset($orderEvent['ns2_StockReference'])) {
                    # code...
                    $sku = $orderEvent['ns2_StockReference']['ns2_SKU'];
                    $confirmQty = $orderEvent['ns2_QtyConfirmed'];
                    $order_item = OrderItem::where(array('order_id' => $orderId, 'sku' => $sku))->first();
                    if ($order_item) {
                        # code...
                        $up = OrderItem::where('order_id', $orderId)->where('sku', $sku)->update(['confirm_qty' => $confirmQty, 'order_status' => $status]);
                        if($up){
                            $result = $this->client->deleteMessage([
                                'QueueUrl' => $this->statusUrl,
                                'ReceiptHandle' => $message['ReceiptHandle']
                            ]);

                            // $str = 'SQS Order Status Successfully with: '.$message['MessageId'];
                            $str = 'SQS Order Status Successfully with: '.$orderId;
                            Log::channel('sqslog')->info($str);
                            // Log::channel('sqs_orders')->info($str);
                        } else {
                            $str = 'Sales order status data not saved Successfully with order id:-'.$orderId.' or confirm qty:- '.$confirmQty.' and order qty:-'.$order_item->ordered_qty;
                            Log::channel('sqs_orders')->info($str);
                            throw new \Exception($str);
                        }
                    } else {
                        $str = 'No sales order status item in database with order id:-'.$orderId.' and item sku:-'.$sku;
                        Log::channel('sqs_orders')->info($str);
                    }
                } else {
                    if (is_array($orderEvent) && count($orderEvent) > 0) {
                        # code...
                        foreach ($orderEvent as $key => $ordr) {
                            # code...
                            $sku = $ordr['ns2_StockReference']['ns2_SKU'];
                            $confirmQty = $ordr['ns2_QtyConfirmed'];
                            $order_item = OrderItem::where(array('order_id' => $orderId, 'sku' => $sku))->first();
                            if ($order_item) {
                                # code...
                                $up = OrderItem::where('order_id', $orderId)->where('sku', $sku)->update(['confirm_qty' => $confirmQty, 'order_status' => $status]);
                                if($up){
                                    $result = $this->client->deleteMessage([
                                        'QueueUrl' => $this->statusUrl,
                                        'ReceiptHandle' => $message['ReceiptHandle']
                                    ]);

                                    $str = 'SQS Order Status Successfully with: '.$orderId;
                                    // $str = 'SQS Order Status Successfully with: '.$message['MessageId'];
                                    Log::channel('sqslog')->info($str);
                                    // Log::channel('sqs_orders')->info($str);
                                } else {
                                    $str = 'Sales order status data not saved Successfully with order id:-'.$orderId.' or confirm qty:- '.$confirmQty.' and order qty:-'.$order_item->ordered_qty;
                                    Log::channel('sqs_orders')->info($str);
                                    throw new \Exception($str);
                                }
                            } else {
                                $str = 'No sales order status item in database with order id:-'.$orderId.' and item sku:-'.$sku;
                                Log::channel('sqs_orders')->info($str);
                            }
                        }
                    } else {
                        $str = 'Sales Order Status:- No order event in the OrderStatusEventLine';
                        Log::channel('sqs_orders')->info($str);
                    }
                }
            } else{
                $str = 'Sales Order Status:- No order status event in the message';
                Log::channel('sqs_orders')->info($str);
            }
        } else {
            $str = 'Order Status:- No Data in queue.';
            Log::channel('sqs_orders')->info($str);
        }
    }

    /**
    * Single order status xml load to array
    */
    public function parse_xml_into_array($xml_string, $options = array()) {
        /*
        DESCRIPTION:
        - parse an XML string into an array
        INPUT:
        - $xml_string
        - $options : associative array with any of these keys:
            - 'flatten_cdata' : set to true to flatten CDATA elements
            - 'use_objects' : set to true to parse into objects instead of associative arrays
            - 'convert_booleans' : set to true to cast string values 'true' and 'false' into booleans
        OUTPUT:
        - associative array
        */

        // Remove namespaces by replacing ":" with "_"
        if (preg_match_all("|</([\\w\\-]+):([\\w\\-]+)>|", $xml_string, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $xml_string = str_replace('<'. $match[1] .':'. $match[2], '<'. $match[1] .'_'. $match[2], $xml_string);
                $xml_string = str_replace('</'. $match[1] .':'. $match[2], '</'. $match[1] .'_'. $match[2], $xml_string);
            }
        }

        $output = json_decode(json_encode(@simplexml_load_string($xml_string, 'SimpleXMLElement', ($options['flatten_cdata'] ? LIBXML_NOCDATA : 0))), (isset($options['use_objects']) ? false : true));

        // Cast string values "true" and "false" to booleans
        if (isset($options['convert_booleans'])) {
            $bool = function(&$item, $key) {
                if (in_array($item, array('true', 'TRUE', 'True'), true)) {
                    $item = true;
                } elseif (in_array($item, array('false', 'FALSE', 'False'), true)) {
                    $item = false;
                }
            };
            array_walk_recursive($output, $bool);
        }

        return $output;
    }

    /**
    * Order Return Status
    */
    public function returnOrderStaus($id = null){
        $ordr_no = ReverseLogisticWaybill::where(['way_bill_number' => $id])->first();
        if (!empty($ordr_no)) {
            $date = date('Y-m-d',strtotime($ordr_no->created_at)).'T'.date('H:i:s',strtotime($ordr_no->created_at));
            $xml = '<?xml version="1.0" encoding="UTF-8"?><ns3:SalesOrderMessage xmlns:ns3="http://www.missguided.com/services/sell/message/orderstatus/v1"><ns1:Context xmlns:ns1="http://www.missguided.com/services/message/v1"></ns1:Context><ns4:OrderStatusEvent xmlns:ns4="http://www.missguided.com/sell/orderstatus/v1">';

            $xml .= '<ns4:RecordType>RET</ns4:RecordType>';
            $xml .= '<ns4:OrderId>'.$ordr_no->way_bill_number.'</ns4:OrderId>';
            foreach ($ordr_no->packages as $key => $package) {
                $item = OrderItem::select('ordered_qty')->where(['order_id' => $id, 'sku' => $package->bar_code])->first();
                $qty = $package->package_count; 
                if (isset($item->ordered_qty)) {
                    # code...
                    $qty = $item->ordered_qty;
                }
                $xml .= '<ns4:OrderStatusEventLine>';
                    $xml .= '<ns4:StockReference>';
                        $xml .= '<ns4:SKU>'.$package->bar_code.'</ns4:SKU>';
                        $xml .= '<ns4:AltSKU>'.$package->bar_code.'</ns4:AltSKU>';
                    $xml .= '</ns4:StockReference>';
                    $xml .= '<ns4:QtyOrdered>'.$qty.'</ns4:QtyOrdered>';
                    $xml .= '<ns4:QtyConfirmed>'.$package->package_count.'</ns4:QtyConfirmed>';
                    $xml .= '<ns4:ReturnReason>'.$package->return_reason.'</ns4:ReturnReason>';
                $xml .= '</ns4:OrderStatusEventLine>';
            }            
            $xml .= '<ns4:EventDate>'.$date.'</ns4:EventDate>';
            $xml .= '</ns4:OrderStatusEvent>';
            $xml .= '</ns3:SalesOrderMessage>';
            $params = [
                'DelaySeconds' => 10,                
                'MessageBody' => $xml,
                'QueueUrl' => $this->rtnStatusUrl
            ];

            try {
                $result = $this->client->sendMessage($params);
                // echo '<pre>';
                // print_r($result);
                $str = '#'.$id.' Return Order Status:- Successfully return order in queue.';
                Log::channel('sqs_orders')->info($str);
            } catch (AwsException $e) {
                // output error message if fails
                // dd($e->getMessage());
                $str = '#'.$id.' Return Order Status:- '.$e->getMessage();
                Log::channel('sqs_orders')->info($str);
            }
        }        
    }

    /**
    * Carrier Message process
    */
    public function carrierHandle(){
        $carrierMessage = true;
        $limit = 100;
        $count = 0;

        $str = '#################### Carrier Messages Process Start ################################';
        Log::channel('carrier_orders')->info($str);

        while ($carrierMessage == true && $count < $limit) {
            # code...
            $carrierMessage = $this->carrierMessages();
            if ($carrierMessage) {
                # code...
                $count++;
            }
        }

        if ($count == 0) {
            # code...
            Log::channel('carrier_orders')->info('No Carrier Messages available');
        } else{
            Log::channel('carrier_orders')->info('Carrier Messages process count ='.$count);
        }
    }

    /**
    * Carrier Management Message
    */
    public function carrierMessages(){
        $msg_available = true;
        DB::beginTransaction();
        try {
            $result = $this->client->receiveMessage(array(
                'AttributeNames'        => ['All'],
                'MaxNumberOfMessages'   => 10,
                'MessageAttributeNames' => ['All'],
                'QueueUrl'              => $this->carrierUrl,
            ));

            if ($result->get('Messages') != null && count($result->get('Messages')) > 0) {
                foreach( $result->get('Messages') as $message ){
                    $body = $message['Body'];
                    $json_obj = json_decode($body);
                    // dd($json_obj);
                    if (!empty($json_obj)) {
                        # code...
                        // $rtn_order = ReverseLogisticWaybill::where('way_bill_number', $json_obj->order_ref)->where('status', 'Success')->get();
                        $rtn_order = OrderData::where('order_id', $json_obj->order_ref)->get();
                        if ($rtn_order->count() > 0) {
                            # code...
                            $carrier_order_data = OrderCarrierData::updateOrCreate(array('order_ref' => $json_obj->order_ref, 'parcel_code' =>$json_obj->parcel_code));
                            $carrier_order_data->system_correlation_code = $json_obj->system_correlation_code;
                            $carrier_order_data->custom1 = $json_obj->custom1;
                            $carrier_order_data->carrier_consignment_code = $json_obj->carrier_consignment_code;
                            $carrier_order_data->carrier_code = $json_obj->carrier_code;
                            $carrier_order_data->postcode = $json_obj->postcode;
                            $carrier_order_data->transaction_type_id = $json_obj->transaction_type_id;
                            $carrier_order_data->destination_country_code = $json_obj->destination_country_code;
                            $carrier_order_data->system_code = $json_obj->system_code;
                            $carrier_order_data->retailer_id = $json_obj->retailer_id;
                            $carrier_order_data->retailer_key = $json_obj->retailer_key;
                            $carrier_order_data->metapack_carrier_service_code = $json_obj->metapack_carrier_service_code;

                            if(!$carrier_order_data->save()){
                                $str = 'Error :- Inserting carrier order data error.';
                                Log::channel('carrier_orders')->info($str);
                                throw new \Exception($str);
                            }

                            $carrier_status = OrderCarrierStatus::updateOrCreate(array('parcel_code' => $json_obj->parcel_code, 'parcel_status_id' => $json_obj->parcel_status_id));
                            $carrier_status->parcel_status_name = $json_obj->parcel_status_name;
                            $carrier_status->carrier_status_code = $json_obj->carrier_status_code;
                            $carrier_status->carrier_reason_code = $json_obj->carrier_reason_code;
                            $carrier_status->con_matching_code = $json_obj->con_matching_code;
                            $carrier_status->achieved_datetime = $json_obj->achieved_datetime;
                            $carrier_status->matched_datetime = $json_obj->matched_datetime;
                            $carrier_status->notified_by_source_datetime = $json_obj->notified_by_source_datetime;
                            $carrier_status->processed_datetime = $json_obj->processed_datetime;
                            $carrier_status->achieved_timezone = $json_obj->achieved_timezone;
                            $carrier_status->trackable_item_completed_date = $json_obj->trackable_item_completed_date;

                            if(!$carrier_status->save()){
                                $str = 'Error:- Inserting carrier order status error.';
                                Log::channel('carrier_orders')->info($str);
                                throw new \Exception($str);
                            }

                            DB::commit();
                            $msg_available = true;

                            $result = $this->client->deleteMessage([
                                'QueueUrl' => $this->carrierUrl,
                                'ReceiptHandle' => $message['ReceiptHandle']
                            ]);
                        } else {
                            $str = 'Carrier Order data not saved with order ref:- '.$json_obj->order_ref;
                            Log::channel('carrier_orders')->info($str);
                            
                            $result = $this->client->deleteMessage([
                                'QueueUrl' => $this->carrierUrl,
                                'ReceiptHandle' => $message['ReceiptHandle']
                            ]);
                        }                        
                    } else {
                        $str = 'Carrier Order data is empty';
                        Log::channel('carrier_orders')->info($str);
                    }
                }
            } else {
                $msg_available = false;
            }

        } catch (\Exception $e) {
            DB::rollback();
            $str = "Exception Processing Carrier Order. Statement Rolledback Reason:" .$e->getMessage();
            Log::channel('carrier_orders')->info($str);
            $msg_available = false;
        }

        return $msg_available;
    }
}
