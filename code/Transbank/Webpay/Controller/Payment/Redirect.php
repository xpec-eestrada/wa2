<?php

namespace Transbank\Webpay\Controller\Payment;

class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,         
        \Magento\Framework\View\Result\PageFactory $resultPageFactory        
    ){
        parent::__construct($context);
        
        $this->resultPageFactory = $resultPageFactory;
    }
    
    public function execute()
    {        
        $resultPage = $this->resultPageFactory->create();    	
    	return $resultPage;        
    }
}