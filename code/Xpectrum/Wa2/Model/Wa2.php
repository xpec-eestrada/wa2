<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Xpectrum\Wa2\Model;

use Xpectrum\Wa2\Api\Wa2Interface;

/**
 * Wa2Interface.
 */
class Wa2 implements Wa2Interface{
    private $table_prefix='wa2_d';
    private $loggerxpec;
    /**
     * Return el stock de un sku.
     *
     * @api
     * @param string $sku Es el identificador unico del producto.
     * @return string Obtiene el stock del producto en el inventario del e-commerce.
     */
    public function getStock($sku) {
        $status='';
        $mensaje='';
        $stock=0;
        try{
            $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
            $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
            $result1 = $connection->fetchAll("SELECT FORMAT(qty,0) AS qty FROM ".$this->table_prefix."catalog_product_entity INNER JOIN ".$this->table_prefix."cataloginventory_stock_item ON(product_id=entity_id) WHERE sku='".$sku."'");
            if(count($result1)>0){
                $stock=$result1[0]['qty'];
                $status='successful';
            }else{
                $status='error';
                $mensaje='El sku ingresado no existe';
            }    
        }catch(Exception $err){
            $status='error';
            $mensaje=$err->getMessage();
        }
        $result='{"status":"'.$status.'","mensaje":"'.$mensaje.'","data":{"stock":'.$stock.'}}';
        return $result;
    }
    /**
     * Establece el stock a un sku.
     *
     * @api
     * @param string $sku Es el identificador unico del producto.
     * @param int $stock La cantidad del pruducto en el inventario.
     * @return string Se obtiene una respuesta de la transacción.
     */
    public function setStock($sku,$stock){
        $status='';
        $mensaje='';
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/xpec_stock.log');
        $this->loggerxpec = new \Zend\Log\Logger();
        $this->loggerxpec->addWriter($writer);
        try{
            $objectManager  =   \Magento\Framework\App\ObjectManager::getInstance();
            $connection     = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
            $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
            if($stock>0){
                $this->loggerxpec->info('Sku: '.$sku.'  Stock: '.$stock);
                $result1 = $connection->query("UPDATE ".$this->table_prefix."catalog_product_entity INNER JOIN ".$this->table_prefix."cataloginventory_stock_item ON(product_id=entity_id) SET qty=".$stock.",is_in_stock=1 WHERE sku='".$sku."'");
            }else{
                if($stock==0){
                    $this->loggerxpec->info('Sku: '.$sku.'  Stock: '.$stock);
                    $result1 = $connection->query("UPDATE ".$this->table_prefix."catalog_product_entity INNER JOIN ".$this->table_prefix."cataloginventory_stock_item ON(product_id=entity_id) SET qty=".$stock.",is_in_stock=0 WHERE sku='".$sku."'");
                }else{
                    $this->loggerxpec->info('No se pudo actualizar porque stock es negativo Sku: '.$sku.'  Stock: '.$stock);
                }
            }
            $tproduct  = $resource->getTableName('catalog_product_entity');
            $ids=array();
            
            $status='successful';
            $mensaje='';
        }catch(Exception $err){
            $status='error';
            $mensaje=$err->getMessage();
        }
        return $result='{"status":"'.$status.'","mensaje":"'.$mensaje.'"}';
    }

     /**
     * Actualizar varios Stocks
     *
     * @api
     * @param string $param JSON con la relación de skus y stocks a modificar.
     * @return string Se obtiene una respuesta de la transacción
     */
    public function setStocks($param){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/xpec_stock.log');
        $this->loggerxpec = new \Zend\Log\Logger();
        $this->loggerxpec->addWriter($writer);
        $data=json_decode($param);
        
        $this->loggerxpec->info('Parametros: '.$param);
        $status='';
        $mensaje='';
        if(isset($data->param) && is_array($data->param)){
            try{
                $objectManager  =   \Magento\Framework\App\ObjectManager::getInstance();
                $connection     = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
                $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $i=0;
                $status='successful';
                $arrskus=array();
                foreach($data->param as $obj){
                    if(isset($obj->sku) && isset($obj->stock)){
                        try{
                            if($obj->stock>0){
                                $this->loggerxpec->info('Sku: '.$obj->sku.'  Stock: '.$obj->stock);
                                $result1 = $connection->query("UPDATE ".$this->table_prefix."catalog_product_entity INNER JOIN ".$this->table_prefix."cataloginventory_stock_item ON(product_id=entity_id) SET qty=".$obj->stock.",is_in_stock=1 WHERE sku='".$obj->sku."'");
                            }else{
                                if($obj->stock==0){
                                    $this->loggerxpec->info('Sku: '.$obj->sku.'  Stock: '.$obj->stock);
                                    $result1 = $connection->query("UPDATE ".$this->table_prefix."catalog_product_entity INNER JOIN ".$this->table_prefix."cataloginventory_stock_item ON(product_id=entity_id) SET qty=".$obj->stock.",is_in_stock=0 WHERE sku='".$obj->sku."'");
                                }else{
                                    $this->loggerxpec->info('No se pudo actualizar porque stock es negativo Sku: '.$obj->sku.'  Stock: '.$obj->stock);
                                }
                            }
                            $arrskus[]=$obj->sku;
                        }catch(Exception $err){
                            $mensaje=$mensaje.'El objeto con index {'.$i.'} genero erro en la consulta ('.$err->getMessage().'). ';
                        }
                    }else{
                        $status='error';
                        $mensaje=$mensaje.'El objeto con index {'.$i.'} es invalido. ';
                    }
                    $i++;
                }

                $this->loggerxpec->info('Proceso de inventario finalizado');

                //reindexList
            }catch(Exception $err){
                $status='error';
                $mensaje=$err->getMessage();
                $this->loggerxpec->info('Error: 1'.$mensaje);
            }
        }else{
            $status='error';
            $mensaje='Se esperaba parametro comuniquece con el adminsitrador.';
            $this->loggerxpec->info('Error: 2'.$mensaje);
        }
        $result='{"status":"'.$status.'","mensaje":"'.$mensaje.'"}';
        return $result;
    }
}