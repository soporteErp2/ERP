<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../web_service/nuSoap/nusoap.php");
	$id_empresa = $_SESSION['EMPRESA'];

	// VALIDAR PARA LA IMPRESION DE FACTURA ELECTRONICA
	$sql="SELECT UUID FROM ventas_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id ";
	$query=$mysql->query($sql,$mysql->link);
	$UUID = $mysql->result($query,0,'UUID');
	if ($UUID=='' || is_null($UUID) ) {
		echo "<center><h1>No se puede generar el XML de esta factura, intentelo con una mas reciente</h1></center>";
		exit;
	}

	$objSoap = new nusoap_client("https://test.facse.net/conexion/comprobante.asmx?WSDL", true,false,false,false,false,0,600);
	$errorWs = $objSoap->getError();

	if ($errorWs) { echo "<h2>Constructor error</h2><pre>".$errorWs."</pre>"; exit; }
	$responseWs = $objSoap->call('ConsultarComprobante', array('idcomprobante' => $UUID ));

	if ($objSoap->fault) {
		echo "<h2>Fault</h2><pre>";
		print_r($responseWs);
		echo "</pre>";
	}
	else {
		$errorWs = $objSoap->getError();
		if ($errorWs) { echo "<h2>Error</h2><pre>".$errorWs."</pre>"; }
		else {
			$arrayResponse = json_decode($responseWs["ConsultarComprobanteResult"]);

			$nombreArchivo = $arrayResponse[2][1];
    		$cadenaXML = $arrayResponse[0][1];

			// CREAR EL XML Y DESCARGARLO
    		header("Content-disposition: attachment; filename=$nombreArchivo.xml");
			header ("Content-Type:text/xml");
			//output the XML data
			echo  base64_decode($cadenaXML);
			header("Expires: 0");
		}
	}

?>