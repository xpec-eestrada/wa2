<?php

/**
 * Implementacion de nueva clase Boton de Pago 2.0
 * 
 * @name BotonPago
 * @since 6-2-2013
 * @version 1.2
 * @author Francisco Hurtado <fhurtado@servipag.cl>
 */
class BotonPago
{	
	/**
     *
     * @var string variable que contiene la ruta de la llave privada
     */
	private $rutaLlavePrivada;
    
    /**
     *
     * @var string variable que contiene array de conquetenacion ordenado
     */
	private $rutaLlavePublica;
    
    /**
     *
     * @var array variable que contiene array de conquetenacion ordenado
     */
	private $arrayOrdenamiento;
    
    /**
     *
     * @var string variable que contiene la ruta de los log
     */
	private $rutalog;
	                  
	/**
     *  constructor de la clase
     */
	public function __construct() {}
    
    /**
     * Setea el array de concatenar y lo ordena
     * 
     * @param array $ord array de concatenacion
     * @return void
     */
	public function setArrayOrdenamiento($ord = null)
    {
		if ($ord) {
			asort($ord);
			$this->arrayOrdenamiento = $ord;
			$this->generarLog('0', 'Realiza ordenamiento:' . implode(',', $ord));
		}
	}
    
    /**
     * Setea las rutas de las llaves estableciendo los valores en las variables
     * privadas
     * 
     * @param string $rutaLlavePri ruta llave privada, el valor puede ser nulo
     * @param string $rutaLlavePub ruta llave publica, el valor puede ser nulo
     * @return void
     */
	public function setRutaLlaves($rutaLlavePri = null, $rutaLlavePub = null)
    {
		if ($rutaLlavePri) {
			$this->rutaLlavePrivada = $rutaLlavePri;
			$this->generarLog('1', 'Setea Ruta Llave Privada :' . $rutaLlavePri);
		}
		if ($rutaLlavePub) {
			$this->rutaLlavePublica = $rutaLlavePub;
			$this->generarLog('1', 'Setea Ruta Llave Publica :' . $rutaLlavePub);
		}
	}
    
    /**
     * Setea la ruta log
     * 
     * @param string $rutaL ruta llave privada, el valor puede ser nulo
     * @return void
     */
	public function setRutaLog($rutaL = null)
    {
		if ($rutaL) {
			$this->rutalog = $rutaL;
			$this->generarLog('1', 'Setea Ruta Log:' . $rutaL);
		}
	}
    
    /**
     * Obtiene la llave publica desde la configuracion del modulo de pago
     * @param string $file nombre de Archivo
     * @return string llave publica
     */
	public function getPublica($file = null)
    {
		if (!$file) {
			return false;
        }
		
		$absolute_path = __DIR__ . $file;
		$fp = fopen($absolute_path, 'r');
		$txtpublica = fread ($fp, 8192);
		fclose($fp);
		
		$this->generarLog('1', 'Obtiene Llave Publica :');
		
		return $txtpublica;
	}
	
	/**
     * Obtiene la llave privada desde la configuracion del modulo de pago
     * @param string $file nombre de Archivo
     * @return string llave privada
     */
	public function getPrivada($file = null)
    {
		if (!$file) {
			return false;
        }
        
		$absolute_path = __DIR__ . $file;
		$fp = fopen($absolute_path, 'r');
		$txtprivada = fread($fp, 8192);
		fclose($fp);
		
        $this->generarLog('1', 'Obtiene Llave Privada:');
        
		return $txtprivada;
	}
    
    /**
     * Obtiene firma desde ubicacion senalada desde variable de entrada
     * 
     * @param string $file nombre de archivo
     * @return string firma
     */
	public function getFirma($file = null)
    {
		if (!$file) {
			return false;
        }
		
		$fp = fopen($file, 'r');
		$txtfirma = fread($fp, 8192);
        $txtfirma = str_replace(' ', '+', $txtfirma);
		fclose($fp);
		$this->generarLog('1', 'Obtiene Firma :' . $txtfirma);   
        
		return $txtfirma;
	}    
    
