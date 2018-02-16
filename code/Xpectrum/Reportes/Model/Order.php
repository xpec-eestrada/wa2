<?php 
namespace Xpectrum\Reportes\Model;
class Order extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG         = 'xpec_order';
    protected $_cacheTag    = 'xpec_order';
    protected $_eventPrefix = 'xpec_order';

    protected function _construct(){
        $this->_init('Xpectrum\Reportes\Model\ResourceModel\Order');
    }
    public function getIdentities(){
        return [self::CACHE_TAG . '_' . $this->getIdOrder()];
    }
    public function getDefaultValues(){
        $values = [];
        return $values;
    }

}