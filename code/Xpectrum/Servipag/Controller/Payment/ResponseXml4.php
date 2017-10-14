<?php

namespace Xpectrum\Servipag\Controller\Payment;

class ResponseXml4 extends \Magento\Framework\App\Action\Action
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
    protected $servipagPayment;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,        
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $salesOrder,
        \Xpectrum\Servipag\Model\ServipagPayment $servipagPayment
    ){
        parent::__construct($context);
        
        $this->checkoutSession = $checkoutSession;
        $this->salesOrder = $salesOrder;
        $this->servipagPayment = $servipagPayment;
    }
    
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        
        if (isset($data['xml']) && !empty($data['xml'])) {
            $xml4 = $data['xml'];
        } else {
            $xml4 = false;
        }
        
        $incrementId = $this->checkoutSession->getLastRealOrderId();
        $order = $this->salesOrder->loadByIncrementId($incrementId);
        
        if ($incrementId) {
            if ($xml4) {
                $result = $this->servipagPayment->validateXml4($xml4);
                if (isset($result['codigo']) && $result['codigo'] === 0) {
                    $order->setState('processing')->setStatus('processing'); 
                    $order->save();

                    $this->_redirect('checkout/onepage/success/', array('_secure' => false));
                } else {                
                    $order->cancel()->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Canceled')->save();

                    $this->messageManager->addError('Le informamos que su orden ' . $incrementId . ', realizada el ' . $order->getCreatedAt() . ' termin&oacute; de forma inesperada.');
                    $this->_redirect('checkout/onepage/failure/', array('_secure' => false));
                }
            } else {
                $order->cancel()->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Canceled')->save();

                $this->messageManager->addError('Le informamos que su orden ' . $incrementId . ', realizada el ' . $order->getCreatedAt() . ' termin&oacute; de forma inesperada.');
                $this->_redirect('checkout/onepage/failure/', array('_secure' => false));
            }
        } else {
            $this->messageManager->addError('Hubo un error en el pago. N&uacute;mero de pedido inv&aacute;lido.');
            $this->_redirect('checkout/onepage/failure/', array('_secure' => false));
        }
    }
}