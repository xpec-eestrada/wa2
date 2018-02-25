<?php
namespace Xpectrum\Reportes\Model\Indexer;

use Magento\Framework\Indexer\CacheContext;
class OrderIndex implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    private $cacheContext;
    private $_logger;

    public function __construct(
        \Xpectrum\Reportes\Logger\LoggerAdmin $logger
    ) {
        $this->_logger = $logger;
        
    }
    public function execute($ids){
    }
    public function executeFull(){
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();
        $i=0;
        try {
            $tableindx      = $resource->getTableName('xpec_indx_orders');
            $sql            = 'DELETE FROM '.$tableindx;
            $result         = $connection->query($sql);
            $torder         = $resource->getTableName('sales_order');
            $taddress       = $resource->getTableName('sales_order_address');
            $tpayment       = $resource->getTableName('sales_order_payment');

            $tableindxshipp = $resource->getTableName('xpec_indx_shipping');
            $tentity        = $resource->getTableName('catalog_product_entity');
            $torderitem     = $resource->getTableName('sales_order_item');
            $toptionvalue   = $resource->getTableName('eav_attribute_option_value');
            $tentityint     = $resource->getTableName('catalog_product_entity_int');
            $tattribute     = $resource->getTableName('eav_attribute');
            $sql            = 'TRUNCATE '.$tableindxshipp;
            $result         = $connection->query($sql);
            

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
            INNER JOIN '.$tpayment.' pay ON(pay.parent_id=torder.entity_id)';
            $rsorders = $connection->fetchAll($sql);
            $values='';
            foreach($rsorders as $roworder){
                $product                = $this->getDataProduct($resource,$connection,$roworder['entity_id']);
                $products_shipping      = $this->getDataProductShipping($resource,$connection,$roworder,$tentity,$torderitem,$toptionvalue,$tentityint,$tattribute);

                $shipping_address       = str_replace(array("'"), "\'", $roworder['shipping_address']);
                $billing_address        = str_replace(array("'"), "\'", $roworder['billing_address']);
                $shipping_description   = str_replace(array("'"), "\'", $roworder['shipping_description']);
                $customer_name          = str_replace(array("'"), "\'", $roworder['name']);
                $phone                  = str_replace(array("'"), "\'", $roworder['phone']);
                $payment                = $roworder['additional_information'];
                $objpayment             = unserialize($payment);
                $method                 = str_replace(array("'"), "\'", (isset($objpayment['method_title']))?$objpayment['method_title']:$roworder['method']);
                if(empty($values)){
                    $values="(".$roworder['entity_id'].",'".$roworder['increment_id']."','".$product['sku']."','".$product['qty']."','".$product['name']."','".$phone."','".$roworder['created_at']."',".round($roworder['grand_total']).",'".$roworder['status']."','".$shipping_address."','".$billing_address."','".$shipping_description."','".$roworder['customer_email']."',".round($roworder['shipping_amount']).",'".$customer_name."','".$method."')";
                }else{
                    $values=$values.",(".$roworder['entity_id'].",'".$roworder['increment_id']."','".$product['sku']."','".$product['qty']."','".$product['name']."','".$phone."','".$roworder['created_at']."',".round($roworder['grand_total']).",'".$roworder['status']."','".$shipping_address."','".$billing_address."','".$shipping_description."','".$roworder['customer_email']."',".round($roworder['shipping_amount']).",'".$customer_name."','".$method."')";
                }
                $sql = 'SELECT increment_id FROM '.$tableindxshipp.' WHERE id_order = '.$roworder['entity_id'];
                $rsvaleexit = $connection->fetchAll($sql);
                $swvalidate = false;
                $values = str_replace(array("\r", "\n"), '', $values);
                $sql = "INSERT INTO ".$tableindx."(id_order,increment_id,skus,qty,productnames,phone,created_at,total,status,shipping_address,billing_address,shipping_description,customer_email,shipping_price,customer_name,payment_method) VALUES ".$values;
                foreach($rsvaleexit as $itemval){
                    $swvalidate = true;
                }
                if(!$swvalidate){
                    $connection->query($sql);
                }else{
                    $ar = array(
                        'IdOrder'   => $roworder['entity_id'],
                        'Estado'    => 'Repetida',
                        'Sql'       => $sql
                    );
                    $this->_logger->info( json_encode($ar) );
                }
                foreach($products_shipping as $item){
                    $sql = "INSERT INTO ".$tableindxshipp."(id_order,increment_id,sku,payment,authocode,productname,size,color,qty,shipping_method,price_product_base,price_product_total,discount_percent,price_order_base,price_order_total,created_at,status) 
                            VALUE(".$roworder['entity_id'].",'".$roworder['increment_id']."','".$item['skuparent']."','".$method."','".$roworder['cc_trans_id']."','".$item['name']."','".$item['size']."','".$item['color']."',".$item['qty'].",'".$shipping_description."',".round($item['base_price']).",".round($item['price_inc_tax']).",".round($item['disc_percent']).",".round($roworder['base_subtotal']).",".round($roworder['grand_total']).",'".$roworder['created_at']."','".$roworder['status']."')";
                    $connection->query($sql);
                }
                $values='';
            }
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
    private function getDataProduct($resource,$connection,$idOrder){
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
    private function setDataIndexShipping($resource,$connection,$itemorder){
        $shipping_address       = str_replace(array("'"), "\'", $itemorder['shipping_address']);
        $billing_address        = str_replace(array("'"), "\'", $itemorder['billing_address']);
        $shipping_description   = str_replace(array("'"), "\'", $itemorder['shipping_description']);
        $customer_name          = str_replace(array("'"), "\'", $itemorder['name']);
        $phone                  = str_replace(array("'"), "\'", $itemorder['phone']);
        $payment                = $itemorder['additional_information'];
        $objpayment             = unserialize($payment);
        $method                 = str_replace(array("'"), "\'", (isset($objpayment['method_title']))?$objpayment['method_title']:$itemorder['method']);
        $tproduct = $resource->getTableName('sales_order_item');
        $sql = 'SELECT sku,name,qty_ordered  FROM '.$tproduct.' WHERE order_id='.$idOrder.' AND product_type=\'simple\'';


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
        INNER JOIN '.$torderitem.' parentp ON(parentp.item_id=orit.parent_item_id)
        WHERE orit.product_type=\'simple\' AND orit.order_id='.$idOrder;
        $rsproducts = $connection->fetchAll($sql);
        $data = array();
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
    public function executeList(array $ids){
        
    }
    public function executeRow($id){
        
    }
    protected function getCacheContext(){
        if (!($this->cacheContext instanceof CacheContext)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(CacheContext::class);
        } else {
            return $this->cacheContext;
        }
    }
}