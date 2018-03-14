<?php
namespace Magecomp\Orderstatus\Model\ResourceModel;

class Orderstatus extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
    {
        $this->_init('orderstatus','orderstatus_id');
    }
}