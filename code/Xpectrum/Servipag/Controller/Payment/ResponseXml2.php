<?php

namespace Xpectrum\Servipag\Controller\Payment;

class ResponseXml2 extends \Magento\Framework\App\Action\Action
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
     * @var \Xpectrum\Servipag\Model\Servipag
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
        $xml3 = '';        
        try {            
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPostValue();
                if (isset($data['XML']) && !empty($data['XML'])) {
                    $xml2 = $data['XML'];
                } else {
                    $xml2 = false;
                }

                if ($xml2) {
                    $result = $this->servipagPayment->validateXml2($xml2);                
                    $xml3 = $this->servipagPayment->setXml3($result['codigo'], $result['mensaje']);
                    $this->servipagPayment->setLogXml3($result['id_servipag'], $xml3);
                }
            } else {
                die('Recurso no accesible');
            }
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }        
        
        echo $xml3;
    }
}