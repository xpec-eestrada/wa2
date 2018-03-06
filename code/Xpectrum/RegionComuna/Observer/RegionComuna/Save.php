<?php
namespace Xpectrum\RegionComuna\Observer\RegionComuna;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Type;

class Save implements ObserverInterface
{
    
    /**
    * @var \Magento\Framework\HTTP\ZendClientFactory
    */
    protected $_httpClientFactory;
    public $scopeConfig;
    protected $eav;
    private $_customer;
    private $countryFactory;
    private $_eventManager;
    private $_logger;

    /**
    * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\Event\Manager $eventManager,
        \Xpectrum\RegionComuna\Logger\Logger $logger,
        \Magento\Directory\Model\CountryFactory $countryFactory){
        $this->_httpClientFactory   = $httpClientFactory;
        $this->countryFactory       = $countryFactory;
        $this->_eventManager        = $eventManager;
        $this->scopeConfig          = $scopeConfig;
        $this->_customer            = $customer;
        $this->_logger              = $logger;
        $this->eav                  = $eavConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        $this->_eventManager->dispatch('region_comuna_save_before');
        $order          = $observer->getEvent()->getOrder();
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();
        $this->saveRegionComuna($order,$resource,$connection);
        $this->_eventManager->dispatch('region_comuna_save_after',['order'=>$order]);
    }
    private function saveRegionComuna($order,$resource,$connection){
        $orderId        = $order->getId();
        $i=0;
        $this->_logger->info('Inicio Proceso');
        try {
            $taddresss          = $resource->getTableName('xpec_order_address_data');
            $objShippingAddress = $order->getShippingAddress();
            $addressId          = $objShippingAddress->getCustomerAddressId();
            $this->_logger->info('IdOrder: '.$orderId.' -- IdAddress: '.$addressId);
            if(isset($addressId) && is_numeric($addressId)){
                $objRegion          = $this->getDataRegion($resource,$connection,$addressId);
                $objComuna          = $this->getDataComuna($resource,$connection,$addressId);
            }
            $sql    = 'SELECT id FROM '.$taddresss.' WHERE id_order = '.$orderId;
            $rssw   = $connection->fetchAll($sql);
            $swnew  = true; 
            foreach($rssw as $item){
                $swnew = false;
            }
            if($swnew){
                if(isset($objComuna['id']) && is_numeric($objComuna['id']) && $objComuna['id']>0){
                    if(isset($addressId) && is_numeric($addressId)){
                        $sql = 'INSERT INTO '.$taddresss.'(id_order,id_region,name_region,id_comuna,name_comuna,type_address) 
                        VALUES('.$orderId.','.$objRegion['id'].',\''.$objRegion['nombre'].'\','.$objComuna['id'].',\''.$objComuna['nombre'].'\',1)';
                        $sql = str_replace(array("\r", "\n"), '', $sql);
                        $connection->query($sql);
                        $this->_logger->info('Data Grabada con Exito.');
                    }else{
                        
                    }
                }else{
                    if(isset($addressId) && is_numeric($addressId)){
                        throw new \Exception("Actualice la dirección y seleccione la comuna.");
                    }else{
                        $region     =  $objShippingAddress->getData("region");
                        $regionId   =  $objShippingAddress->getData("region_id");
                        $comuna     =  $objShippingAddress->getData("city");
                        $arrcom     = $this->getDataComunaByText($resource,$connection,$comuna);
                        $sql        = 'INSERT INTO '.$taddresss.'(id_order,id_region,name_region,id_comuna,name_comuna,type_address) 
                        VALUES('.$orderId.','.$regionId.',\''.$region.'\','.$arrcom['id'].',\''.$comuna.'\',1)';
                        $sql = str_replace(array("\r", "\n"), '', $sql);
                        $connection->query($sql);
                        $this->_logger->info('Data Grabada con Exito(Invitado).');
                    }
                }
            }else{
                $this->_logger->info('Data no grabada porque ya existia.');
            }
        } catch (\Exception $e) {
            $this->_logger->info('Error al grabar region y comuna. '.$e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()),
            $e);
        }
    }
    private function getDataComuna($resource,$connection,$addressId){
        $this->_logger->info('Obteniendo data de Comuna');
        $tcomuna            = $resource->getTableName('xpec_comunas');
        $tselectedcomuna    = $resource->getTableName('customer_address_entity_int');
        $tattribute         = $resource->getTableName('eav_attribute');
        $sql = 'SELECT co.id,nombre 
        FROM '.$tcomuna.' co
        INNER JOIN '.$tselectedcomuna.' sel ON 
            (co.id=sel.value AND sel.attribute_id=(SELECT attribute_id FROM '.$tattribute.' WHERE attribute_code=\'xpec_comuna\'))
        WHERE
            sel.entity_id='.$addressId;
        $sql        = str_replace(array("\r", "\n"), '', $sql);
        $rscomuna   = $connection->fetchAll($sql);
        $data       = array();
        foreach($rscomuna as $row){
            $idComuna   = $row['id'];
            $nameComuna = $row['nombre'];
            $data = array('id' => $idComuna, 'nombre'=>$nameComuna);
        }
        $this->_logger->info('Data de Comuna Obtenida');
        return $data;
    }
    private function getDataRegion($resource,$connection,$addressId){
        $this->_logger->info('Obteniendo data de region');
        $tregion    = $resource->getTableName('customer_address_entity');
        $sql        = 'SELECT region_id,region FROM '.$tregion.' WHERE entity_id = '.$addressId;
        $sql        = str_replace(array("\r", "\n"), '', $sql);
        $rscomuna   = $connection->fetchAll($sql);
        $data       = array();
        foreach($rscomuna as $row){
            $id   = $row['region_id'];
            $nombre = $row['region'];
            $data = array('id' => $id, 'nombre'=>$nombre);
        }
        $this->_logger->info('Data de Region Obtenida');
        return $data;
    }
    private function getDataComunaByText($resource,$connection,$textComuna){
        $this->_logger->info('Obteniendo data de Comuna');
        $tcomuna            = $resource->getTableName('xpec_comunas');
        $sql = "SELECT co.id,nombre FROM ".$tcomuna." co WHERE nombre='".$textComuna."'";
        $sql        = str_replace(array("\r", "\n"), '', $sql);
        $rscomuna   = $connection->fetchAll($sql);
        $data       = array();
        foreach($rscomuna as $row){
            $idComuna   = $row['id'];
            $nameComuna = $row['nombre'];
            $data = array('id' => $idComuna, 'nombre'=>$nameComuna);
        }
        $this->_logger->info('Data de Comuna Obtenida');
        return $data;
    }

}
