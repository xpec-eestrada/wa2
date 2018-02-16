<?php
namespace Xpectrum\Reportes\Model\ResourceModel;


class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    /**
     * Date model
     * 
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('xpec_indx_orders', 'id_order');
    }

    /**
     *
     * @param string $id
     * @return string|bool
     */
    public function getOrderById($id){
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'id_order')
            ->where('id_order = :id');
        $binds = ['id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Xpectrum\Reportes\Model\Order $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_beforeSave($object);
    }
}