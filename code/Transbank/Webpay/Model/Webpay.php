<?php

namespace Transbank\Webpay\Model;

require_once(__DIR__ . '/../lib/libwebpay/webpay-soap.php');

class Webpay extends \Magento\Payment\Model\Method\AbstractMethod
{    
    /**
     * 
     * @var \Magento\Checkout\Model\Session 
     */    
    protected $checkoutSession;
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $_storeManager;
    
    const PAYMENT_METHOD_WEBPAY_CODE = 'webpay';
    
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_WEBPAY_CODE;
    
    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * Transbank Webpay block path
     *
     * @var string
     */
    protected $_infoBlockType = 'Transbank\Webpay\Block\Info\Webpay';

    private $_accesToken;

    private $_detallepagoFactory;

    private $loggerxpec;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        //$this->_detallepagoFactory=$DetallepagoFactory;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/xpec_webpay_order.log');
        $this->loggerxpec = new \Zend\Log\Logger();
        $this->loggerxpec->addWriter($writer);
        
        $this->checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;        
        $this->title = $this->getConfigData('title');
        $this->description = $this->getConfigData('description');
        $this->status = $this->getConfigData('status');
        
        $store = $this->_storeManager->getStore();
        $idstore=$this->_storeManager->getStore()->getId();
        $this->_accesToken=$this->getConfigData('token_serv',$idstore);
        $this->config = array(
            'MODO' => $this->getConfigData('test_mode'),
            'PRIVATE_KEY' => $this->getConfigData('private_key',$idstore),
            'PUBLIC_CERT' => $this->getConfigData('public_cert',$idstore),
            'WEBPAY_CERT' => $this->getConfigData('webpay_cert',$idstore),
            'CODIGO_COMERCIO' => $this->getConfigData('commerce_code',$idstore),
            'TIPO_TRANS' => $this->getConfigData('tipo_trans',$idstore),
            'CODIGO_MALL' => $this->getConfigData('mall_code',$idstore),
            'URL_RETURN' => $store->getBaseUrl() . 'webpay/payment/response',
            'URL_FINAL' => $store->getBaseUrl() . 'webpay/payment/success',
            'VENTA_DESC' => array(
                'VD' => 'Venta D&eacute;bito',
                'VN' => 'Venta Cr&eacute;dito',
                'VC' => 'Venta en cuotas',
                'SI' => '3 cuotas sin inter&eacute;s',
                'S2' => '2 cuotas sin inter&eacute;s',
                'NC' => 'N cuotas sin inter&eacute;s',
            ),
        );
    }
    
    /**
     * Realiza la primera transacción con Webpay
     */
    public function getPaidConnect()
    {       
        $response = $this->checkoutSession->getCartData();        
        $webpay = new \WebPaySOAP($this->config);        
        $result = $webpay->webpayNormal->initTransaction($response['cartAmount'], $sessionId = '', $response['cartOrder'], $response['urlFinal']);
        $this->checkoutSession->unsCartData();
        
        return $result;
    }

    /**
     * Obtiene datos de transacción
     */
    public function validate()
    {

        $cartData = array(
            'cartAmount' => $this->checkoutSession->getQuote()->getGrandTotal(),
            'cartOrder' => $this->checkoutSession->getQuote()->getReservedOrderId(),
            'urlFinal' => $this->config['URL_FINAL'],
        );
        
        $this->checkoutSession->setCartData($cartData);
        return $this;
    }

    /**
     * Permite realizar la comprobación de pago
     */
    public function transactionResult($token_ws)
    {        
        try {
            $webpay = new \WebPaySoap($this->config);
            $result = $webpay->webpayNormal->getTransactionResult($token_ws);
        } catch (Exception $e) {
            $result['error'] = 'Error conectando a Webpay';
            $result['detail'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Permite obtener el Status
     */
    public function getOrderStatus()
    {
        return $this->status;
    }

    /**
     * Permite obtener información del Pago
     */
    public function getPaidResult($result)
    {
        $this->loggerxpec->info('antes print');

        $paymentTypeCode = $result->detailOutput->paymentTypeCode;
        $paymenCodeResult = $this->config['VENTA_DESC'][$paymentTypeCode];
        $tipoPago='';
        $tipoPagotildada='';
        $tipoCuotas='';
        $numeroCuotas='';
        $attribadd='';
        switch($paymentTypeCode){
            case 'VD':
                $tipoPago='D&eacute;bito';
                $tipoPagotildada='Débito';
                $tipoCuotas='Venta D&eacute;bito';
                $numeroCuotas='0';
            break;
            case 'VN':
                $tipoPago='Cr&eacute;dito';
                $tipoPagotildada='Crédito';
                $tipoCuotas='Sin Cuotas';
                $numeroCuotas='0';
            break;
            case 'VC':
                $tipoPago='Cr&eacute;dito';
                $tipoPagotildada='Crédito';
                $tipoCuotas='Cuotas Normales';
                $numeroCuotas='4-48';
            break;
            case 'SI':
                $tipoPago='Cr&eacute;dito';
                $tipoPagotildada='Crédito';
                $tipoCuotas='Sin Inter&eacute;s';
                $numeroCuotas='3';
            break;
            case 'S2':
                $tipoPago='Cr&eacute;dito';
                $tipoPagotildada='Crédito';
                $tipoCuotas='Sin Inter&eacute;s';
                $numeroCuotas='2';
            break;
            case 'NC':
                $tipoPago='Cr&eacute;dito';
                $tipoPagotildada='Crédito';
                $tipoCuotas='Sin Inter&eacute;s';
                $numeroCuotas='2-10';
            break;
        }

        if ($result->detailOutput->responseCode == 0) {
            $transactionResponse = 'Aceptado';
        } else {
            $transactionResponse = 'Rechazado [' . $result->detailOutput->responseCode . ']';
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($result->buyOrder);
        try {
            $emailSender = $objectManager->create('\Magento\Sales\Model\Order\Email\Sender\OrderSender');
            $emailSender->send($order);
        } catch (\Exception $e) {
            $this->loggerxpec->info($e->getMessage());
            //$this->_logger->critical($e);
        }

        // $payment = $order->getPayment();
        // $payment->setAdditionalInformation('payment', 'TARJETA CR');
        // $payment->save();
        //$detorden=$this->obtenerDetalleOrden($order->getId());
        

        $payResult = array(
            'description' => $this->title,
            'paymenCodeResult' => $transactionResponse,
            'date_accepted' => $result->transactionDate,
            'buyOrder' => $result->buyOrder,
            'authorizationCode' => $result->detailOutput->authorizationCode,
            'cardNumber' => $result->cardDetail->cardNumber,
            'amount' => $result->detailOutput->amount,
            'sharesNumber' => $result->detailOutput->sharesNumber,
            'paymenCodeResult' => $paymenCodeResult,
            'detalle' => array(
                'tipopago' => $tipoPago,
                'tipocuotas' => $tipoCuotas,
                'numcuotas' => $numeroCuotas
            ),
            'detalleOrder' => $order,
            'objpay'=>$order
        );
        $xpecDetallePago = array(
            'description' => $this->title,
            'paymenCodeResult' => $transactionResponse,
            'date_accepted' => $result->transactionDate,
            'buyOrder' => $result->buyOrder,
            'authorizationCode' => $result->detailOutput->authorizationCode,
            'cardNumber' => $result->cardDetail->cardNumber,
            'amount' => $result->detailOutput->amount,
            'sharesNumber' => $result->detailOutput->sharesNumber,
            'paymenCodeResult' => $paymenCodeResult,
            'detalle' => array(
                'tipopago' => $tipoPago,
                'tipocuotas' => $tipoCuotas,
                'numcuotas' => $numeroCuotas
            ),
            'orderID' => $order->getId()
        );
        // $det = $objectManager->create('\Xpectrum\Detallepago\Model\Detallepago');
        // $json = json_encode( $xpecDetallePago );
        // $det->grabarDetalle($result->buyOrder,$json);
        $arraylog=array(
            'description' => $this->title,
            'paymenCodeResult' => $transactionResponse,
            'date_accepted' => $result->transactionDate,
            'buyOrder' => $result->buyOrder,
            'authorizationCode' => $result->detailOutput->authorizationCode,
            'cardNumber' => $result->cardDetail->cardNumber,
            'amount' => $result->detailOutput->amount,
            'sharesNumber' => $result->detailOutput->sharesNumber,
            'paymenCodeResult' => $paymenCodeResult,
            'detalle' => array(
                'tipopago' => $tipoPago,
                'tipocuotas' => $tipoCuotas,
                'numcuotas' => $numeroCuotas
            ),
        );

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('sales_order_payment');
        $sql='UPDATE '.$tableName.'
            SET
                cc_type=\''.$tipoPagotildada.'\',
                cc_last_4=\''.$result->cardDetail->cardNumber.'\',
                cc_trans_id=\''.$result->detailOutput->authorizationCode.'\'
            WHERE
                parent_id='.$order->getId();
        $connection->query($sql);
        $this->loggerxpec->info(print_r(array('Orden'=>$arraylog ), true));
        return $payResult;
    }
    private function obtenerDetalleOrden($idorden){
        try{
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $url=$storeManager->getStore()->getBaseUrl().'index.php/rest/V1/orders/'.$idorden;
            $token = $this->_accesToken;
            $httpHeaders = new \Zend\Http\Headers();
            $httpHeaders->addHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]);
            
            $request = new \Zend\Http\Request();
            $request->setHeaders($httpHeaders);
            $request->setMethod(\Zend\Http\Request::METHOD_GET);
            $request->setUri($url);
            $client = new \Zend\Http\Client();
            $options = [
                'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => [CURLOPT_FOLLOWLOCATION => true]
            ];
            $client->setOptions($options);
            $response = $client->send($request);
            return $response;
        }catch(Exception $err){
            $this->loggerxpec->info(print_r(array('error'=>$err->getMessage(),'idOrden'=>$idorder), true));
            throw new Exception($err->getMessage());
        }
    }
    private function obtenerItemsOrden($idorden){
        try{
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $url=$storeManager->getStore()->getBaseUrl().'index.php/rest/V1/orders/items/'.$idorden;
            $token = $this->_accesToken;
            $httpHeaders = new \Zend\Http\Headers();
            $httpHeaders->addHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]);
            
            $request = new \Zend\Http\Request();
            $request->setHeaders($httpHeaders);
            $request->setMethod(\Zend\Http\Request::METHOD_GET);
            $request->setUri($url);
            $client = new \Zend\Http\Client();
            $options = [
                'adapter'   => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => [CURLOPT_FOLLOWLOCATION => true]
            ];
            $client->setOptions($options);
            $response = $client->send($request);
            return $response;
        }catch(Exception $err){
            $this->loggerxpec->info(print_r(array('error'=>$err->getMessage(),'idOrden'=>$idorder), true));
            throw new Exception($err->getMessage());
        }
    }
}