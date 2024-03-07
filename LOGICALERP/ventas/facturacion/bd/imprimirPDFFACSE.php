<?php
	include("../../../web_service/nuSoap/nusoap.php");

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
			// echo "<br>";
			$arrayResponse = json_decode($responseWs["ConsultarComprobanteResult"]);
			// print_r($arrayResponse);

			$cadenaPDF     = $arrayResponse[1][1];
			$nombreArchivo = $arrayResponse[2][1];

			// CREAR EL PDF Y DESCARGARLO
			file_put_contents($nombreArchivo.".pdf", base64_decode($cadenaPDF));
			header("Content-Type: application/force-download");
    		header("Content-Disposition: attachment; filename=\"$nombreArchivo.pdf\"");
    		readfile($nombreArchivo.".pdf");

    		// ELIMINAR EL PDF GENERADO
    		unlink($nombreArchivo.".pdf");

		}
	}

?>