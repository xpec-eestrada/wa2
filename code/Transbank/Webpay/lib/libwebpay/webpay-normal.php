<?php
require_once('soap/soap-wsse.php');
require_once('soap/soap-validation.php');
require_once('soap/soapclient.php');
//require_once 'WebpayService.php';
class getTransactionResult{
var $tokenInput;//string
}
class getTransactionResultResponse{
var $return;//transactionResultOutput
}
class transactionResultOutput{
var $accountingDate;//string
var $buyOrder;//string
var $cardDetail;//cardDetail
var $detailOutput;//wsTransactionDetailOutput
var $sessionId;//string
var $transactionDate;//dateTime
var $urlRedirection;//string
var $VCI;//string
}
class cardDetail{
var $cardNumber;//string
var $cardExpirationDate;//string
}
class wsTransactionDetailOutput{
var $authorizationCode;//string
var $paymentTypeCode;//string
var $responseCode;//int
}
class wsTransactionDetail{
var $sharesAmount;//decimal
var $sharesNumber;//int
var $amount;//decimal
var $commerceCode;//string
var $buyOrder;//string
}
class acknowledgeTransaction{
var $tokenInput;//string
}
class acknowledgeTransactionResponse{
}
class initTransaction{
var $wsInitTransactionInput;//wsInitTransactionInput
}
class wsInitTransactionInput{
var $wSTransactionType;//wsTransactionType
var $commerceId;//string
var $buyOrder;//string
var $sessionId;//string
var $returnURL;//anyURI
var $finalURL;//anyURI
var $transactionDetails;//wsTransactionDetail
var $wPMDetail;//wpmDetailInput
}
class wpmDetailInput{
var $serviceId;//string
var $cardHolderId;//string
var $cardHolderName;//string
var $cardHolderLastName1;//string
var $cardHolderLastName2;//string
var $cardHolderMail;//string
var $cellPhoneNumber;//string
var $expirationDate;//dateTime
var $commerceMail;//string
var $ufFlag;//boolean
}
class initTransactionResponse{
var $return;//wsInitTransactionOutput
}
class wsInitTransactionOutput{
var $token;//string
var $url;//string
}
class WebPayNormal
{
    var $config;
    var $soapClient;
    private $looger;
    private static $WSDL_URL_NORMAL = array(
            "INTEGRACION"   => "https://webpay3gint.transbank.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl",
            "CERTIFICACION" => "https://tbk.orangepeople.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl",
            "PRODUCCION"    => "https://webpay3g.transbank.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl",
	);

	private static $RESULT_CODES = array(
		 "0" => "Transacción aprobada",
		"-1" => "Rechazo de transacción",
		"-2" => "Transacción debe reintentarse",
		"-3" => "Error en transacción",
		"-4" => "Rechazo de transacción",
		"-5" => "Rechazo por error de tasa",
		"-6" => "Excede cupo máximo mensual",
		"-7" => "Excede límite diario por transacción",
		"-8" => "Rubro no autorizado",
    );
    
    private $xpecidorden;

    private static $classmap = array('getTransactionResult' => 'getTransactionResult', 'getTransactionResultResponse' => 'getTransactionResultResponse', 'transactionResultOutput' => 'transactionResultOutput', 'cardDetail' => 'cardDetail', 'wsTransactionDetailOutput' => 'wsTransactionDetailOutput', 'wsTransactionDetail' => 'wsTransactionDetail', 'acknowledgeTransaction' => 'acknowledgeTransaction', 'acknowledgeTransactionResponse' => 'acknowledgeTransactionResponse', 'initTransaction' => 'initTransaction', 'wsInitTransactionInput' => 'wsInitTransactionInput', 'wpmDetailInput' => 'wpmDetailInput', 'initTransactionResponse' => 'initTransactionResponse', 'wsInitTransactionOutput' => 'wsInitTransactionOutput');
    
