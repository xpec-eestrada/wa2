<?php
namespace Xpectrum\Reportes\Model\ResourceModel\Envio;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'xpectrum_shipping_collection';
    protected $_eventObject = 'xpec_shipping_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Xpectrum\Reportes\Model\Envio', 'Xpectrum\Reportes\Model\ResourceModel\Envio');
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
    protected function _toOptionArray($valueField = 'id', $labelField = 'id', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}