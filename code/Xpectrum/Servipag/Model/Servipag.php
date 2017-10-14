<?php 

namespace Xpectrum\Servipag\Model;

class Servipag extends \Magento\Framework\Model\AbstractModel 
    implements ServipagInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'xpectrum_servipag_servipag';

    protected $_cacheTag = 'xpectrum_servipag_servipag';

    protected $_eventPrefix = 'xpectrum_servipag_servipag';

    protected function _construct()
    {
        $this->_init('Xpectrum\Servipag\Model\ResourceModel\Servipag');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
    
    public function loadByIdServipag($idSevipag)
    {
        if (!$idSevipag) {
            $idSevipag = $this->getIdServipag();
        }
        $id = $this->getResource()->loadByIdServipag($idSevipag);
        return $this->load($id);
    }
}