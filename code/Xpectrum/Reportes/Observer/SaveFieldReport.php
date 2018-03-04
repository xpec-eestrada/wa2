<?php
namespace Xpectrum\Reportes\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Type;

class SaveFieldReport implements ObserverInterface
{
    
    /**
    * @var \Magento\Framework\HTTP\ZendClientFactory
    */
    protected $_httpClientFactory;

    public $scopeConfig;

    protected $eav;

    private $_customer;

    private $countryFactory;

    /**
    * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Directory\Model\CountryFactory $countryFactory){
        $this->scopeConfig = $scopeConfig;
        $this->eav = $eavConfig;
        $this->_httpClientFactory = $httpClientFactory;
        $this->_customer = $customer;
        $this->countryFactory = $countryFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        $order          = $observer->getEvent()->getOrder();
        $orderId        = $order->getId();
        $incrementalId  = $order->getIncrementId();
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();
        $i=0;
        try {
            $fecha = date("Y-m-d H:i:s");
            $tableindx  = $resource->getTableName('xpec_indx_orders');
            $torder     = $resource->getTableName('sales_order');
            $taddress   = $resource->getTableName('sales_order_address');
            $tpayment   = $resource->getTableName('sales_order_payment');

            $tableindxshipp = $resource->getTableName('xpec_indx_shipping');
            $tentity        = $resource->getTableName('catalog_product_entity');
            $torderitem     = $resource->getTableName('sales_order_item');
            $toptionvalue   = $resource->getTableName('eav_attribute_option_value');
            $tentityint     = $resource->getTableName('catalog_product_entity_int');
            $tattribute     = $resource->getTableName('eav_attribute');

            $sql = 'SELECT torder.entity_id,increment_id,created_at,grand_total,torder.status,torder.store_id,base_subtotal,pay.cc_trans_id,
            (SELECT telephone 
                FROM '.$taddress.' 
                WHERE parent_id=torder.entity_id AND address_type=\'shipping\') as phone,
            (SELECT street 
                FROM '.$taddress.' 
                WHERE parent_id=torder.entity_id AND address_type=\'shipping\') as shipping_address,
            (SELECT street 
                FROM '.$taddress.' 
                WHERE parent_id=torder.entity_id AND address_type=\'billing\') as billing_address,
            shipping_description,customer_email,torder.shipping_amount,
            concat(customer_firstname,\' \',customer_lastname) as name,method,additional_information
            FROM '.$torder.' torder 
            LEFT JOIN '.$tpayment.' pay ON(pay.parent_id=torder.entity_id) WHERE torder.entity_id='.$orderId;
            $sql = str_replace(array("\r", "\n"), '', $sql);
            $rsorders = $connection->fetchAll($sql);
            $values='';
            foreach($rsorders as $roworder){
                
                $product                = $this->getDataProduct($order);
                $objShippingAddress     = $order->getShippingAddress();
                $addressId               = $objShippingAddress->getCustomerAddressId();
                $shippingAddressLines   = $objShippingAddress->getStreet();
                $dlvStreet              = isset($shippingAddressLines[0]) ? $shippingAddressLines[0] : "";
                $dlvStreet              .= isset($shippingAddressLines[1]) ? " ".$shippingAddressLines[1] : "";
                $shipping_address       = str_replace(array("'"), "\'", $dlvStreet);
                $shipping_address       = str_replace(array("\r", "\n"), '', $shipping_address);
                
                $objBillingAddress      = $order->getBillingAddress();
                $billingAddressLines    = $objBillingAddress->getStreet();
                $dlvStreet              = isset($billingAddressLines[0]) ? $billingAddressLines[0] : "";
                $dlvStreet              .= isset($billingAddressLines[1]) ? " ".$billingAddressLines[1] : "";
                $billing_address        = str_replace(array("'"), "\'", $dlvStreet);
                $billing_address        = str_replace(array("\r", "\n"), '', $billing_address);
                //$shipping_description   = str_replace(array("'"), "\'", $order->getShippingMethod());
                $shipping_description   = str_replace(array("'"), "\'", $order->getShippingDescription());
                $payment                = $roworder['additional_information'];
                $objpayment             = unserialize($payment);
                $method                 = str_replace(array("'"), "\'", (isset($objpayment['method_title']))?$objpayment['method_title']:$roworder['method']);
                $name = '';
                if( $order->getCustomerFirstname() != null ) {// Customer login
                    $name = trim($order->getCustomerFirstname().' '.$order->getCustomerLastname());
                }else{// Customer not login
                    $name = trim($objShippingAddress->getData("firstname").' '.$objShippingAddress->getData("lastname"));
                }
                $customer_name          = str_replace(array("'"), "\'",$name);

                $payment        = $order->getPayment();
                $objmethod      = $payment->getMethodInstance();
                $methodTitle    = $objmethod->getTitle();
                $methodTitle    = str_replace(array("'"), "\'", $methodTitle);
                $phone          = str_replace(array("'"), "\'", $objShippingAddress->getData("telephone"));
                $values="(".$roworder['entity_id'].",'".$roworder['increment_id']."','".$product['sku']."','".$product['qty']."','".$product['name']."','".$phone."','".$roworder['created_at']."',".round($roworder['grand_total']).",'".$roworder['status']."','".$shipping_address."','".$billing_address."','".$shipping_description."','".$roworder['customer_email']."',".round($roworder['shipping_amount']).",'".$customer_name."','".$methodTitle."')";

                $sql        = 'SELECT skus FROM '.$tableindx.' WHERE id_order='.$orderId;
                $rssearch   = $connection->fetchAll($sql);
                $swaction   = false;
                foreach($rssearch as $rowsearch){
                    $swaction = true;
                }
                if($swaction){
                    $sql = "UPDATE 
                                ".$tableindx." 
                            SET 
                                status = '".$roworder['status']."'
                            WHERE 
                                id_order = ".$orderId;
                    $sql = str_replace(array("\r", "\n"), '', $sql);
                }else{
                    $values = str_replace(array("\r", "\n"), '', $values);
                    $sql = "INSERT INTO ".$tableindx."(id_order,increment_id,skus,qty,productnames,phone,created_at,total,status,shipping_address,billing_address,shipping_description,customer_email,shipping_price,customer_name,payment_method) VALUES ".$values;
                    
                }
                $connection->query($sql);

                $sql        = 'SELECT id FROM '.$tableindxshipp.' WHERE id_order = '.$orderId;
                $rssearch2  = $connection->fetchAll($sql);
                $swaction2 = false;
                foreach($rssearch2 as $rowsearch2){
                    $swaction2 = true;
                }
                if(!$swaction2){
                    $products_shipping      = $this->getDataProductShipping($resource,$connection,$roworder,$tentity,$torderitem,$toptionvalue,$tentityint,$tattribute);
                    foreach($products_shipping as $item){
                        $objComuna = $this->getDataComuna($resource,$connection,$roworder['entity_id']);
                        if(isset($objComuna['id']) && is_numeric($objComuna['id']) && $objComuna['id']>0){
                            $sql2 = "INSERT INTO ".$tableindxshipp."(id_order,increment_id,sku,payment,authocode,productname,size,color,qty,shipping_method,price_product_base,price_product_total,discount_percent,price_order_base,price_order_total,created_at,status,id_comuna,nombre_comuna) 
                                VALUE(".$roworder['entity_id'].",'".$roworder['increment_id']."','".$item['skuparent']."','".$method."','".$roworder['cc_trans_id']."','".$item['name']."','".$item['size']."','".$item['color']."',".$item['qty'].",'".$shipping_description."',".round($item['base_price']).",".round($item['price_inc_tax']).",".round($item['disc_percent']).",".round($roworder['base_subtotal']).",".round($roworder['grand_total']).",'".$roworder['created_at']."','".$roworder['status']."',".$objComuna['id'].",'".$objComuna['nombre']."')";
                            $sql2 = str_replace(array("\r", "\n"), '', $sql2);
                            $connection->query($sql2);
                        }else{
                            error_log("check");
                            throw new \Exception("Debe seleccionar una comuna en su dirección");
                        }
                    }
                }else{
                    $sql2 = "UPDATE 
                                ".$tableindxshipp." 
                            SET 
                                status = '".$roworder['status']."'
                            WHERE 
                                id_order = ".$orderId;
                    $sql2 = str_replace(array("\r", "\n"), '', $sql2);
                    $connection->query($sql2);
                }
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    private function getDataProduct($order){
        $items = $order->getItems();
        $sku = '';
        $name = '';
        $qty = '';
        foreach( $items as $item ){
            if( $item->getProductType() == Type::TYPE_SIMPLE || $item->getProductType() == Type::TYPE_VIRTUAL ){
                $product    = $item->getProduct();
                $qty        = (empty($qty))?(int)$item->getQtyOrdered():$qty.','.(int)$item->getQtyOrdered();
                $sku        = (empty($sku))?$product->getSku():$sku.','.$product->getSku();
                $name       = (empty($name))?$product->getName():$name.','.$product->getName();
            }
        }
        $data       = array('sku'=>$sku,'name'=>$name,'qty'=>$qty);
        return $data;
    }
    public function getDataProductShipping($resource,$connection,$itemorder,$tentity,$torderitem,$toptionvalue,$tentityint,$tattribute){
        $idOrder = $itemorder['entity_id'];
        $storeId = $itemorder['store_id'];
        
        $sql = 'SELECT orit.name,orit.qty_ordered,parentp.base_price,parentp.base_price_incl_tax,parentp.tax_percent,
        (SELECT e.sku FROM '.$tentity.' e 
            INNER JOIN '.$torderitem.' parent ON(parent.product_id=e.entity_id)
            WHERE parent.item_id=orit.parent_item_id) as skuparent,orit.product_id,
        (SELECT tcolor.value  FROM '.$toptionvalue.' tcolor
            INNER JOIN '.$tentityint.' rec ON (tcolor.option_id=rec.value AND (rec.store_id=0 OR rec.store_id='.$storeId.'))
            INNER JOIN '.$tattribute.' attr ON (attr.attribute_code=\'color\' AND attr.attribute_id=rec.attribute_id)
            WHERE rec.entity_id=orit.product_id ORDER BY rec.store_id DESC LIMIT 1
        ) AS color,
        (SELECT ttalla.value  FROM '.$toptionvalue.' ttalla
            INNER JOIN '.$tentityint.' rec ON (ttalla.option_id=rec.value AND (rec.store_id=0 OR rec.store_id='.$storeId.'))
            INNER JOIN '.$tattribute.' attr ON (attr.attribute_code=\'size\' AND attr.attribute_id=rec.attribute_id)
            WHERE rec.entity_id=orit.product_id ORDER BY rec.store_id DESC LIMIT 1
        ) AS talla
        FROM '.$torderitem.' orit 
        LEFT JOIN '.$torderitem.' parentp ON(parentp.item_id=orit.parent_item_id)
        WHERE orit.product_type=\'simple\' AND orit.order_id='.$idOrder;
        $sql        = str_replace(array("\r", "\n"), '', $sql);
        $rsproducts = $connection->fetchAll($sql);
        $data       = array();
        foreach($rsproducts as $row){
            $data[] = array(
                'name'          => $row['name'],
                'qty'           => $row['qty_ordered'],
                'base_price'    => $row['base_price'],
                'price_inc_tax' => $row['base_price_incl_tax'],
                'disc_percent'  => $row['tax_percent'],
                'color'         => $row['color'],
                'size'          => $row['talla'],
                'skuparent'     => $row['skuparent'],
                'product_id'    => $row['product_id']
            );
        }
        return $data;
    }
    private function getDataComuna($resource,$connection,$idOrder,$type=1){
        $tcomuna    = $resource->getTableName('xpec_order_address_data');
        $sql        = 'SELECT id_comuna,name_comuna FROM '.$tcomuna.' WHERE id_order='.$idOrder.' AND type_address='.$type;
        $rsorders   = $connection->fetchAll($sql);
        $data       = array();
        foreach($rsorders as $item){
            $data = array('id'=>$item['id_comuna'],'nombre'=>$item['name_comuna']);
        }
        return $data;
    }
}
