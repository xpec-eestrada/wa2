<?php 
namespace Xpectrum\Reportes\Model;
class Envio extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG         = 'xpec_shipping';
    protected $_cacheTag    = 'xpec_shipping';
    protected $_eventPrefix = 'xpec_shipping';

    protected function _construct(){
        $this->_init('Xpectrum\Reportes\Model\ResourceModel\Envio');
    }
    public function getIdentities(){
        return [self::CACHE_TAG . '_' . $this->getIdEnvio()];
    }
    public function getDefaultValues(){
        $values = [];
        return $values;
    }

}