    /**
     * Realiza el cifrado de datos
     * 
     * @param string $datos datos concatenado para el cifrado
     * @return string datos firmados
     */
	public function encripta($datos)
    {
		$result = '';
		$this->generarLog('1', 'Funcion Encripta :');
		// obtiene firma publica o llave privada
		$llavePrivada = $this->getPrivada($this->rutaLlavePrivada);
		
		// firma los datos enviados
		$this->generarLog('1', '-----------------------------------');
		$this->generarLog('1', 'Realiza Firmado de los Datos :');
		$this->generarLog('1', '------- variable datos: ' . $datos);
		$this->generarLog('1', '------- variable result: ' . $result);
		$this->generarLog('1', '------- variable llavePrivada: ' . $llavePrivada);
		$this->generarLog('1', '-----------------------------------');
		
		// controla excepciones
		try {
			$this->generarLog('1', '------------------Entra dentro del try---------------------');
			openssl_sign($datos, $result, $llavePrivada, OPENSSL_ALGO_MD5);
			$this->generarLog('1', 'Realizado Firmado de los Datos :' . $result);
		} catch(Exception $e) {
			// o el tipo de Excepcion que requieras...
			$this->generarLog('1', '------- Error en generacion de firma ----: ');
			$this->generarLog('1', '------- Mensaje Error: ' . $e->getMessage ());
			$this->generarLog('1', '------- Fin Error ----------------: ');
		}
		
		$this->generarLog('1', '-----------------------------------');
		// encripta en base64
		$result = base64_encode($result);
		$this->generarLog('1', 'Realiza Encriptacion de los Datos :' . $result);
		// returna el valor
		return $result;
	}
    
    /**
     * Realiza a traves de descifrado de la llave la verificacion de la firma
     * 
     * @param string $datos datos concatenado
     * @param string $firma firma
     * @return boolean resultado de validacion de la firma
     */
	public function desencripta($datos, $firma)
    {
        $firma = str_replace(' ', '+', $firma);
        
		$this->generarLog('1', 'Funcion Desencripta :');
		// obtiene firma llave publica
		$llave = $this->getPublica($this->rutaLlavePublica);
		// desencripta en base64
		$base64 = base64_decode($firma);
		$this->generarLog('1', 'Desencripta en Base64 :' . $base64);
		
		$this->generarLog('1', 'Verificacion de Firma Datos:' . $datos . '--b64:' . $base64 . '--Llave:' . $llave);
		// Verifica firma
		if (openssl_verify($datos, $base64, $llave, OPENSSL_ALGO_MD5)) {
			$this->generarLog('1', 'Verificacion de Firma Positiva');
			return true;
		} else {
			$this->generarLog('1', 'Verificacion de Firma Negativa');
			return false;
		}
	}
    
    /**
     * Realiza la generacion del XML1 firmado
     * 
     * @param string $CodigoCanalPago codigo que contiene el canal de pago
     * @param string $IdTxPago Id cliente
     * @param string $FechaPago fecha de pago
     * @param int $MontoTotalDeuda monto total de la deuda
     * @param int $NumeroBoletas numero de boletas
     * @param string $IdSubTx Id sub trx
     * @param string $Identificador codigo identificador
     * @param string $Boleta boleta
     * @param int $Monto monto
     * @param string $FechaVencimiento fecha de vencimiento
     * @param string $NombreCliente nombre del cliente
     * @param string $RutCliente rut del cliente
     * @param string $EmailCliente email del cliente
     * @return string XML con la firma
     */
	public function generaXml($CodigoCanalPago = null, $IdTxPago = null, 
            $FechaPago = null, $MontoTotalDeuda = null, $NumeroBoletas = null, 
            $IdSubTx = null, $Identificador = null, $Boleta = null, 
            $Monto = null, $FechaVencimiento = null, $NombreCliente = null, 
            $RutCliente = null, $EmailCliente = null)
    {
		$datos = '';
		$this->generarLog('1', 'Funcion Generacion XML1');
		$this->generarLog('1', 'IdSubTx ' . $IdSubTx);
		
		foreach ($this->arrayOrdenamiento as $key => $val) {
			switch ($key) {
				case 'CodigoCanalPago' :
					$datos = $datos . $CodigoCanalPago;
					break;
				case 'IdTxPago' :
					$datos = $datos . $IdTxPago;
					break;
				case 'FechaPago' :
					$datos = $datos . $FechaPago;
					break;
				case 'MontoTotalDeuda' :
					$datos = $datos . $MontoTotalDeuda;
					break;
				case 'NumeroBoletas' :
					$datos = $datos . $NumeroBoletas;
					break;
			}
		}
        
		foreach ($this->arrayOrdenamiento as $key => $val) {
			switch ($key) {				
				case 'IdSubTx' :
					$datos = $datos . $IdSubTx;
					break;
				case 'Identificador' :
					$datos = $datos . $Identificador;
					break;
				case 'Boleta' :
					$datos = $datos . $Boleta;
					break;
				case 'Monto' :
					$datos = $datos . $Monto;
					break;
				case 'FechaVencimiento' :
					$datos = $datos . $FechaVencimiento;
					break;
			}
		}
		
		$this->generarLog('1', 'Datos Concatenado :' . $datos);
		$firma = $this->encripta($datos);
		$this->generarLog('1', 'Firma para XML1:' . $firma);
		$xml = "<?xml version='1.0' encoding='ISO-8859-1'?><Servipag><Header><FirmaEPS>$firma</FirmaEPS><CodigoCanalPago>$CodigoCanalPago</CodigoCanalPago><IdTxPago>$IdTxPago</IdTxPago><EmailCliente>$EmailCliente</EmailCliente><NombreCliente>$NombreCliente</NombreCliente><RutCliente>$RutCliente</RutCliente><FechaPago>$FechaPago</FechaPago><MontoTotalDeuda>$MontoTotalDeuda</MontoTotalDeuda><NumeroBoletas>$NumeroBoletas</NumeroBoletas><Version>2</Version></Header><Documentos><IdSubTx>$IdSubTx</IdSubTx><Identificador>$Identificador</Identificador><Boleta>$Boleta</Boleta><Monto>$Monto</Monto><FechaVencimiento>$FechaVencimiento</FechaVencimiento></Documentos></Servipag>";
		$this->generarLog('1', 'XML1 completo:' . $xml);
        
		return $xml;
	}
    
