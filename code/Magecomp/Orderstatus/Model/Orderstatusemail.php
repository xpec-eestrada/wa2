<?php
namespace Magecomp\Orderstatus\Model;

class Orderstatusemail extends \Magento\Framework\Model\AbstractModel implements OrderstatusemailInterface, \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'orderstatusemail';
	
    protected function _construct()
    {
        $this->_init('Magecomp\Orderstatus\Model\ResourceModel\Orderstatusemail');
    }
 
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}