    function __construct($config)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/webpay_'.date('Y-m-d').'.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);

		$this->config = $config;
		$privateKey = $this->config->getParam("PRIVATE_KEY");
		$publicCert = $this->config->getParam("PUBLIC_CERT");

		$modo = $this->config->getModo();
		$url = WebPayNormal::$WSDL_URL_NORMAL[$modo];
                
                $this->soapClient = new WSSecuritySoapClient($url, $privateKey, $publicCert, array(
                    "classmap" => self::$classmap,
                    "trace" => true,
                    "exceptions" => true
                ));              
    }
    
    function _getTransactionResult($getTransactionResult)
    {
        
        $getTransactionResultResponse = $this->soapClient->getTransactionResult($getTransactionResult);
        return $getTransactionResultResponse;
        
    }

    function _acknowledgeTransaction($acknowledgeTransaction)
    {
        
        $acknowledgeTransactionResponse = $this->soapClient->acknowledgeTransaction($acknowledgeTransaction);
        return $acknowledgeTransactionResponse;
        
    }

    function _initTransaction($initTransaction)
    {
        

        $initTransactionResponse = $this->soapClient->initTransaction($initTransaction);
        return $initTransactionResponse;
        
    }

    function _getReason($code){
		return WebPayNormal::$RESULT_CODES[$code];
    }
    
    public function getIdOrden(){
        return $this->xpecidorden;
    } 
    public function setIdOrden($orden){
        $this->xpecidorden=$orden;
    }

    public function initTransaction($amount, $sessionId="", $ordenCompra="0", $urlFinal){
        $this->setIdOrden($ordenCompra);
        try{
            $amount=number_format($amount,0,'','');
            $tipotrans=$this->config->getParam("TIPO_TRANS");
            $error = array();
            $arraydefi=array();
            $wsInitTransactionInput = new wsInitTransactionInput();
            $wsTransactionDetail = new wsTransactionDetail();
            $detailArray = array();
            $wsInitTransactionInput->wSTransactionType = $tipotrans;
            $arraydefi['wSTransactionType']=$tipotrans;
            $wsInitTransactionInput->commerceId = $this->config->getParam("CODIGO_MALL");
            $arraydefi['commerceId']=$this->config->getParam("CODIGO_MALL");
            $wsInitTransactionInput->buyOrder = $ordenCompra;
            $arraydefi['buyOrder']=$ordenCompra;
            $wsInitTransactionInput->sessionId = $sessionId;
            $arraydefi['sessionId']=$sessionId;
            $wsInitTransactionInput->returnURL = $this->config->getParam("URL_RETURN");
            $arraydefi['returnURL']=$this->config->getParam("URL_RETURN");
            $wsInitTransactionInput->finalURL = $urlFinal;
            $arraydefi['finalURL']=$urlFinal;
            
            $arraydetalle=array();
            /*Primera tienda*/
            $wsTransactionDetail->commerceCode = $this->config->getParam("CODIGO_COMERCIO");
            $arraydetalle['commerceCode']=$this->config->getParam("CODIGO_COMERCIO");
            $wsTransactionDetail->buyOrder = $ordenCompra;
            $arraydetalle['buyOrder']=$ordenCompra;
            $wsTransactionDetail->amount = $amount;
            $arraydetalle['amount']=$amount;
            $arraydefi['transactionDetails']=$arraydetalle;

            $this->logger->info(date('H:i:s') . ' - datos request initTransaction IdOrden='.$this->getIdOrden());

            $this->logger->info(date('H:i:s'). ' - ' .print_r($arraydefi, true) . ' IdOrden='.$this->getIdOrden());

            
            /*Se agrega al arreglo de tiendas*/
            $detailArray[0] = $wsTransactionDetail;
            $wsInitTransactionInput->transactionDetails = $detailArray;
            $endpoint = $urlFinal;
            $initTransactionResponse = $this->_initTransaction(
                array("wsInitTransactionInput" => $wsInitTransactionInput)
            );
            $xmlResponse = $this->soapClient->__getLastResponse();
            $this->logger->info(date('H:i:s') . ' - initTransaction request  - ' . $this->soapClient->__getLastResponse(). ' IdOrden='.$this->getIdOrden());
            $this->logger->info(date('H:i:s') . ' - initTransaction response - ' . $xmlResponse . ' IdOrden='.$this->getIdOrden());

            $soapValidation = new SoapValidation($xmlResponse, $this->config->getParam("WEBPAY_CERT"));
            $validationResult = $soapValidation->getValidationResult();
            /*Invocar sólo sí $validationResult es TRUE*/
            if ($validationResult) {
                $wsInitTransactionOutput = $initTransactionResponse->return;
                $this->logger->info(date('H:i:s') . ' - TOKEN de Transacción entregado por Webpay - ' . $xmlResponse . ' IdOrden='.$this->getIdOrden());
                /*TOKEN de Transacción entregado por Webpay*/
                $tokenWebpay = $wsInitTransactionOutput->token;
                $this->logger->info(date('H:i:s') . ' - URL donde se debe continuar el flujo - ' . $wsInitTransactionOutput->url . ' IdOrden='.$this->getIdOrden());
                /*URL donde se debe continuar el flujo*/
                $urlRedirect = $wsInitTransactionOutput->url;
                return array ( 
                    "url" => $urlRedirect,
                    "token_ws" => $tokenWebpay
                );
            }else{           
                $error["error"] = "Error validando conexión a Webpay";
                $error["detail"] = "No se puede validar la respuesta usando certificado " . WebPaySOAP::getConfig("WEBPAY_CERT");
                $this->logger->info(date('H:i:s') . ' - Error - '.$error["detail"] . ' IdOrden='.$this->getIdOrden());
            }
        }catch(Exception $err){
            $error["error"] = "Error conectando a Webpay";
            $error["detail"] = $err->getMessage();
            $this->logger->info(date('H:i:s') . ' - Error - '.$err->getMessage() . ' IdOrden='.$this->getIdOrden());
        }
        return $error;
    }


	public function getTransactionResult($token){		
		$getTransactionResult = new getTransactionResult();
		$getTransactionResult->tokenInput = $token;
		$getTransactionResultResponse = $this->_getTransactionResult($getTransactionResult);
		
        $xmlResponse = $this->soapClient->__getLastResponse();
        $tmpidorden = $getTransactionResultResponse->return;
        $this->setIdOrden($tmpidorden->buyOrder);
        
        $this->logger->info(date('H:i:s') . ' - initTransaction request  - ' . $this->soapClient->__getLastResponse(). ' IdOrden='.$this->getIdOrden());
        $this->logger->info(date('H:i:s') . ' - transactionResult response - ' . $xmlResponse. ' IdOrden='.$this->getIdOrden());

		$soapValidation = new SoapValidation($xmlResponse, $this->config->getParam("WEBPAY_CERT"));
		$validationResult = $soapValidation->getValidationResult();
        if ($validationResult === TRUE){
            $result = $getTransactionResultResponse->return;
            $this->logger->info(date('H:i:s') . ' - Avisar a transbank que transaccion esta OK - ' . ' IdOrden='.$this->getIdOrden());
			/** Avisar a transbank que transaccion esta OK */
			if ($this->acknowledgeTransaction($token)){
                $this->logger->info(date('H:i:s') . ' - Ver si transaccion fue exitosa - ' . ' IdOrden='.$this->getIdOrden());
				/** Ver si transaccion fue exitosa */
				$resultCode = $result->detailOutput->responseCode;
				if ( ($result->VCI == "TSY" || $result->VCI == "") && $resultCode == 0){
                    $this->logger->info(date('H:i:s') . ' - Codigo de Respuesta : '.$resultCode.' - ' . 'VCI = '.$result->VCI . ' IdOrden='.$this->getIdOrden());
					return $result;
					//$result["aaa"] = "OK";
                    /*
                    TSY: Autenticación exitosa
                    TSN: autenticación fallida.
                    TO: Tiempo máximo excedido para autenticación.
                    ABO: Autenticación abortada por tarjetahabiente.
                    U3: Error interno en la autenticación.
                    Puede ser vacío si la transacción no se autentico.

                    0 Transacción aprobada.
                    -1 Rechazo de transacción.
                    -2 Transacción debe reintentarse.
                    -3 Error en transacción.
                    -4 Rechazo de transacción.
                    -5 Rechazo por error de tasa.
                    -6 Excede cupo máximo mensual.
                    -7 Excede límite diario por transacción.
                    -8 Rubro no autorizado.
                    */
				}else{
                    $this->logger->info(date('H:i:s') . ' - Codigo de Respuesta Incorrecto : '.$resultCode.' - ' . 'VCI = '.$result->VCI . ' IdOrden='.$this->getIdOrden());
					$result->detailOutput->responseDescription = $this->_getReason($resultCode);
					return $result;					
				}
			}
			else{
                $this->logger->info(date('H:i:s') . ' - Error eviando ACK a Webpay - ' . ' IdOrden='.$this->getIdOrden());
				return array("error" => "Error eviando ACK a Webpay");
			}
        }		
        $this->logger->info(date('H:i:s') . ' - Error validando transacción en Webpay - ' . ' IdOrden='.$this->getIdOrden());
		return array("error" => "Error validando transacción en Webpay");
	}


	public function acknowledgeTransaction($token){
		$acknowledgeTransaction = new acknowledgeTransaction();
		$acknowledgeTransaction->tokenInput = $token;
		$acknowledgeTransactionResponse = $this->_acknowledgeTransaction($acknowledgeTransaction);

        $xmlResponse = $this->soapClient->__getLastResponse();
        
        $this->logger->info(date('H:i:s') . ' - acknowledgeTransaction request  - ' . $this->soapClient->__getLastRequest(). ' IdOrden='.$this->getIdOrden());
        $this->logger->info(date('H:i:s') . ' - acknowledgeTransaction response - ' . $xmlResponse. ' IdOrden='.$this->getIdOrden());
        
		$soapValidation = new SoapValidation($xmlResponse, $this->config->getParam("WEBPAY_CERT"));
		$validationResult = $soapValidation->getValidationResult();
        return $validationResult === TRUE;
	}
}
