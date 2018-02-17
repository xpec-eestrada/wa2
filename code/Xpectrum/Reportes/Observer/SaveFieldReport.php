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

            $sql = 'SELECT torder.entity_id,increment_id,created_at,grand_total,torder.status,
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
            concat(customer_firstname,\' \',customer_lastname) as name,method
            FROM '.$torder.' torder 
            LEFT JOIN '.$tpayment.' pay ON(pay.parent_id=torder.entity_id) WHERE torder.entity_id='.$orderId;
            $sql = str_replace(array("\r", "\n"), '', $sql);
            $rsorders = $connection->fetchAll($sql);
            $values='';
            foreach($rsorders as $roworder){
                //$product = $this->getDataProduct($resource,$connection,$roworder['entity_id']);
                $product = $this->getDataProduct($order);
                $objShippingAddress     = $order->getShippingAddress();
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
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    private function getDataProduct2($resource,$connection,$idOrder){
        $tproduct = $resource->getTableName('sales_order_item');
        $sql = 'SELECT sku,name,qty_ordered  FROM '.$tproduct.' WHERE order_id='.$idOrder.' AND product_type=\'simple\'';
        $sku = '';
        $name = '';
        $qty = '';
        $rsproducts = $connection->fetchAll($sql);
        foreach($rsproducts as $row){
            $sku = (empty($sku))?$row['sku']:$sku.','.$row['sku'];
            $name = (empty($name))?$row['name']:$name.','.$row['name'];
            $tmp = explode('.',$row['qty_ordered']);
            $qty = (empty($qty))?$tmp[0]:$qty.','.$tmp[0];
        }
        $data = array('sku'=>$sku,'name'=>$name,'qty'=>$qty);
        return $data;
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



}
