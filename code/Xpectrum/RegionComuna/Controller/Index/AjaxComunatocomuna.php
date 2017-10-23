<?php

namespace Xpectrum\RegionComuna\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class AjaxComunatocomuna extends \Magento\Framework\App\Action\Action{

    protected $_resultPageFactory;
    protected $_resource;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_resultPageFactory = $resultJsonFactory;
        $this->_resource = $resource;
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return $this
     */
    public function execute(){
        $post         = $this->getRequest()->getPostValue();
        $connection   = $this->_resource->getConnection();
        $tablecomunas = $this->_resource->getTableName('xpec_comunas');
        $id_comuna     = (isset($post) && isset($post['id_comuna']) && is_numeric($post['id_comuna']))?$post['id_comuna']:0;
        $datajson     = $this->_resultPageFactory->create();
        $data         = array();
        if($id_comuna>0){
            //$sql      = 'SELECT id,nombre FROM '.$tablecomunas.' WHERE idregion='.$idregion.' ORDER BY nombre';

            $sql      = 'SELECT id,nombre
                            FROM '.$tablecomunas.' 
                            WHERE idregion = (SELECT idregion FROM '.$tablecomunas.' WHERE id='.$id_comuna.')';
            $result   = $connection->fetchAll($sql);
            $data[]=array('label'=>'Seleccione Comuna','value'=>'');
            foreach($result as $item){
                $data[]=array('label'=>$item['nombre'],'value'=>$item['id']);
            }
        }else{
            $data[]=array('label'=>'Seleccione Región','value'=>'');
        }
        return $datajson->setData(array('result'=>$data));
    }
}