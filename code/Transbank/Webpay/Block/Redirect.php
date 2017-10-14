<?php
namespace Transbank\Webpay\Block;

class Redirect extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Transbank\Webpay\Model\Webpay
     */
    protected $webpay;
    private $_checkoutSession;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Transbank\Webpay\Model\Webpay $webpay,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ){        
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->webpay = $webpay;
    }
    
    public function getPaidConnect()
    {
        return $this->webpay->getPaidConnect();        
    }
    public function getRealOrderId()
    {
        return $this->_checkoutSession->getLastRealOrderId();
    }
}