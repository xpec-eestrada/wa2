<?php

namespace Xpectrum\Servipag\Model;

require_once(__DIR__ . '/../lib/libservipag/BotonPago.php');

class ServipagPayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_SERVIPAG_CODE = 'servipag';
    
    const SERVIPAG_URL = 'https://www.servipag.com/BotonPago/BotonPago/Pagar';
    
    /**
     * @var array
     */
    protected $matrizIni;
    
    /**
     * \BotonPago
     */
    protected $botonPago;


    /**
     * 
     * @var \Magento\Checkout\Model\Session 
     */    
    protected $checkoutSession;
    
    /**
     *
     * @var \Magento\Customer\Model\Session 
     */
    protected $customerSession;
    
    /**
     * @var \Xpectrum\Servipag\Model\ServipagFactory 
     */
    protected $servipagFactory;
    
    /**
     *
     * @var \Magento\Sales\Model\Order 
     */
    protected $salesOrder;
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;
            
    /**
     *
     * @var string 
     */
    private $publicKey;
    
    /**
     *
     * @var string 
     */
    private $privateKey;
    
    /**
     *
     * @var string 
     */
    private $paymentChannelCode;   
    
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_SERVIPAG_CODE;
    
    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * Xpectrum Servipag block path
     *
     * @var string
     */
    protected $_infoBlockType = 'Xpectrum\Servipag\Block\Info\Servipag';

    public function __construct(
        \Xpectrum\Servipag\Model\ServipagFactory $servipagFactory,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,        
        \Magento\Sales\Model\Order $salesOrder,        
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
        
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->servipagFactory = $servipagFactory;
        $this->salesOrder = $salesOrder;        
        $this->_storeManager = $storeManager;

        $cart = $this->checkoutSession->getCartData();
        $total = $cart['cartAmount'];
        
        $this->directoryList = $directoryList;
        $path_config = $this->directoryList->getPath('app') . '/code/Xpectrum/Servipag/lib/libservipag/config.ini';
        $matrizIni = parse_ini_file($path_config, true);
        
        $this->botonPago = new \BotonPago();
        $this->matrizIni = $matrizIni;        
        $this->publicKey = $this->matrizIni['Config_Llaves']['publica'];
        $this->privateKey = $this->matrizIni['Config_Llaves']['privada'];
        $this->paymentChannelCode = $this->getConfigData('payment_channel_code');
    }
    
    /**
     * Permite obtener el Status
     */
    public function getOrderStatus()
    {
        return $this->status;
    }
    
    /**
     * Obtiene datos de transacción
     */
    public function validate()
    {
        $cartData = array(
            'cartAmount' => $this->checkoutSession->getQuote()->getGrandTotal(),
            'cartId' => $this->checkoutSession->getQuote()->getId()
        );
        
        $this->checkoutSession->setCartData($cartData);
        return $this;
    }
    
    /**
     * Genera el XML1
     * 
     * @return string XML1
     */
    public function setXml1()
    {
        $xml = '';
        
        try {            
            
            $orderId = $this->checkoutSession->getLastRealOrderId();
            $cart = $this->checkoutSession->getCartData();
            $total = $cart['cartAmount'];
            $customer = $this->customerSession->getCustomer();
            
            // estableco las llaves            
            $this->botonPago->setRutaLlaves($this->privateKey, $this->publicKey);
            $this->botonPago->setArrayOrdenamiento($this->matrizIni['Config_Nodo']);            

            $codigoCanalPago = $this->paymentChannelCode;
            $idTxPago = $orderId;
            $fechadePago = date('Ymd');
            $montoTotalDeuda = number_format($total, 0, '', '');
            $idSubTx = '1';
            $numeroBoletas = '1';
            $identificador = $orderId;
            $boleta = date('YmdHis');
            $monto = number_format($total, 0, '', '');
            $fechaVencimiento = date('Ymd');
            $nombreCliente = $customer->getName();
            $rutCliente = '';
            $emailCliente = $customer->getEmail();
            $idOrderState = '13';
            $fecha = date ('Y-m-d H:i:s');

            $xml = $this->botonPago->generaXml(
                    $codigoCanalPago, 
                    $idTxPago, 
                    $fechadePago, 
                    $montoTotalDeuda, 
                    $numeroBoletas, 
                    $idSubTx, 
                    $identificador, 
                    $boleta, 
                    $monto, 
                    $fechaVencimiento, 
                    $nombreCliente, 
                    $rutCliente, 
                    $emailCliente
            );
            
            $this->checkoutSession->unsCartData();
            
            if ($this->getConfigData('debug_xml1')) {
                $this->setDebugXml1(
                    $customer->getId(),
                    $identificador,
                    $codigoCanalPago,
                    $idTxPago,
                    $fechadePago,
                    $montoTotalDeuda,
                    $idSubTx,
                    $numeroBoletas,            
                    $boleta,
                    $monto,
                    $fechaVencimiento,
                    $nombreCliente,
                    $rutCliente,
                    $emailCliente,
                    $idOrderState,
                    $fecha,
                    $xml
                );
            }            
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }        
            
        return $xml;
    }
    
    /**
     * Genera un archivo log para testear XML1
     * 
     * @param int $customerId ID del cliente
     * @param int $identificador ID del carro
     * @param string $codigoCanalPago Codigo canal de pago
     * @param int $idTxPago ID del carro
     * @param string $fechadePago Fecha de pago
     * @param int $montoTotalDeuda Monto a pagar
     * @param int $idSubTx ID sub tx
     * @param int $numeroBoletas Numero boletas
     * @param string $boleta Fecha transaccion
     * @param int $monto Monto a pagar
     * @param string $fechaVencimiento Fecha de vencimiento
     * @param string $nombreCliente Nombre del cliente
     * @param string $rutCliente Rut del cliente
     * @param string $emailCliente Email del cliente
     * @param int $idOrderState ID order state
     * @param string $fecha Fecha de la transaccion
     * @param string $xml XML1 
     * @return void
     */
    public function setDebugXml1($customerId, $identificador, $codigoCanalPago,
            $idTxPago, $fechadePago, $montoTotalDeuda, $idSubTx, $numeroBoletas,
            $boleta, $monto, $fechaVencimiento, $nombreCliente,
            $rutCliente, $emailCliente, $idOrderState, $fecha, $xml)
    {
        $debugUser = fopen(__DIR__ . '/../logs/user-' . $customerId . '-carro-' . $identificador . '.txt', 'wt');
        fwrite($debugUser, 'CodigoCanal = ' . $codigoCanalPago . "\n");
        fwrite($debugUser, 'IdTxPago = ' . $idTxPago . "\n");
        fwrite($debugUser, 'FechaPago = ' . $fechadePago . "\n");
        fwrite($debugUser, 'MontoTotalDeuda = ' . $montoTotalDeuda . "\n");
        fwrite($debugUser, 'IdSubTx = ' . $idSubTx . "\n");
        fwrite($debugUser, 'NumeroBoletas = ' . $numeroBoletas . "\n");
        fwrite($debugUser, 'Identificador = ' . $identificador . "\n");
        fwrite($debugUser, 'Boleta = ' . $boleta . "\n");
        fwrite($debugUser, 'Monto = ' . $monto . "\n");
        fwrite($debugUser, 'FechaVencimiento = ' . $fechaVencimiento . "\n");
        fwrite($debugUser, 'NombreCliente = ' . $nombreCliente . "\n");
        fwrite($debugUser, 'RutCliente = ' . $rutCliente . "\n");
        fwrite($debugUser, 'EmailCliente = ' . $emailCliente . "\n");
        fwrite($debugUser, 'IdOrderState = ' . $idOrderState . "\n");
        fwrite($debugUser, 'Fecha = ' . $fecha . "\n");
        fwrite($debugUser, 'XML = ' . $xml . "\n");
        fclose($debugUser);
    }
    
    /**
     * Genera log con el resultado del XML2 enviado por servipag
     * 
     * @param string $idTransaccion ID de la transaccion en servipag
     * @param string $xml2 XML2 enviado por servipag
     * @return void
     */
    public function setLogXml2($idTransaccion, $xml2)
    {
        $debug = fopen(__DIR__ . '/../logs/resultadoXml2-' . $idTransaccion . '.txt', 'wt');
        fwrite($debug, 'XML 2 = ' . $xml2 . "\n");
        fclose($debug);
    }
    
    /**
     * Valida el XML2
     * 
     * @param string $xml2 XML2
     * @return array Respuesta de la validacion
     */
    public function validateXml2($xml2)
    {
        $resp['codigo'] = 1;
        $resp['mensaje'] = 'Transaccion Mala';
        $resp['id_servipag'] = 0;
        
        try {
            $idTxCliente = substr($xml2, strrpos($xml2, '<IdTxCliente>'), (strrpos($xml2, '</IdTxCliente>') - strrpos($xml2, '<IdTxCliente>')));
            $idTrxServipag = substr($xml2, strrpos($xml2, '<IdTrxServipag>'), (strrpos($xml2, '</IdTrxServipag>') - strrpos ($xml2, '<IdTrxServipag>')));
            $monto = substr($xml2, strrpos($xml2, '<Monto>'), (strrpos($xml2, '</Monto>') - strrpos($xml2, '<Monto>')));
            
            $idServipag = str_replace('<IdTrxServipag>', '', $idTrxServipag);
            $montoPedido = (int)str_replace('<Monto>', '', $monto);
            $idPedido = str_replace('<IdTxCliente>', '', $idTxCliente);    
            
            // Log XML2
            $this->setLogXml2($idServipag, $xml2);        

            $nodo = $this->matrizIni['Config_Nodo_XML2'];

            // Estableco las rutas de las llaves
            $this->botonPago->setRutaLlaves($this->privateKey, $this->publicKey);

            // Realizo la comprobacion del XML2
            $result = $this->botonPago->compruebaXML2($xml2, $nodo);

            // genero codigo y mensaje para el xml3        
            if ($result) {
                $resp['codigo'] = 0;
                $resp['mensaje'] = 'Transaccion OK 10-4';
                $resp['id_servipag'] = $idServipag;
                
                $customer = $this->customerSession->getCustomer();                
                $order = $this->salesOrder->loadByIncrementId($idPedido);
                
                // Guardo en la tabla servipag
                $servipag = $this->servipagFactory->create();
                $servipag->setIdServipag($idServipag);
                $servipag->setIdCliente($customer->getId());
                $servipag->setIdPedido($idPedido);
                $servipag->setEstadoPago('Aceptado');
                $servipag->setMonto($montoPedido);
                $servipag->setFecha($order->getCreatedAt());
                $servipag->save();
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }        
        
        return $resp;
    }
    
    /**
     * Genera el XML 3
     * 
     * @param string $codigo Codigo de respuesta
     * @param string $mensaje Mensaje de respuesta
     * @return string XML3
     */
    public function setXml3($codigo, $mensaje)
    {
        return $this->botonPago->generaXml3($codigo, $mensaje);        
    }
    
    /**
     * Genera log con el resultado del XML3
     * 
     * @param string $idTransaccion ID de la transaccion en servipag
     * @param string $xml3 XML3 enviado por servipag
     * @return void
     */
    public function setLogXml3($idTransaccion, $xml3)
    {
        $debug = fopen(__DIR__ . '/../logs/resultadoXml3-' . $idTransaccion . '.txt', 'wt');
        fwrite($debug, 'XML 3 = ' . $xml3 . "\n");
        fclose($debug);
    }
    
    /**
     * Valida el XML4
     * 
     * @param string $xml4
     * @return array $xml4 Respuesta de la validacion
     */
    public function validateXml4($xml4)
    {
        $resp['codigo'] = 1;
        $resp['mensaje'] = 'Transaccion Mala';
        $resp['id_servipag'] = 0;
        
        try {
            $nodo = $this->matrizIni['Config_Nodo_XML4'];

            // estableco las rutas de las llaves
            $this->botonPago->setRutaLlaves($this->privateKey, $this->publicKey);
            
            $idTrxServipag = substr($xml4, strrpos($xml4, '<IdTrxServipag>'), (strrpos($xml4, '</IdTrxServipag>') - strrpos($xml4, '<IdTrxServipag>')));
            $idServipag = str_replace('<IdTrxServipag>', '', $idTrxServipag);
            
            // realizo la comprobacion del XML4
            $result = $this->botonPago->validaXml4($xml4, $nodo);

            // genero codigo y mensaje para el xml4
            if ($result) {                
                $resp['codigo'] = 0;
                $resp['mensaje'] = 'Transaccion OK 10-4';
                $resp['id_servipag'] = $idServipag;
            }
            
            $servipag = $this->servipagFactory->create();
            $servipag->loadByIdServipag($idServipag);
            $servipag_data = array(
                'id_servipag' => $servipag->getIdServipag(),
                'id_pedido' => $servipag->getIdPedido(),
                'estado_pago' => $servipag->getEstadoPago(),
                'fecha' => $servipag->getFecha(),
                'monto' => $servipag->getMonto()
            );
            
            $this->checkoutSession->setPaidResult($servipag_data);
            $this->setLogXml4(
                $idServipag, 
                $xml4, 
                $servipag->getIdPedido(), 
                $servipag->getEstadoPago(), 
                $servipag->getMonto()
            );
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
        
        return $resp;
    }
    
    /**
     * Genera log con el resultado del XML4
     * 
     * @param string $idTransaccion ID de la transaccion en servipag
     * @param string $xml4 XML4 enviado por servipag
     * @param string $idPedido ID del pedido
     * @param string $estadoPedido Estado del pedido
     * @param int $monto Monto del pedido
     * @return void
     */
    public function setLogXml4($idTransaccion, $xml4, $idPedido, $estadoPedido, $monto)
    {
        $debug = fopen(__DIR__ . '/../logs/resultadoXml4-' . $idTransaccion . '.txt', 'wt');
        fwrite($debug, 'XML 4 = ' . $xml4 . "\n");        
        fwrite($debug, 'id = ' . $idPedido . "\n");
        fwrite($debug, 'servipag = ' . $idTransaccion . "\n");
        fwrite($debug, 'estado = ' . $estadoPedido . "\n");
        fwrite($debug, 'monto = ' . $monto . "\n");
        fclose($debug);
    }
}