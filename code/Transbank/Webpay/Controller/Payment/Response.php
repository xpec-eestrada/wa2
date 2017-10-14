<?php

namespace Transbank\Webpay\Controller\Payment;

class Response extends \Magento\Framework\App\Action\Action
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
        if ($this->getRequest()->isPost()) {
            require_once(__DIR__ . '/../../lib/libwebpay/webpay-soap.php');
            
            $data = $this->getRequest()->getPostValue();
            if (isset($data['token_ws']) && !empty($data['token_ws'])) {
                $token_ws = $data['token_ws'];
            } else {
                $token_ws = 0;
            }

            $result = $this->webpay->transactionResult($token_ws);
            
            /* Pago realizado correctamente y aceptado por Webpay */
            if (!empty($result->buyOrder) && $result->detailOutput->responseCode == 0) {

                $payData = $this->webpay->getPaidResult($result);
                
                $this->checkoutSession->setPaidFlag(1);
                $this->checkoutSession->setHasPaidResult($payData);

	    $order = $this->salesOrder->loadByIncrementId($this->checkoutSession->getLastRealOrderId());
            $order->setState('processing')->setStatus('processing');
            $order->save();

                
                $webPaySoap = new \WebPaySOAP($this->webpay->config);                
                $webPaySoap->redirect($result->urlRedirection, array('token_ws' => $token_ws));
            } else {

                $order = $this->salesOrder->loadByIncrementId($this->checkoutSession->getLastRealOrderId());
                $order->cancel()->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true, 'Canceled')->save();

                $responseDescription = htmlentities($result->detailOutput->responseDescription);

                $date = new \DateTime($result->transactionDate);
                $transactionDate = $date->format('d-m-Y H:i:s');

///                $this->messageManager->addError('Le informamos que su orden ' . $result->buyOrder . ', realizada el ' . $transactionDate . ' termin&oacute; de forma inesperada (' . $responseDescription . ')');
                $this->_redirect('checkout/onepage/failure/', array('_secure' => false));
            }
        } else {

            $this->messageManager->addError('Ocurri&oacute; un error al intentar conectar con WebPay Plus. Por favor intenta mas tarde.');
            $this->_redirect('checkout/onepage/failure/', array('_secure' => false));
        }        
    }
}
