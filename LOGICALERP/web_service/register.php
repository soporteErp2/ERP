<?php
	header('Access-Control-Allow-Origin: *'); //ACCESO DESDE MULTIPLES ORIGENES

	error_reporting(0);  //DESACTIVA REPORTES DE ERRORES
    ini_set('display_errors', 0);
	include("funciones_globales/funciones_globales.php");

	function insertUpdateTercero($arrayWs){ $nameArchivo = "insert_update_tercero"; return include("validate_ws.php"); }
	function insertNota($arrayWs){ $nameArchivo = "insert_nota"; return include("validate_ws.php"); }
	function insertDocumentos($arrayWs){ $nameArchivo = "insert_documentos"; return include("validate_ws.php"); }

	//====================== METODOS WEBSERVICE =====================//
	//***************************************************************//

	if(isset($_POST['dataJsonTerceros'])){
		$arrayWs = json_decode($_POST['dataJsonTerceros'],true);

		if(!$arrayWs){ echo json_encode(array("estado"=>"error", "msj"=>"No es un Schema Json Valido")); exit; }
		insertUpdateTercero($arrayWs);
	}
	else if(isset($_POST['dataJsonNota'])){
		$arrayWs = json_decode($_POST['dataJsonNota'],true);

		if(!$arrayWs){ echo json_encode(array("estado"=>"error", "msj"=>"No es un Schema Json Valido")); exit; }
		insertNota($arrayWs);
	}
	else if(!isset($debug)){
		include("nuSoap/nusoap.php");

		$objSoap = new soap_server();

		$objSoap->register("insertUpdateTercero");
		$objSoap->register("insertCuentas");
		$objSoap->register("insertDocumentos");

		// $objSoap->service($HTTP_RAW_POST_DATA);
		$post = file_get_contents("php://input");
		$objSoap->service($post);
	}
?>