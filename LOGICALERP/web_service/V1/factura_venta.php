<?php

	//==============================// VALIDATION //==============================//
	/******************************************************************************/

	// DOCUMENTO UNICO
	$sql   = "SELECT COUNT(id) AS cont FROM ventas_facturas WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND keyws='$arrayWs[keyws]' and activo=1";
	$query = mysql_query($sql,$link);
	$cont  = mysql_result($query,0,'cont');

	if ($cont>0) { response_error(array('estado' => 'error','msj'=>'ya existe una factura con la key relacionada!')); }

	// SUCURSAL
	$sqlSucursal   = "SELECT COUNT(id) AS cont,id FROM empresas_sucursales WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND nombre='$arrayWs[sucursal]' LIMIT 0,1";
	$querySucursal = mysql_query($sqlSucursal, $conexion);
	$contSucursal  = mysql_result($querySucursal,0,'cont');
	$id_sucursal   = mysql_result($querySucursal,0,'id');

	if($cont==0){ return array('estado' => 'false','msj'=>'No existe la sucursal en la empresa'); }

	// BODEGA
	$sqlBodega   = "SELECT COUNT(id) AS cont,id FROM empresas_sucursales_bodegas WHERE activo=1 AND id_sucursal=$id_sucursal AND nombre='$arrayWs[bodega]' LIMIT 0,1";
	$queryBodega = mysql_query($sqlBodega, $conexion);
	$contBodega  = mysql_result($queryBodega,0,'cont');
	$id_bodega   = mysql_result($queryBodega,0,'id');

	if($contBodega == 0){ return array('estado' => 'false','msj'=>'No existe la bodega en la empresa'); }

	// TERCERO
	$sql   = "SELECT COUNT(id) AS cont,id,codigo,tipo_identificacion,numero_identificacion,nombre_comercial
				FROM terceros
				WHERE activo=1
					AND id_empresa=$arrayWs[id_empresa]
					AND numero_identificacion='$arrayWs[nit_tercero]'";
	$query      = mysql_query($sql,$conexion);
	$nit        = mysql_result($query,0,'numero_identificacion');
	$cont       = mysql_result($query,0,'cont');
	$codigo     = mysql_result($query,0,'codigo');
	$tercero    = mysql_result($query,0,'nombre_comercial');
	$tipo_nit   = mysql_result($query,0,'tipo_identificacion');
	$id_tercero = mysql_result($query,0,'id');

	if($cont==0){  return array('estado' => 'false','msj'=>'No existe el tercero en la empresa'); }

	// CUENTA DE COBRO
	$sql   = "SELECT COUNT(id) AS cont, id,nombre,id_cuenta,cuenta,cuenta_niif
				FROM configuracion_cuentas_pago
				WHERE activo=1
					AND id_empresa=$arrayWs[id_empresa]
					AND cuenta='$arrayWs[cuenta_pago_colgaap]'";
	$query = mysql_query($sql,$conexion);
	$cont        = mysql_result($query,0,'cont');
	$id_pago     = mysql_result($query,0,'id');
	$nombre_pago = mysql_result($query,0,'nombre');
	$cuenta      = mysql_result($query,0,'cuenta');
	$id_cuenta   = mysql_result($query,0,'id_cuenta');
	$cuenta_niif = mysql_result($query,0,'cuenta_niif');

	if($cont==0){ return array('estado' => 'false','msj'=>'la cuenta de pago no existe en el sistema!'); }

	// CENTRO DE COSTOS
	if ($arrayWs['centro_costos']!='') {
		$sql   = "SELECT COUNT(id) AS cont,id FROM centro_costos WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND codigo='$arrayWs[centro_costos]'";
		$query = mysql_query($sql,$link);
		$cont    = mysql_result($query,0,'cont');
		$id_ccos = mysql_result($query,0,'id');

		if($cont==0){ response_error(array('estado' => 'error','msj'=>'no existe el centro de costos en el sistema!')); }
	}

	// IMPUESTOS
	$sql   = "SELECT COUNT(id) AS cont,id FROM centro_costos WHERE activo=1 AND id_empresa=$arrayWs[id_empresa]";
	$query = mysql_query($sql,$link);
	$cont    = mysql_result($query,0,'cont');
	$id_ccos = mysql_result($query,0,'id');

	if($cont==0){ response_error(array('estado' => 'error','msj'=>'no existe el centro de costos en el sistema!')); }



	// INSERTAR LA CABECERA DEL DOCUMENTO
	$random_documento = responseUnicoRanomico();
	$fieldFactura   = "(random,
						fecha_creacion,
						fecha_contabilizado,
						fecha_inicio,
						fecha_vencimiento,
						id_cliente,
						documento_vendedor,
						nombre_vendedor,
						id_usuario,
						id_configuracion_cuenta_pago,
						configuracion_cuenta_pago,
						id_cuenta_pago,
						cuenta_pago,
						cuenta_pago_niif,
						id_empresa,
						id_sucursal,
						id_bodega,
						id_centro_costo,
						estado,
						tipo)";
	$valueFactura = "('$random_documento',
					'$arrayWs[fecha_documento]',
					'$arrayWs[fecha_documento]',
					'$arrayWs[fecha_documento]',
					'$arrayWs[fecha_vencimiento]',
					'$id_tercero',
					'$arrayWs[documento_empleado]',
					'$arrayWs[nombre_empleado]',
					'$arrayWs[id_empleado]',
					'$id_pago',
					'$nombre_pago',
					'$id_cuenta',
					'$cuenta',
					'$cuenta_niif',
					'$arrayWs[id_empresa]',
					'$id_sucursal',
					'$id_ccos',
					'0',
					'Ws')";

	$sql   = "INSERT INTO ventas_facturas $fieldFactura VALUES $valueFactura";
	$query = mysql_query($sql,$link);

	if (!$query) { response_error(array('estado'=>'error', 'msj'=>'No se inserto el documento')); }

	$sqlSelectId  = "SELECT id FROM ventas_facturas WHERE random='$random_documento' LIMIT 0,1";
	$id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');
	$consecutivo_documento = 0;

	// INSERTAR LAS CUENTAS DEL DOCUMENTO
	$where    = "";
	$arrItems = array();
	foreach ($arrayWs['items'] as $element) {
		$where .= "codigo='$element[codigo]' OR ";
		$arrItems[] = $element['codigo'];
	}

	$arrItems = array_unique($arrItems);
	$itemsWs  = COUNT($arrItems);
	$where    = substr($valueInsertCuentasColgaap, 0, -3)

	$itemsBd     = array();
	$validateBd  = array();
	$contItemsBd = 0;

	$sqlItems   = "SELECT id_item, costos, precio_venta FROM inventario_totales WHERE activo=1 AND id_ubicacion='$arrayWs[id_bodega]' AND id_empresa='$arrayWs[id_empresa]' AND ($where)";
	$queryItems = mysql_query($sqlItems,$link);
	while ($row = mysql_fetch_object($queryItems)) {
		$itemsBd++;
		$validateBd[] = $row->codigo;
		$arrayItems["$row->codigo"] = array('id_item'=>$row->id_item, 'costo'=>$row->costos, 'precio'=>$row->precio_venta);

	}

	// ITEM QUE NO ESTAN EN LA BD
	if($itemsWs > $itemsBd){

	}

	$value = "";
	foreach ($arrayWs['items'] as $item) {

		if(!isset($arrayItems["$codigo"])){ 'error'; }

		$cant    = $item['cantidad'];
		$obs     = $item['obs'];
		$codigo  = $item['codigo'];
		$costo   = $arrayItems["$codigo"]["costo"];
		$precio  = $arrayItems["$codigo"]["precio"];
		$id_item = $arrayItems["$codigo"]["id_item"];

		$tdesc = (!isset($item['tdesc']))? 'ps': $item['tdesc'];
		$vdesc = (!isset($item['vdesc']))? 0: $item['vdesc'];

		if($tdesc!='ps' && $tdesc!='pr'){ $tdesc='ps'; }
		$tdesc = ($tdesc == 'pr')? 'porcentaje': 'pesos';

		if(is_nan($vdesc)){ $vdesc=0; }

		$value .= "($id_documento,
					$id_item,
					'$codigo',
					'$nombre',
					'$cant',
					'$cant',
					'$obs',
					'$tdesc',
					'$vdesc'),";
	}


	$value = substr($value, 0, -1);
	$sqlItems =	"(id_factura_venta,
					id_inventario,
					codigo,
					nombre,
					cantidad,
					saldo_cantidad,
					observaciones,
					tipo_descuento,
					descuento)
				VALUES $value";
	$queryItems = mysql_query($sqlItems);


	//=======================// CONTABILIZACION //=======================//
	/*********************************************************************/

	// function terminarGenerar($id,$id_empresa,$id_sucursal,$id_bodega,$observacion,ventas_facturas,id_factura_venta,ventas_facturas_inventario,$tablaRetenciones,$opcGrillaContable,$id_empresa,$idPlantilla,$fechaFactura,$link){
		global $saldoGlobalfactura, $saldoGlobalFacturaSinAbono, $cuentaPago, $cuentaPagoNiif;

		//===================================== VALIDACION CANTIDAD ARTICULOS INVENTARIO FACTURA =======================================//
		/********************************************************************************************************************************/
		validaCantidadArticulos($id,$id_sucursal,$id_bodega,'ventas_facturas_inventario','id_factura_venta',$link);

		//===================================== CREACION DE ARRAY DOCUMENTOS DE REFERENCIA =============================================//
		/********************************************************************************************************************************/
		$arraySindDoc      = '';
		$arrayRemisiones   = '';
		$arrayPedidos      = '';
		$arrayCotizaciones = '';


		$acumIdPedido     = '';		//CONDICIONAL GLOBAL WHERE SQL IDS PEDIDO
		$acumIdRemisiones = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES


		//================================================= VALIDACION FACTURA =========================================================//
		/********************************************************************************************************************************/
		$sqlFactBd     = "SELECT numero_factura, prefijo, estado, activo, id_cliente, id_configuracion_cuenta_pago, cuenta_pago, cuenta_pago_niif, cuenta_anticipo, valor_anticipo, id_centro_costo, exento_iva
							FROM ventas_facturas
							WHERE id='$id'";
		$queryFactBd   = mysql_query($sqlFactBd,$link);

		$newNumFactBd   = mysql_result($queryFactBd,0,'numero_factura');
		$newPrefijoFac  = mysql_result($queryFactBd,0,'prefijo');
		$estadoFactBd   = mysql_result($queryFactBd,0,'estado');
		$activoFactBd   = mysql_result($queryFactBd,0,'activo');
		$idCliente      = mysql_result($queryFactBd,0,'id_cliente');
		$idCcos         = mysql_result($queryFactBd,0,'id_centro_costo');

		$idCuentaPago   = mysql_result($queryFactBd,0,'id_configuracion_cuenta_pago');
		$cuentaPago     = mysql_result($queryFactBd,0,'cuenta_pago');
		$cuentaPagoNiif = mysql_result($queryFactBd,0,'cuenta_pago_niif');
		$exento_iva     = mysql_result($queryFactBd,0,'exento_iva');

		if(!$queryFactBd){
			echo '<script>
					alert("Aviso! No 1,\nSin conexion con la base de datos!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		// CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Venta'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);


		//=================================== UPDATE ======================================//
		/***********************************************************************************/

		// PARA LLENAR EL CAMPO NUMERO FACTURA COMPLETO, VERIFICAMOS SI HAY UN PREFIJO PARA CONCATENARLO SI NO NO
		$newPrefijoFac      = str_replace(" ", "", $newPrefijoFac);
		$consecutivoFactura = (strlen($newPrefijoFac) > 0)? $newPrefijoFac.' '.$newNumFactBd: $newNumFactBd;

		contabilizarSinPlantilla($arrayAnticipo,$arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$id_bodega,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);
		contabilizarSinPlantillaNiif($arrayAnticipo,$arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$id_bodega,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);

		// UPDATE CONSECUTIVO FACTURA ==>
		$updateEstadoFactura = "UPDATE ventas_facturas SET estado = 1, fecha_contabilizado=NOW() WHERE id='$id'";
		$queryEstadoFactura  = mysql_query($updateEstadoFactura,$link);

		if(is_nan($newNumFactBd) || $newNumFactBd == 0){

			// SELECT NUMERO DE FACTURA ==>
			$sqlNumActualFact = "SELECT numero_factura AS valor, prefijo
								FROM ventas_facturas
								WHERE activo=1 AND id_empresa='$id_empresa' AND id='$id'
								LIMIT 0,1";
			$queryActualFact = mysql_query($sqlNumActualFact,$link);

			$newPrefijoFac   = mysql_result($queryActualFact,0,'prefijo');
			$newNumFactBd    = mysql_result($queryActualFact,0,'valor');
			if (!$queryActualFact) {
				echo '<script>
						alert("Error! No 4,\nSin conexion con la base de datos!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			$newPrefijoFac      = str_replace(" ", "", $newPrefijoFac);
			$consecutivoFactura = (strlen($newPrefijoFac) > 0)? $newPrefijoFac.' '.$newNumFactBd: $newNumFactBd;


			$updateAsientosColgaap = "UPDATE asientos_colgaap
										SET consecutivo_documento='$consecutivoFactura',
											id_documento_cruce='$id',
											tipo_documento_cruce='FV',
											numero_documento_cruce='$consecutivoFactura'
										WHERE id_documento='$id'
											AND tipo_documento='FV'
											AND tipo_documento_extendido='Factura de Venta'
											AND id_empresa='$id_empresa'
											AND activo=1";
			$queryAsientosColgaap = mysql_query($updateAsientosColgaap,$link);

			$updateAsientosColgaap = "UPDATE asientos_niif
										SET consecutivo_documento='$consecutivoFactura',
											id_documento_cruce='$id',
											tipo_documento_cruce='FV',
											numero_documento_cruce='$consecutivoFactura'
										WHERE id_documento='$id'
											AND tipo_documento='FV'
											AND tipo_documento_extendido='Factura de Venta'
											AND id_empresa='$id_empresa'
											AND activo=1";
			$queryAsientosColgaap = mysql_query($updateAsientosColgaap,$link);
		}

		// ACTUALIZAMOS LA FACTURA PARA DAR POR TERMINADA
		$sqlGeneraFact   = "UPDATE ventas_facturas
							SET id_sucursal ='$id_sucursal',
								id_bodega ='$id_bodega',
								cuenta_pago = '$cuentaPago',
								cuenta_pago_niif = '$cuentaPagoNiif',
								observacion ='$observacion',
								fecha_inicio ='$fechaFactura',
								numero_factura_completo ='$consecutivoFactura',
								plantillas_id = '$idPlantilla',
								total_factura = '$saldoGlobalfactura',
								total_factura_sin_abono = '$saldoGlobalFacturaSinAbono'
							WHERE id = '$id' AND id_empresa = '$id_empresa'";
		$queryGeneraFact = mysql_query($sqlGeneraFact,$link);
		if ($queryGeneraFact) { actualizaCantidadArticulos($id,$id_sucursal,$id_bodega,'ventas_facturas_inventario','id_factura_venta','eliminar',$id_empresa,$link); }
		else{
			echo'<script>
					alert("Error!,\nNo se finalizo la factura\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				return;
		}


		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','FV','Factura de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

		echo'<script>
				//PONER EL NUMERO DE LA FACTURA EN EL TITULO DEL DOCUMENTO
				document.getElementById("titleDocumentoFacturaVenta").innerHTML="Factura de Venta<br>N. '.$consecutivoFactura.'";

				Ext.get("contenedor_'.$opcGrillaContable.'").load({
					url     : "bd/grillaContableBloqueada.php",
					scripts : true,
					nocache : true,
					params  :
					{
						id_factura_venta  : "'.$id.'",
						opcGrillaContable : "'.$opcGrillaContable.'",
						filtro_bodega     : "'.$id_bodega.'"
					}
				});
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';




































	return array('estado' => 'true');

	// ROLLBAK DEL PROCESO DE INSERT
	function rollback(ventas_facturas,$id_documento,$id_empresa,$link){

		$sql   = "DELETE FROM ventas_facturas WHERE id=$id_documento AND id_empresa=$arrayWs[id_empresa]";		// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
		$query = mysql_query($sql,$link);

		$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento  AND tipo_documento='FV' AND id_empresa=$id_empresa";		// ELIMINAR LOS ASIENTO COLGAAP
		$query = mysql_query($sql,$link);

		$sql   = "DELETE FROM asientos_niif WHERE id_documento=$id_documento  AND tipo_documento='FV' AND id_empresa=$id_empresa";		// ELIMINAR LOS ASIENTO NIIF
		$query = mysql_query($sql,$link);
	}

	function responseUnicoRanomico(){

		//Si es un Nuevo Documento -->
		$random1 = time();             //GENERA PRIMERA PARTE DEL ID UNICO

		$chars = array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
				'I', 'J', 'K', 'L', 'M', 'N', 'O',
				'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
				'X', 'Y', 'Z', '1', '2', '3', '4', '5',
				'6', '7', '8', '9', '0'
				);
		$max_chars = count($chars) - 1;
		srand((double) microtime()*1000000);
		$random2 = '';
		for($i=0; $i < 6; $i++){ $random2 = $random2 . $chars[rand(0, $max_chars)]; }

		$randomico = $random1.''.$random2; // ID UNICO
		return $randomico;
	}

?>
