<?php

namespace WeltPixel\GoogleTagManager\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var \WeltPixel\GoogleTagManager\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;


    /**
     * @param \WeltPixel\GoogleTagManager\Helper\Data $helper     
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     */
    public function __construct
    (
        \WeltPixel\GoogleTagManager\Helper\Data $helper,                                
        \Magento\Checkout\Model\Session $_checkoutSession
    )
    {
        $this->helper = $helper;
        $this->_checkoutSession = $_checkoutSession;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $customer = $observer->getEvent()->getCustomer();        
        $this->_checkoutSession->setUserId($this->helper->setUser($customer->getId()));

        return $this;
    }
}