<?php
	require_once("../../configuracion/conectar.php");
	require_once("../../configuracion/define_variables.php");

	require_once("nuSoap/nusoap.php");
	require_once("funciones_globales/funciones_globales.php");

	$arrayNit           = explode('-', $_SESSION['NITEMPRESA']);
	$nit_empresa        = $arrayNit[0];
	$id_pais            = $_SESSION['PAIS'];
	$id_empresa         = $_SESSION['EMPRESA'];
	$id_sucursal        = $_SESSION['SUCURSAL'];
	$id_empleado        = $_SESSION['IDUSUARIO'];
	$nombre_empleado    = $_SESSION['NOMBREFUNCIONARIO'];
	$documento_empleado = $_SESSION['CEDULAFUNCIONARIO'];

	$sqlMetodos  = "SELECT COUNT(M.id) AS contMetodo,M.metodo,M.titulo,M.direccion,S.carpeta,M.archivo,M.propiedad
					FROM web_service_metodos AS M,
						web_service_software AS S
					WHERE M.id='$id_metodo'
						AND M.activo=1
						AND M.id_software=S.id
						AND S.id_empresa='$id_empresa'
						AND S.activo=1";
	$queryMetodos = mysql_query($sqlMetodos);

	$contMetodo      = mysql_result($queryMetodos, 0, 'contMetodo');
	$tituloMetodo    = mysql_result($queryMetodos, 0, 'titulo');
	$nombreMetodo    = mysql_result($queryMetodos, 0, 'metodo');
	$direccionMetodo = mysql_result($queryMetodos, 0, 'direccion');
	$carpetaMetodo   = mysql_result($queryMetodos, 0, 'carpeta');
	$archivoMetodo   = mysql_result($queryMetodos, 0, 'archivo');
	$propiedadMetodo = mysql_result($queryMetodos, 0, 'propiedad');

	// $sqlEmpresa   = "SELECT documento FROM empresas WHERE id='$id_empresa' AND activo=1";
	// $queryEmpresa = mysql_query($sqlEmpresa,$link);
	// $nit_empresa  = mysql_result($queryEmpresa, 0, 'documento');

	if($contMetodo == 0){ echo '<div style="margin:10px; font_weight:bold;">NO HAY METODOS DECLARADOS PARA EL PRESENTE WEB SERVICE</div>'; exit; }

	//=========================// WEBSERVICE //========================//
	//*****************************************************************//
	if($carpetaMetodo == "SIHO"){
		if($nombreMetodo == 'Movimiento'){ $arrayDataMetodos = array("tre_date_records" => $fecha_metodo); }
		else if($nombreMetodo == 'Terceros'){ $arrayDataMetodos = array("inv_property" => $propiedadMetodo, "inv_date_audit" => $fecha_metodo); }
		else if($nombreMetodo == 'Facturas'){ $arrayDataMetodos = array("inv_property" => "2", "inv_date_audit" => $fecha_metodo); }
		else if($tituloMetodo == 'Documento Interno'){ $arrayDataMetodos = array("propiedad" => $propiedadMetodo, "fecha" => $fecha_metodo); }
		else if($nombreMetodo == 'Movimiento_Facturas'){ $arrayDataMetodos = array("propiedad" => $propiedadMetodo, "fecha" => $fecha_metodo); }
		else if($nombreMetodo == 'Items_SIHOPOS_ERP'){ $arrayDataMetodos = array("propiedad" => $propiedadMetodo, "fecha" => $fecha_metodo); }
		else if($nombreMetodo == 'Movimientos_Diarios'){ $arrayDataMetodos = array("propiedad" => $propiedadMetodo, "fecha" => $fecha_metodo); }
		else if($nombreMetodo == 'Movimientos_Recibos_Caja'){ $arrayDataMetodos = array("propiedad" => $propiedadMetodo, "fecha" => $fecha_metodo); }
		else if($nombreMetodo == 'Documentos_contables'){ $arrayDataMetodos = array("propiedad" => $propiedadMetodo, "fecha" => $fecha_metodo); }
		// else if($nombreMetodo == 'Documentos_contables'){ $arrayDataMetodos = array("propiedad" => $propiedadMetodo, "fecha" => $fecha_metodo); }
		else{
			$responseWs = $responseWs[$nombreMetodo.'Result']['diffgram']['NewDataSet']['Table1'];
			require_once($carpetaMetodo."/".$archivoMetodo);
			exit;
		}
	}

	$objSoap = new nusoap_client($direccionMetodo, true,false,false,false,false,0,600);
	$errorWs = $objSoap->getError();

	if ($errorWs) { echo "<h2>Constructor error</h2><pre>".$errorWs."</pre>"; exit; }
	$responseWs = $objSoap->call($nombreMetodo, $arrayDataMetodos);
	// print_r($objSoap);

	if ($objSoap->fault) {
		echo "<h2>Fault</h2><pre>";
		print_r($responseWs);
		echo "</pre>";
	}
	else {
		$errorWs = $objSoap->getError();
		if ($errorWs) { echo "<h2>Error</h2><pre>".$errorWs."</pre>"; }
		else {
			$consecutivo = str_replace('-', '', $fecha_metodo);

			$arrayWs['nit_empresa']        = $nit_empresa;
			$arrayWs['codigo_sucursal']    = 1;

			$arrayWs['id_pais']            = $id_pais;
			$arrayWs['id_empresa']         = $id_empresa;
			$arrayWs['id_empleado']        = $id_empleado;
			$arrayWs['nombre_empleado']    = $nombre_empleado;
			$arrayWs['documento_empleado'] = $documento_empleado;

			//=====================// METODOS WEBSERVICE //====================//
			//*****************************************************************//
			if($carpetaMetodo == "SIHO"){
				$responseWs = $responseWs[$nombreMetodo.'Result']['diffgram']['NewDataSet']['Table1'];
				// echo json_encode($responseWs); exit;
				// print_r($responseWs); exit;
				// echo $carpetaMetodo."/".$archivoMetodo; exit;
				require_once($carpetaMetodo."/".$archivoMetodo);
			}
		}
	}

	//========================/ RESPONSE ERROR /======================//
	//****************************************************************//
	function response_error($arrayResponse, $salir=true){
		global $nombreMetodo, $fecha_metodo, $link;
		// if($arrayResponse['estado'] != 'true'){ echo'<div style="margin:10px; font_weight:bold;">- '.$arrayResponse['msj'].'</div>'; }
		// else{ echo'<div style="margin:10px; font_weight:bold;">'.$arrayResponse['msj'].'</div>'; }

		$estado = "Error";
		if($arrayResponse['estado'] != 'true'){ echo'<div style="margin:10px; font_weight:bold;">- '.$arrayResponse['msj'].'</div>'; }
		else{ echo'<div style="margin:10px; font_weight:bold;">'.$arrayResponse['msj'].'</div>'; $estado='Ok'; }

		if($nombreMetodo == 'Movimiento_Facturas'){
			$buscarString = strpos($arrayResponse['msj'],'ya ha sido ingresada');
			if($buscarString > 0){ $estado = 'Repetido'; }

			$sqlLog = "INSERT INTO web_service_log (detalle,detalle2,fecha_service,fecha_ejecucion,hora_ejecucion,metodo,estado)
						VALUES ('$arrayResponse[msj]','$arrayResponse[factura]','$fecha_metodo',NOW(),NOW(),'$nombreMetodo','$estado')";
			$queryLog = mysql_query($sqlLog,$link);
		}

		if($salir){ exit; }
	}
?>