    /**
     * Realiza la generacion de los logs
     * 
     * @param int $numero numero del log
     * @param string $texto mensaje del log
     * @return void
     */
	public function generarLog($numero, $texto)
    {		
		// se agrega ruta donde se guarda log
		$realtime = __DIR__ . '/../../logs/' . date ('Ymd') . '.log';
		$ddf = fopen($realtime, 'a');
		fwrite($ddf, "[" . date('r') . "]     $numero: $texto \r\n");
		fclose($ddf);
	}    
    
    /**
     * Realiza la validacion del XML2 recibido
     * 
     * @param string $xml xml
     * @param array $nodo array con nodo
     * @return boolean resultado de la validacion
     */
	public function compruebaXml2($xml, $nodo)
    {
		$this->generarLog('1', 'Funcion Comprueba Xml2:');
		$this->generarLog('1', 'xml:' . $xml);
		$this->generarLog('1', 'nodo:' . implode(',', $nodo));
		$datos = '';
		$firma = substr($xml, strrpos($xml, '<FirmaServipag>'), (strrpos($xml, '</FirmaServipag>') - strrpos($xml, '<FirmaServipag>')));
		$firma = str_replace('<FirmaServipag>', '', $firma);
		$this->generarLog('1', 'Obtencion Firma dentro XML2 :' . $firma);        
		asort($nodo);
        
		foreach ($nodo as $key => $val) {
			switch ($key) {
				case 'IdTrxServipag' :
					$datos = $datos . substr($xml, strrpos($xml, '<IdTrxServipag>'), strrpos($xml, '</IdTrxServipag>') - strrpos($xml, '<IdTrxServipag>'));
					$datos = str_replace('<IdTrxServipag>', '', $datos);
					break;
				case 'IdTxCliente' :
					$datos = $datos . substr($xml, strrpos($xml, '<IdTxCliente>'), strrpos($xml, '</IdTxCliente>') - strrpos($xml, '<IdTxCliente>'));
					$datos = str_replace('<IdTxCliente>', '', $datos);
					break;
				case 'FechaPago' :
					$datos = $datos . substr($xml, strrpos($xml, '<FechaPago>'), strrpos($xml, "</FechaPago>") - strrpos($xml, '<FechaPago>'));
					$datos = str_replace('<FechaPago>', '', $datos);
					break;
				case 'CodMedioPago' :
					$datos = $datos . substr($xml, strrpos($xml, '<CodMedioPago>'), strrpos($xml, '</CodMedioPago>') - strrpos($xml, '<CodMedioPago>'));
					$datos = str_replace('<CodMedioPago>', '', $datos);
					break;
				case 'FechaContable' :
					$datos = $datos . substr($xml, strrpos($xml, '<FechaContable>'), strrpos($xml, '</FechaContable>') - strrpos($xml, '<FechaContable>'));
					$datos = str_replace('<FechaContable>', '', $datos);
					break;
				case 'CodigoIdentificador' :
					$datos = $datos . substr($xml, strrpos($xml, '<CodigoIdentificador>'), strrpos($xml, '</CodigoIdentificador>') - strrpos($xml, '<CodigoIdentificador>'));
					$datos = str_replace('<CodigoIdentificador>', '', $datos);
					break;
				case 'Boleta' :
					$datos = $datos . substr($xml, strrpos($xml, '<Boleta>'), strrpos($xml, '</Boleta>') - strrpos($xml, '<Boleta>'));
					$datos = str_replace('<Boleta>', '', $datos);
					break;
				case 'Monto' :
					$datos = $datos . substr($xml, strrpos($xml, '<Monto>'), strrpos($xml, '</Monto>') - strrpos($xml, '<Monto>'));
					$datos = str_replace('<Monto>', '', $datos);
					break;
			}
		}
        
		$this->generarLog('5', 'Datos concatenacion para verificacion de Firma:' . $datos);
		$datos = str_replace(' ', '', $datos);
		$this->generarLog('5', 'Desencriptacion Datos:' . $datos . '--Firma:' . $firma);
		$result = $this->desencripta($datos, $firma);
		
		if ($result) {
			// log bueno
			$this->generarLog('1', 'Firma Valida :');
			return true;
		} else {
			// log malo
			$this->generarLog('2', 'Firma No Valida : ');
			return false;
		}
	}
    
