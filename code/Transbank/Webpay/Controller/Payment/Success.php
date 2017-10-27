<?php

namespace Transbank\Webpay\Controller\Payment;

class Success extends \Magento\Framework\App\Action\Action
{   
            
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $salesOrder;
    
    /**
     * @var \Transbank\Webpay\Model\Webpay
     */
    protected $webpay;    

    public function __construct(
        \Magento\Framework\App\Action\Context $context,        
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $salesOrder,
        \Transbank\Webpay\Model\Webpay $webpay
    ){
        parent::__construct($context);        
        
        $this->checkoutSession = $checkoutSession;
        $this->salesOrder = $salesOrder;
        $this->webpay = $webpay;
    }
    
    public function execute()
    {        
        $paidFlag = $this->checkoutSession->getPaidFlag();
        $result = $this->checkoutSession->getHasPaidResult();
        $incrementId = trim($this->checkoutSession->getLastRealOrderId());
        $order = $this->salesOrder->loadByIncrementId($incrementId);
        
        if (!isset($paidFlag) || $paidFlag == '' || !isset($result['buyOrder']) || 
                (isset($result['buyOrder']) && trim($result['buyOrder']) != $incrementId)) {

            $order->cancel()->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Canceled')->save();
            
            $this->_redirect('checkout/onepage/failure/', array('_secure' => false));
        } else {
            
            //$order->setState('processing')->setStatus('processing'); 
            //$order->save();
            //$this->checkoutSession->unsPaidFlag();
            $this->_redirect('checkout/onepage/success/', array('_secure' => false));
        }        
    }
}
