<?php
namespace Magecomp\Orderstatus\Model\ResourceModel\Orderstatus;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magecomp\Orderstatus\Model\Orderstatus', 'Magecomp\Orderstatus\Model\ResourceModel\Orderstatus');
    }
}