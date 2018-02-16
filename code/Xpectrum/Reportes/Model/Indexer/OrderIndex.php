<?php
namespace Xpectrum\Reportes\Model\Indexer;

use Magento\Framework\Indexer\CacheContext;
class OrderIndex implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    private $cacheContext;

    public function __construct(
        
    ) {
        
        
    }
    public function execute($ids){
    }
    public function executeFull(){
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();
        $i=0;
        try {
            $fecha = date("Y-m-d H:i:s");
            $tableindx = $resource->getTableName('xpec_indx_orders');
            $sql       = 'DELETE FROM '.$tableindx;
            $result    = $connection->query($sql);

            $torder = $resource->getTableName('sales_order');
            $taddress = $resource->getTableName('sales_order_address');

            $sql = 'SELECT torder.entity_id,increment_id,created_at,grand_total,torder.status,
            (SELECT street 
                FROM '.$taddress.' 
                WHERE parent_id=torder.entity_id AND address_type=\'shipping\') as shipping_address,
            (SELECT street 
                FROM '.$taddress.' 
                WHERE parent_id=torder.entity_id AND address_type=\'billing\') as billing_address,
            shipping_description,customer_email,torder.shipping_amount,
            concat(customer_firstname,\' \',customer_lastname) as name,method
            FROM '.$torder.' torder 
            INNER JOIN wa2_dsales_order_payment pay ON(pay.parent_id=torder.entity_id);';
            $rsorders = $connection->fetchAll($sql);
            $values='';
            foreach($rsorders as $roworder){
                $product = $this->getDataProduct($resource,$connection,$roworder['entity_id']);
                if(empty($values)){
                    $values="(".$roworder['entity_id'].",'".$roworder['increment_id']."','".$product['sku']."','".$product['qty']."','".$product['name']."','phone','".$roworder['created_at']."',".round($roworder['grand_total']).",'".$roworder['status']."','".$roworder['shipping_address']."','".$roworder['billing_address']."','".$roworder['shipping_description']."','".$roworder['customer_email']."',".round($roworder['shipping_amount']).",'".$roworder['name']."','".$roworder['method']."')";
                }else{
                    $values=$values.",(".$roworder['entity_id'].",'".$roworder['increment_id']."','".$product['sku']."','".$product['qty']."','".$product['name']."','phone','".$roworder['created_at']."',".round($roworder['grand_total']).",'".$roworder['status']."','".$roworder['shipping_address']."','".$roworder['billing_address']."','".$roworder['shipping_description']."','".$roworder['customer_email']."',".round($roworder['shipping_amount']).",'".$roworder['name']."','".$roworder['method']."')";
                }
                $sql = "INSERT INTO ".$tableindx."(id_order,increment_id,skus,qty,productnames,phone,created_at,total,status,shipping_address,billing_address,shipping_description,customer_email,shipping_price,customer_name,payment_method) VALUES ".$values;
                $connection->query($sql);
                $values='';
            }
            //$sql = "INSERT INTO ".$tableindx."(id_order,increment_id,skus,qty,productnames,phone,created_at,total,status,shipping_address,billing_address,shipping_description,customer_email,shipping_price,customer_name,payment_method) VALUES ".$values;
            //$connection->query($sql);

            
        } catch (\Exception $e) {
            error_log($e->getMessage());
            //$this->logger->info(date("Y-m-d H:i:s")." - Error : ".$e->getMessage());
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