<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecomp\Orderstatus\Controller\Adminhtml\Orderstatus;

use Magecomp\Orderstatus\Model\OrderstatusFactory;
use Magecomp\Orderstatus\Model\OrderstatusemailFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
	protected $modelStatusFactory;
	protected $statusemailFactory;
	
	
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
        OrderstatusFactory $modelStatusFactory, 
        OrderstatusemailFactory $statusemailFactory
    ) 
	{
		$this->modelStatusFactory = $modelStatusFactory;
        $this->statusemailFactory = $statusemailFactory;
        parent::__construct($context);
       
    }
	
	protected function _isAllowed()
    {
		return $this->_authorization->isAllowed('Magecomp_Orderstatus::orderstatus');
    }
	
	
	/**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
		$id = $this->getRequest()->getParam('id');
        if ($id) 
		{
            try 
			{
				// Delete Template From Database 
				$statusemailmodel =  $this->statusemailFactory->create();
				$removecollection = $statusemailmodel->getCollection()->addFieldToFilter('order_status_id',$id);
				foreach($removecollection as $currow)
			    {
					   $currow->delete(); 
			    }
				
				// Delete Status
                $model = $this->modelStatusFactory->create();
                $model->load($id);
                $model->delete();
				
                $this->messageManager->addSuccess(__('You deleted the status sucessfully'));
                return $resultRedirect->setPath('*/*/');
            } 
			catch (\Exception $e) 
			{
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a status to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
	
	 
    
}
