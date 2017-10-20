<?php

namespace Xpectrum\RegionComuna\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class AjaxComuna extends \Magento\Framework\App\Action\Action{

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
        $idregion     = (isset($post) && isset($post['id_region']) && is_numeric($post['id_region']))?$post['id_region']:0;
        $datajson     = $this->_resultPageFactory->create();
        $data         = array();
        if($idregion>0){
            $sql      = 'SELECT id,nombre FROM '.$tablecomunas.' WHERE idregion='.$idregion.' ORDER BY nombre';
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