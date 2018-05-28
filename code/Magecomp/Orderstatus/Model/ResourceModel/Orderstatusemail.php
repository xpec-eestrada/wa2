<?php
namespace Magecomp\Orderstatus\Model\ResourceModel;

class Orderstatusemail extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected function _construct()
    {
        $this->_init('orderstatustemplate','orderstatustemplate_id');
    }
}