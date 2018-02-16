<?php
namespace Xpectrum\Reportes\Model\ResourceModel\Order;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id_order';
    protected $_eventPrefix = 'xpectrum_order_collection';
    protected $_eventObject = 'order_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Xpectrum\Reportes\Model\Order', 'Xpectrum\Reportes\Model\ResourceModel\Order');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'id', $labelField = 'nombre', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}