    /**
     * Genera el XML3
     * 
     * @param int $Codigo codigo con resultado(0 = correcto; 1 = fallido)
     * @param string $Mensaje mensaje del resultado
     * @return string XML3
     */
	public function generaXml3($Codigo, $Mensaje)
    {
		$this->generarLog('5', 'Funcion Genera Xml3 Codigo:' . $Codigo . '--Mensaje:' . $Mensaje);
		$xml = "<?xml version='1.0' encoding='ISO-8859-1'?><Servipag><CodigoRetorno>$Codigo</CodigoRetorno><MensajeRetorno>$Mensaje</MensajeRetorno></Servipag>";
		$this->generarLog('5', 'Xml3 Generado:' . $xml);
		return $xml;
	}
    
    /**
     * Genera XML4
     * 
     * @param string $Xml4 XML con los datos
     * @param array $nodo array con los nodos correspondiente al XML4
     * @return boolean si el XML es valido o no
     */
	public function validaXml4($Xml4, $nodo)
    {
		$this->generarLog('4', '***********************************************************************************');
		$this->generarLog('4', 'Funcion Valida XML4 xml:' . $Xml4 . '--Nodos:' . implode(',', $nodo));
		
		if (strpos ($Xml4, '&lt;') !== false) {
			$this->generarLog('4', '---------Se Reemplaza &lt; a <');
			$Xml4 = str_replace('&lt;', '<', $Xml4);
			$this->generarLog('4', '--------- XML4 Resultante: ' . $Xml4);
		}
		
		if (strpos($Xml4, '&gt;') !== false) {
			$this->generarLog('4', '---------Se Reemplaza &gt; a >');
			$Xml4 = str_replace('&gt;', '>', $Xml4);
			$this->generarLog('4', '--------- XML4 Resultante: ' . $Xml4);
		}
		
		$datos = '';
		$firma = substr($Xml4, strrpos($Xml4, '<FirmaServipag>'), (strrpos($Xml4, '</FirmaServipag>') - strrpos($Xml4, '<FirmaServipag>')));
		$firma = str_replace('<FirmaServipag>', '', $firma);
        $firma = str_replace(' ', '+', $firma);
		$this->generarLog('4', 'Firma que contiene XML4 :' . $firma);
		asort($nodo);
        
		foreach ($nodo as $key => $val) {
			switch ($key) {
				case 'IdTrxServipag' :
					$datos = $datos . substr($Xml4, strrpos($Xml4, '<IdTrxServipag>'), (strrpos($Xml4, '</IdTrxServipag>') - strrpos($Xml4, '<IdTrxServipag>')));
					$datos = str_replace('<IdTrxServipag>', '', $datos);
					break;
				case 'IdTxCliente' :
					$datos = $datos . substr($Xml4, strrpos($Xml4, '<IdTxCliente>'), strrpos($Xml4, '</IdTxCliente>') - strrpos($Xml4, '<IdTxCliente>'));
					$datos = str_replace('<IdTxCliente>', '', $datos);
					break;
				case 'EstadoPago' :
					$datos = $datos . substr($Xml4, strrpos($Xml4, '<EstadoPago>'), strrpos($Xml4, '</EstadoPago>') - strrpos($Xml4, '<EstadoPago>'));
					$datos = str_replace('<EstadoPago>', '', $datos);
					break;
				case 'Mensaje' :
					$datos = $datos . substr($Xml4, strrpos($Xml4, '<Mensaje>'), strrpos($Xml4, '</Mensaje>') - strrpos($Xml4, '<Mensaje>'));
					$datos = str_replace('<Mensaje>', '', $datos);
					break;
			}
		}
        
		$this->generarLog('4', 'valor de concatenacion de Nodos XML4:' . $datos);
		$result = $this->desencripta($datos, $firma);
		if ($result) {
			// log bueno
			$this->generarLog('4', 'Firma Valida XML4 :');
			$this->generarLog('4', '*******************************************************************************************');
			return true;
		} else {
			// log malo
			$this->generarLog('4', 'Firma No Valida XML4 : ');
			$this->generarLog('4', '***********************************************************************************');
			return false;
		}
	}   
}