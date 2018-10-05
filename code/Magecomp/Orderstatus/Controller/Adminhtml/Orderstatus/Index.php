<?php
 
namespace Magecomp\Orderstatus\Controller\Adminhtml\Orderstatus;
 
class Index extends \Magento\Backend\App\Action
{
    
    protected $resultPageFactory;
    public function __construct(\Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
	
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magecomp_Orderstatus::orderstatus');
        $resultPage->addBreadcrumb(__('Magecomp'), __('Magecomp'));
        $resultPage->addBreadcrumb(__('Orderstatus'), __('Orderstatus'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Order Statuses'));
        return $resultPage;
    }
	
	protected function _isAllowed()
    {
		return $this->_authorization->isAllowed('Magecomp_Orderstatus::orderstatus');
    }
	
}
