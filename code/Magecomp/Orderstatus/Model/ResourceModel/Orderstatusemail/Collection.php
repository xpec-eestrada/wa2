<?php
namespace Magecomp\Orderstatus\Model\ResourceModel\Orderstatusemail;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magecomp\Orderstatus\Model\Orderstatusemail', 'Magecomp\Orderstatus\Model\ResourceModel\Orderstatusemail');
    }
}