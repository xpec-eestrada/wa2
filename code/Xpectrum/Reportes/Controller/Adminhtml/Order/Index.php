<?php
namespace Xpectrum\Reportes\Controller\Adminhtml\Order;

class Index extends \Magento\Backend\App\Action{

    /**
    * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $resultPageFactory;
    private $_resultPage;

    /**
        * Constructor
        *
        * @param \Magento\Backend\App\Action\Context $context
        * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
        */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
        * Load the page defined in view/adminhtml/layout/exampleadminnewpage_helloworld_index.xml
        *
        * @return \Magento\Framework\View\Result\Page
        */
    public function execute(){
        $this->_setPageData();
        return  $resultPage = $this->getResultPage();
    }
    public function getResultPage(){
        if (is_null($this->_resultPage)) {
            $this->_resultPage = $this->resultPageFactory->create();
        }
        return $this->_resultPage;
    }
    protected function _setPageData(){
        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('Xpectrum_Reportes::xpectrum_reportes_orders');
        $resultPage->getConfig()->getTitle()->prepend((__('Orders')));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Xpectrum'), __('Xpectrum'));
        $resultPage->addBreadcrumb(__('Reportes'), __('Orders'));

        return $this;
    }
}
?>
  