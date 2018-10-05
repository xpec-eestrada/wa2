<?php

namespace Magecomp\Orderstatus\Controller\Adminhtml\Orderstatus;

use Magecomp\Orderstatus\Model\OrderstatusFactory;
use Magecomp\Orderstatus\Model\OrderstatusemailFactory;
use Magento\Backend\App\Action\Context;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var StatusFactory
     */
    protected $modelStatusFactory;

    /**
     * @var TemplateFactory
     */
    protected $statusemailFactory;
		
	protected $logger;
	
    public function __construct(Context $context, 
        OrderstatusFactory $modelStatusFactory, 
        OrderstatusemailFactory $statusemailFactory,
		\Psr\Log\LoggerInterface $logger)
    {
		$this->logger = $logger;
        $this->modelStatusFactory = $modelStatusFactory;
        $this->statusemailFactory = $statusemailFactory;

        parent::__construct($context);
    }
	
	protected function _isAllowed()
    {
		return $this->_authorization->isAllowed('Magecomp_Orderstatus::orderstatus');
    }

    public function execute()
    {
		 // check if data sent
        $data = $this->getRequest()->getPostValue();
		
		if($data) 
		{
		  
		  try 
		  {
			  // Save Status Data
			  $id = (int)$this->getRequest()->getParam('id');
			  $model = $this->modelStatusFactory->create()->load($id);
			  
			  if (!$model->getId() && $id) 
			  {
                $this->messageManager->addError(__('This block no longer exists.'));
                return $resultRedirect->setPath('*/*/');
              }
			  
			  $data['order_is_system'] = 0;
			  
			  $data['order_parent_state'] = implode(',',$data['order_parent_state']);
			  $model->setData($data);
			  
			  $storeEmailTemplate = [];
			  if (isset($data['store_template']))
			  {
				  $storeEmailTemplate = $data['store_template'];
				  unset($data['store_template']);
			  }
			  $model->save();
			  
			   // Save Status Email Data
			   $statusemailmodel =  $this->statusemailFactory->create();
			   $removecollection = $statusemailmodel->getCollection()->addFieldToFilter('order_status_id',$model->getId());
			   foreach($removecollection as $currow)
			   {
					   $currow->delete(); 
			   }
			   //$statusemailmodel->removeOrderstatusemail($model->getId());
			   
			   if (!empty($storeEmailTemplate))
				{
					foreach ($storeEmailTemplate as $storeId => $templateId)
					{
						$template = $this->statusemailFactory->create();
						$data = [
							'order_status_id'	    => $model->getId(),
							'order_store_id'		=> $storeId,
							'order_template_id'	=> $templateId
						];
						$template->addData($data)->save();
					}
				}
			   
			  $this->messageManager->addSuccess(__('The status has been saved.'));
			  // check if 'Save and Continue'
              if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $model->getId()]);
              }
			  $this->_redirect('*/*');
			  return;
		  } catch (\Exception $e) {
			  $this->messageManager->addError($e->getMessage());
			  $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
			  return;
		  }
		}
        $this->_redirect('*/*/');
    }
}
