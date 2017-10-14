<?php

namespace Xpectrum\Servipag\Model\ResourceModel;


class Servipag extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('servipag', 'id');
    }
    
    public function loadByIdServipag($idServipag)
    {
        $table = $this->getMainTable();
        $where = $this->getConnection()->quoteInto('id_servipag = ?', $idServipag);
        $sql = $this->getConnection()->select()->from($table, array('id'))->where($where);
        $id = $this->getConnection()->fetchOne($sql);
        return $id;
    }
}