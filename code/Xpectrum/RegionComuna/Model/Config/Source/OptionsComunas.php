<?php
namespace Xpectrum\RegionComuna\Model\Config\Source;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
/**
* Custom Attribute Renderer
*/
class OptionsComunas extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource{
    /**
    * @var OptionFactory
    */
    protected $optionFactory;
    /**
    * @param OptionFactory $optionFactory
    */
    /**
    * Get all options
    *
    * @return array
    */
    public function getAllOptions(){
        /* your Attribute options list*/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('xpec_comunas');
        $sql='SELECT id,nombre
                    FROM 
                        '.$tableName.' 
                    ORDER BY
                        nombre ASC';
        $result = $connection->fetchAll($sql);
        $data=array();
        $data[]=array('label' => 'Seleccione','value' => '');
        foreach($result as $item){
            $data[]=array('label' => $item['nombre'],'value' => $item['id']);
        }
        return $data;
    }
}
