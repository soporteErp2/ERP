<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../config_var_global.php");
	include("../../facturacion/bd/contabilizar_bd.php");
	include("../../facturacion/bd/contabilizar_niif_bd.php");
	include("../../bd/functions_body_article.php");
	include_once("../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");

	// if ($id_empresa==1 || $id_empresa==47) {
	// 	$fecha_actual = strtotime(date('Y-m-d' ) );
	// 	$fecha_entrada = strtotime("2018-02-12");
	// 	if($fecha_actual >= $fecha_entrada ){
	// 		echo "<script>
	// 				alert(\"Aviso\\nFacturacion cerrada por implementacion de facturacion electronica\");
	// 				console.log('bloqueo $fecha_actual > $fecha_entrada');
	// 			</script>";
	// 		exit;
	// 	}
	// }

	$cuentaPago                 = 0;
	$cuentaPagoNiif             = 0;
	$saldoGlobalfactura         = 0;
	$saldoGlobalFacturaSinAbono = 0;

	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	
	switch ($opc){
		case 'terminarGenerar':
			verificaEstadoDocumento($id,$opcGrillaContable,$link);
			terminarGenerar($id,$id_empresa,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$id_empresa,$idPlantilla,$fechaFactura,$link);
			break;

		case 'cargarDocuementoNewFactura':
			verificaEstadoDocumento($id_factura,$opcGrillaContable,$link);
			cargarDocuementoNewFactura($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar);
			break;

		case 'agregarDocumento':
			verificaEstadoDocumento($id_factura,$opcGrillaContable,$link);
			agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'eliminaDocReferencia':
			verificaEstadoDocumento($id_factura,$opcGrillaContable,$link);
			eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'ventanaUpdateFecha':
			ventanaUpdateFecha();
			break;

		case 'validateUpdateFecha':
			verificaEstadoDocumento($idFacturaVenta,$opcGrillaContable,$link);
			validateUpdateFecha($fecha,$usuario,$password,$id_empresa,$idFacturaVenta,$opcGrillaContable,$link);
			break;

		case 'QuickFechaUpdate':
			validateUpdateFecha($fecha,$usuario,$password,$id_empresa,$idFacturaVenta,$opcGrillaContable,$link);
			break;

		case 'UpdateCuentaPago':
			verificaEstadoDocumento($id,$opcGrillaContable,$link);
			UpdateCuentaPago($id,$idCuentaPago,$link);
			break;

		case 'UpdateIdPlantilla':
			verificaEstadoDocumento($id,$opcGrillaContable,$link);
			UpdateIdPlantilla($id,$idPlantilla,$link);
			break;

		case 'validateCcos':
			validateCcos($codigoCcos,$id_empresa,$link);
			break;

		case 'updateCcos':
			verificaEstadoDocumento($id_factura,$opcGrillaContable,$link);
			updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id_factura,$id_empresa,$link);
			break;

		case 'updateSucursalCliente':
			verificaEstadoDocumento($id_factura,$opcGrillaContable,$link);
			updateSucursalCliente($id_factura,$id_scl_cliente,$nombre_scl_cliente,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'busquedaTerceroPaginacion':
			busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa);
			break;

		case 'cancelar_ccos':
			cancelar_ccos($opcGrillaContable,$id_documento,$id_empresa,$link);
			break;

		//=============================== ANTICIPOS ==============================//
		case 'ventanaValorAnticipo':
			ventanaValorAnticipo($idFactura,$valor_Factura,$id_cuenta_anticipo,$id_anticipo,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'guardarAnticipo':
			guardarAnticipo($valor_Factura,$contFila,$idFactura,$valor_anticipo,$id_cuenta_anticipo,$id_anticipo,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'cancelarAnticipoFactura':
			cancelarAnticipoFactura($idFactura,$id_empresa,$link);
			break;

		case 'filtro_anticipo':
			filtro_anticipo($idFactura,$idTercero,$opcGrilla,$id_empresa,$link);
			break;

		case 'guardarOC':
			guardarOC();
			break;

		//============================ GRUPOS DE ITEMS ===========================//
		case 'saveGroup':
			saveGroup($codigo,$nombre,$cantidad,$observaciones,$id_bodega,$id_documento,$id_impuesto,$nombre_impuesto,$valor_impuesto,$codigo_dian,$opcGrillaContable,$id_sucursal,$id_empresa,$costo_unitario,$descuento,$valor_impuesto,$mysql);
			break;
		case 'updateGroup':
			updateGroup($codigo,$nombre,$cantidad,$observaciones,$id_bodega,$id_documento,$opcGrillaContable,$id_row,$id_sucursal,$id_empresa,$id_impuesto,$costo_unitario,$descuento,$valor_impuesto,$mysql);
			break;
		case 'deleteGrupo':
			deleteGrupo($id_row,$opcGrillaContable,$id_documento,$id_empresa,$mysql);
			break;
		case 'agregarTodosItemsGrupo':
			agregarTodosItemsGrupo($id_documento,$id_impuesto,$id_grupo,$id_empresa,$mysql);
			break;
		case 'agregarItemsGrupo':
			agregarItemsGrupo($id,$codigo,$nombre,$id_documento,$id_grupo,$id_empresa,$mysql);
			break;
		case 'eliminarItemGrupo':
			eliminarItemGrupo($id_documento,$opcGrillaContable,$id_grupo,$id_inventario,$id_empresa,$mysql);
			break;

		//=========================== ANEXAR DOCUMENTOS ==========================//
		case "downloadFile":
			downloadFile($nameFile,$ext,$id,$id_empresa,$id_host);
			break;
		case "consultaSizeDocumento":
			consultaSizeDocumento($nameFile,$ext,$id,$id_host);
			break;
		case "ventanaViewDocumento":
			ventanaViewDocumento($nameFile,$ext,$id,$id_host);
			break;
		case 'deleteDocumentoVentasFacturas':
		  deleteDocumentoVentasFacturas($id_host,$idDocumento,$nombre,$ext,$link);
		  break;
	}

	function terminarGenerar($id,$id_empresa,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$id_empresa,$idPlantilla,$fechaFactura,$link){
		global $saldoGlobalfactura, $saldoGlobalFacturaSinAbono, $cuentaPago, $cuentaPagoNiif;

		// VALIDAR QUE LA FACTURA TENGA UNA SUCURSAL Y UNA RESOLUCION
		$sql="SELECT id_sucursal_cliente,id_configuracion_resolucion FROM ventas_facturas WHERE id='$id';";
		$query=mysql_query($sql,$link);
		$id_row = mysql_result($query,0,'id_sucursal_cliente');
		$id_resolucion = mysql_result($query,0,'id_configuracion_resolucion');
		if ($id_row == 0 || $id_row=='' ){
			echo'<script>
					alert("Advertencia!\nDebe seleccionar la sucursal del cliente");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		if ($id_resolucion == 0 || $id_resolucion=='' ){
			echo'<script>
					alert("Advertencia!\nDebe seleccionar la resolucion de facturacion");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//================== CALCULAR VENCIMIENTO DE LA RESOLUCION =================//
		$sql = "SELECT DATE_ADD(fecha_resolucion,INTERVAL 2 YEAR) AS fecha_resolucion_fin,numero_final_resolucion
						FROM ventas_facturas_configuracion
				 		WHERE activo = 1
		 				AND id_empresa = '$_SESSION[EMPRESA]'
		 				AND id= $id_resolucion;
						";

		$query                   = mysql_query($sql,$link);
		$fecha_resolucion_fin    = mysql_result($query,0,'fecha_resolucion_fin');
		$numero_final_resolucion = mysql_result($query,0,'numero_final_resolucion');

	  	$fecha_diferencia = date_diff(date_create(date('Y-m-d')),date_create($fecha_resolucion_fin));
	  	$fecha_diferencia = $fecha_diferencia->format("%r%a");

		//============ CALCULAR CONSECUTIVOS RESTANTES DE LA RESOLUCION ============//
		$sql = "SELECT numero_factura,numero_factura_completo
						FROM ventas_facturas
						WHERE activo = 1
						AND id_empresa = '$_SESSION[EMPRESA]'
						AND id_sucursal = '$_SESSION[SUCURSAL]'
						AND id_configuracion_resolucion= $id_resolucion
						ORDER BY numero_factura DESC
						LIMIT 0,1;";

		$query                   = mysql_query($sql,$link);
		$numero_factura_actual   = mysql_result($query,0,'numero_factura');
		$numero_factura_completo = mysql_result($query,0,'numero_factura_completo');

		$resolucion_diferencia 	= $numero_final_resolucion - $numero_factura_actual;

		if($resolucion_diferencia == 0 && $numero_factura_completo == ""){
			echo '<script>
							alert("\u00a1Advertencia!\nNo puede generar mas facturas, porque ya ha alcanzado el limite de consecutivos permitidos.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			exit;
		}

		//===================================== VALIDACION CANTIDAD ARTICULOS INVENTARIO FACTURA =======================================//
		/********************************************************************************************************************************/
		validaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$link);

		//===================================== CREACION DE ARRAY DOCUMENTOS DE REFERENCIA =============================================//
		/********************************************************************************************************************************/
		$arraySindDoc      = '';
		$arrayRemisiones   = '';
		$arrayPedidos      = '';
		$arrayCotizaciones = '';

		$contRemisiones = 0;
		$contPedido     = 0;
		$contCotizacion = 0;
		$contSinDoc     = 0;

		$acumIdCotizacion = '';		//CONDICIONAL GLOBAL WHERE SQL IDS COTIZACION
		$acumIdPedido     = '';		//CONDICIONAL GLOBAL WHERE SQL IDS PEDIDO
		$acumIdRemisiones = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

		//ACTUALIZACION SALDOS DE INVENTARTIO
		$sqlSaldoFactura   = "UPDATE ventas_facturas_inventario SET saldo_cantidad=cantidad WHERE id_factura_venta='$id'";
		$querySaldoFactura = mysql_query($sqlSaldoFactura,$link);

		$sqlDocumentoAdjunto = "SELECT id_consecutivo_referencia AS id_referencia, nombre_consecutivo_referencia AS nombre_referencia
								FROM ventas_facturas_inventario
								WHERE id_factura_venta='$id' AND activo=1
								GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";
		$queryDocumentoAdjunto = mysql_query($sqlDocumentoAdjunto,$link);
		while($rowDoc = mysql_fetch_array($queryDocumentoAdjunto)){

			$id_referencia     = $rowDoc['id_referencia'];
			$nombre_referencia = $rowDoc['nombre_referencia'];
			$arrayResult       = Array ( 'id_referencia' => $id_referencia, 'nombre_referencia' => $nombre_referencia);

			if($id_referencia > 0){																								//CON DOCUMENTO DE REFERENCIA
				if($nombre_referencia == 'Remision'){ $contRemisiones++; $arrayRemisiones[$contRemisiones] = $arrayResult; }
				else if($nombre_referencia == 'Pedido'){ $contPedido++; $arrayPedidos[$contPedido] = $arrayResult; }
				else if($nombre_referencia == 'Cotizacion'){ $contCotizacion++; $arrayCotizaciones[$contCotizacion] = $arrayResult; }
			}
			else{ $contSinDoc++; $arraySindDoc[$contSinDoc][$id_referencia] = $nombre_referencia; }								//SIN DOCUMENTO DE REFERENCIA
		}

		//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
		/**********************************************************************************/
		if($contCotizacion>0){																									//VALIDACION COTIZACION
			for($cont=1; $cont<=$contCotizacion; $cont++) {
				$acumIdCotizacion .= ($acumIdCotizacion=='')? "id=":" OR id=";
				$acumIdCotizacion .= $arrayCotizaciones[$cont]['id_referencia'];
			}

			$sqlEstadoCotizacion   = "SELECT consecutivo,estado,activo FROM ventas_cotizaciones WHERE id_empresa=$id_empresa AND ($acumIdCotizacion)";
			$queryEstadoCotizacion = mysql_query($sqlEstadoCotizacion);
			while ($rowEstadoCotizacion = mysql_fetch_array($queryEstadoCotizacion)) {
				if($rowEstadoCotizacion['estado']==3){
					echo'<script>
							alert("Error!\nLa cotizacion codigo '.$rowEstadoCotizacion['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}
		}

		if($contPedido>0){																										//VALIDACION PEDIDO
			for($cont=1; $cont<=$contPedido; $cont++) {
				$acumIdPedido .= ($acumIdPedido=='')? "id=":" OR id=";
				$acumIdPedido .= $arrayPedidos[$cont]['id_referencia'];
			}

			$sqlEstadoPedido   = "SELECT consecutivo,estado,activo FROM ventas_pedidos WHERE id_empresa=$id_empresa AND ($acumIdPedido)";
			$queryEstadoPedido = mysql_query($sqlEstadoPedido);
			while ($rowEstadoPedido = mysql_fetch_array($queryEstadoPedido)) {
				if($rowEstadoPedido['estado']==3){
					echo'<script>
							alert("Error!\nEl pedido codigo '.$rowEstadoPedido['consecutivo'].' esta Cancelado\nrestaure el documento o elimine los articulos relacionados a esta para continuar.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}

			// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES AL PEDIDO ==>
			$sqlValidaSaldo = " SELECT COUNT(TI.id) AS cont_validate_saldo
								FROM ventas_facturas_inventario AS TI, ventas_pedidos_inventario AS TS
								WHERE TI.id_factura_venta='$id' AND TI.activo = 1 AND TS.activo = 1 AND TI.nombre_consecutivo_referencia='Pedido' AND TS.id=TI.id_tabla_inventario_referencia
									AND TI.cantidad > TS.saldo_cantidad
								GROUP BY TI.id";
			$contValidateSaldo = mysql_result(mysql_query($sqlValidaSaldo,$link),0,'cont_validate_saldo');
			if($contValidateSaldo > 0){
				echo '<script>
						alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en el pedido que se adjunto en la presente Factura");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				return;
			}
		}

		if($contRemisiones>0){ 																									// VALIDACION REMISION
			for($cont=1; $cont<=$contRemisiones; $cont++){
				$acumIdRemisiones .= ($acumIdRemisiones=='')? "id=":" OR id=";
				$acumIdRemisiones .= $arrayRemisiones[$cont]['id_referencia'];
			}

			$sqlEstadoRemision   = "SELECT consecutivo,estado,activo FROM ventas_remisiones WHERE id_empresa=$id_empresa AND ($acumIdRemisiones)";
			$queryEstadoRemision = mysql_query($sqlEstadoRemision);
			while ($rowEstadoRemision = mysql_fetch_array($queryEstadoRemision)) {
				if($rowEstadoRemision['estado']==3){
					echo '<script>
							alert("Error!\nLa remision codigo '.$rowEstadoRemision['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>'; exit;
				}
			}


			// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES A LA REMISION ==>
			$sqlValidaSaldo = " SELECT COUNT(TI.id) AS cont_validate_saldo
								FROM ventas_facturas_inventario AS TI, ventas_remisiones_inventario AS TS
								WHERE TI.id_factura_venta='$id' AND TI.activo = 1 AND TS.activo = 1 AND TI.nombre_consecutivo_referencia='Remision' AND TS.id=TI.id_tabla_inventario_referencia
									AND TI.cantidad > TS.saldo_cantidad
								GROUP BY TI.id";
			$contValidateSaldo = mysql_result(mysql_query($sqlValidaSaldo,$link),0,'cont_validate_saldo');
			if($contValidateSaldo > 0){
				echo '<script>
						alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en la remision que se adjunto en la presente factura");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					return;
			}
		}


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

		// $cuentaAnticipo = mysql_result($queryFactBd,0,'cuenta_anticipo');
		// $valorAnticipo  = mysql_result($queryFactBd,0,'valor_anticipo');
		$exento_iva     = mysql_result($queryFactBd,0,'exento_iva');

		$arrayAnticipo  = array('cuenta' => $cuentaAnticipo, 'valor' => $valorAnticipo);

		if(!$queryFactBd){
			echo '<script>
					alert("Aviso! No 1,\nSin conexion con la base de datos!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else if($estadoFactBd == 3){			// ERROR QUERY
			echo '<script>
					alert("Aviso! No 2,\nLa factura ha sido cancelada");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}
		else if ($activoFactBd == 0){		// FACTURA CANCELADA
			echo '<script>
					alert("Aviso! No 3,\nLa factura ha sido eliminada");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}		// FACTURA ELIMINADA

		//CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Venta'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

		//ANTICIPOS
		$arrayAnticipo = array('total'=>0);
		$sqlAnticipo   = "SELECT id,id_cuenta_anticipo,id_documento_anticipo,tipo_documento_anticipo,consecutivo_documento_anticipo,cuenta_colgaap,cuenta_niif,id_tercero,nit_tercero,tercero,valor
							FROM anticipos
							WHERE id_documento='$id'
								AND tipo_documento='FV'
								AND id_empresa='$id_empresa'
								AND activo=1
								AND valor>0";
		$queryAnticipo =  mysql_query($sqlAnticipo,$link);
		while ($rowAnticipo = mysql_fetch_assoc($queryAnticipo)) {

			$idAnticipo  = $rowAnticipo['id'];

			$arrayAnticipo['total'] += $rowAnticipo['valor']*1;

			$arrayAnticipo['anticipos'][$idAnticipo]['valor']          = $rowAnticipo['valor'];
			$arrayAnticipo['anticipos'][$idAnticipo]['id_tercero']     = $rowAnticipo['id_tercero'];
			$arrayAnticipo['anticipos'][$idAnticipo]['cuenta_niif']    = $rowAnticipo['cuenta_niif'];
			$arrayAnticipo['anticipos'][$idAnticipo]['cuenta_colgaap'] = $rowAnticipo['cuenta_colgaap'];
			$arrayAnticipo['anticipos'][$idAnticipo]['consecutivo']    = $rowAnticipo['consecutivo_documento_anticipo'];
			$arrayAnticipo['anticipos'][$idAnticipo]['id_anticipo']    = $rowAnticipo['id_documento_anticipo'];
			$arrayAnticipo['anticipos'][$idAnticipo]['tipo_documento'] = $rowAnticipo['tipo_documento_anticipo'];
		}

		if($arrayAnticipo['total'] > 0){

			$sqlValidaAnticipos = "SELECT COUNT(A.id) AS contAnticipo
									FROM recibo_caja_cuentas AS C, anticipos AS A
									WHERE C.id=A.id_cuenta_anticipo
										AND C.activo=1
										AND A.activo=1
										AND A.id_documento='$id'
										AND A.tipo_documento='FV'
										AND C.saldo_pendiente < A.valor";
			$queryValidaAnticipos = mysql_query($sqlValidaAnticipos,$link);
			$conFailAnticipo = mysql_result($queryValidaAnticipos, 0, 'contAnticipo');

			if(!$queryValidaAnticipos || $conFailAnticipo>0 && user_permisos(301)=="false"){
				echo '<script>
						alert("Aviso,\nHay anticipos que superan el valor registrado, por favor realice las correcciones respectivas.");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

		}

		//=================================== UPDATE ======================================//
		/***********************************************************************************/

		//PARA LLENAR EL CAMPO NUMERO FACTURA COMPLETO, VERIFICAMOS SI HAY UN PREFIJO PARA CONCATENARLO SI NO NO
		$newPrefijoFac      = str_replace(" ", "", $newPrefijoFac);
		$consecutivoFactura = (strlen($newPrefijoFac) > 0)? $newPrefijoFac.' '.$newNumFactBd: $newNumFactBd;

		if($idPlantilla > 0){ 		// CONTABILIZACION FACTURA CON PLANTILLA
			contabilizarConPlantilla($fechaFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$idPlantilla,$id,$idCliente,$exento_iva,$link);
			contabilizarConPlantillaNiif($fechaFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$idPlantilla,$id,$idCliente,$exento_iva,$link);
		}
		else {						// CONTABILIZACION FACTURA SIN PLANTILLA
			contabilizarSinPlantilla($arrayAnticipo,$arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);
			contabilizarSinPlantillaNiif($arrayAnticipo,$arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);
		}

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
		$saldoGlobalfactura         = round($saldoGlobalfactura,$_SESSION['DECIMALESMONEDA']);
		$saldoGlobalFacturaSinAbono = round($saldoGlobalFacturaSinAbono,$_SESSION['DECIMALESMONEDA']);
		//ACTUALIZAMOS LA FACTURA PARA DAR POR TERMINADA
		$sqlGeneraFact   = "UPDATE ventas_facturas
							SET id_sucursal ='$id_sucursal',
								id_bodega ='$idBodega',
								cuenta_pago = '$cuentaPago',
								cuenta_pago_niif = '$cuentaPagoNiif',
								observacion ='$observacion',
								fecha_inicio ='$fechaFactura',
								numero_factura_completo ='$consecutivoFactura',
								plantillas_id = '$idPlantilla',
								total_factura = '$saldoGlobalfactura',
								total_factura_sin_abono = '$saldoGlobalFacturaSinAbono',
								valor_anticipo = " .$arrayAnticipo['total']. "
							WHERE id = '$id' AND id_empresa = '$id_empresa'";
		$queryGeneraFact = mysql_query($sqlGeneraFact,$link);
		
		actualizaCantidadArticulos($id,"salida","Generar");


		if($contCotizacion>0){																				//UPDATE COTIZACIONES
			$sqlUpdateEstado = "UPDATE ventas_cotizaciones SET estado=2 WHERE id_empresa=$id_empresa AND ($acumIdCotizacion)";
			mysql_query($sqlUpdateEstado,$link);
		}

		if($contPedido>0){																					//UPDATE PEDIDOS
			$sqlUpdateSaldos = "UPDATE ventas_pedidos_inventario AS VPI
									INNER JOIN ventas_facturas_inventario AS VRI ON VPI.id=VRI.id_tabla_inventario_referencia
									SET VPI.saldo_cantidad= (VPI.saldo_cantidad-VRI.cantidad)
									WHERE VRI.id_factura_venta='$id' AND VRI.nombre_consecutivo_referencia='Pedido' AND VRI.id_tabla_inventario_referencia=VPI.id AND VPI.activo = 1  AND VRI.activo = 1";
			mysql_query($sqlUpdateSaldos,$link);

			//UPDATE TOTAL ARTICULOS PENDIENTES FACTURAR EN PEDIDO
			for($cont=1; $cont<=$contPedido; $cont++) {
				$id_pedido = $arrayPedidos[$cont]['id_referencia'];
				$sqlUpdatePendientes = "UPDATE ventas_pedidos
										SET unidades_pendientes=(
												SELECT SUM(saldo_cantidad)
												FROM ventas_pedidos_inventario
												WHERE id_pedido_venta= '$id_pedido'),
											estado=2
										WHERE id=$id_pedido";
				mysql_query($sqlUpdatePendientes,$link);
			}
		}

		if($contRemisiones>0){																				//UPDATE REMISIONES
			$sqlUpdateSaldos = "UPDATE ventas_remisiones_inventario AS VRI
								INNER JOIN ventas_facturas_inventario AS VFI ON VRI.id=VFI.id_tabla_inventario_referencia
								SET VRI.saldo_cantidad= (VRI.saldo_cantidad-VFI.cantidad)
								WHERE VFI.id_factura_venta='$id' AND VFI.nombre_consecutivo_referencia='Remision' AND VFI.id_tabla_inventario_referencia=VRI.id AND VRI.activo = 1  AND VFI.activo = 1";
			mysql_query($sqlUpdateSaldos,$link);

			//UPDATE TOTAL ARTICULOS PENDIENTES FACTURAR EN REMISION
			for($cont=1; $cont<=$contRemisiones; $cont++) {
				$id_remision = $arrayRemisiones[$cont]['id_referencia'];
				$sqlUpdatePendientes = "UPDATE ventas_remisiones
										SET pendientes_facturar=(
												SELECT SUM(saldo_cantidad)
												FROM ventas_remisiones_inventario
												WHERE id_remision_venta= '$id_remision'),
											estado=2
										WHERE id=$id_remision";
				mysql_query($sqlUpdatePendientes,$link);
			}
		}

		// global $arrayDatos,$mysql;
		// $arrayDatos["id_documento"] = $id;
		// logInventario($arrayDatos,$mysql);

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
						filtro_bodega     : "'.$idBodega.'"
					}
				});
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
	}

	//================= VALIDACION CANTIDAD DE ARTICULOS A DESCONTAR DE INVENTARIO ===================//
	function validaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$link){

		$cantidadPermitida = 0;
		$cantidadMayor     = 0;
		$sumaCantidad      = 0;
		$codigo            = 0;
		$sqlArticulo = "SELECT
							TI.id_inventario,
							Sum(TI.cantidad) AS suma_cantidad,
							TI.nombre,
							TIT.id_item,
							TIT.cantidad,
							TIT.codigo AS codigo
						FROM
							$tablaInventario AS TI,
							inventario_totales AS TIT
						WHERE TI.activo = 1
							AND TI.$idTablaPrincipal = '$id'
							AND TI.nombre_consecutivo_referencia<>'Remision'
							AND TIT.id_item      = TI.id_inventario
							AND TIT.id_sucursal  = '$id_sucursal'
							AND TIT.id_ubicacion = '$idBodega'
							AND TIT.inventariable = 'true'
						GROUP BY TI.id_inventario
						HAVING Sum(TI.cantidad) > TIT.cantidad
						LIMIT 0,1 ";

		$queryArticulo     = mysql_query($sqlArticulo,$link);
		$sumaCantidad      = mysql_result($queryArticulo,0,'suma_cantidad');
		$cantidadPermitida = mysql_result($queryArticulo,0,'cantidad');
		$codigo            = mysql_result($queryArticulo,0,'codigo');

		if(!$queryArticulo){
			echo'<script>
					alert("Error.\nNo se Validaron los articulos al inventario");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}
		else if (trim($codigo) !== '' && trim($codigo) !== '0' && user_permisos(181)==='false'){
			echo'<script>
					alert("Aviso.\nHay '.round($sumaCantidad).' unidades del inventario codigo '.$codigo.', lo maximo permitido en ventas de este inventario es '.$cantidadPermitida.' unidades.");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}
	}

	//======================= FUNCION SUMA O RESTA INVENTARIOS TOTALES Y SALDO ==========================//
	// function actualizarCantidadArticulos($id_factura,$accion_inventario,$accion_documento){
	function actualizaCantidadArticulos($id_documento,$accion_inventario,$accion_documento){
		global $mysql;
		// consultar la informacion del documento
		$sql = "SELECT id_sucursal,sucursal,id_bodega,bodega,id_empresa,numero_factura_completo AS consecutivo,fecha_inicio
		FROM ventas_facturas WHERE id=$id_documento";
		
		$query = $mysql->query($sql);
		$id_empresa  = $mysql->result($query,0,"id_empresa");
		$id_sucursal = $mysql->result($query,0,"id_sucursal");
		$sucursal    = $mysql->result($query,0,"sucursal");
		$id_bodega   = $mysql->result($query,0,"id_bodega");
		$bodega      = $mysql->result($query,0,"bodega");
		$consecutivo = $mysql->result($query,0,"consecutivo");
		$fecha       = $mysql->result($query,0,"fecha_inicio");

		// consultar los items de ese documento pero solo los que generan movimiento de inventario
		$sql = "SELECT 
						id_inventario AS id,
						codigo,
						nombre,
						nombre_unidad_medida AS unidad_medida,
						cantidad_unidad_medida AS cantidad_unidades,
						costo_inventario AS costo,
						cantidad
					FROM ventas_facturas_inventario 
					WHERE id_factura_venta=$id_documento
					AND activo=1 
					AND inventariable='true' ";
		$query = $mysql->query($sql);
		$index = 0;
		$items = array();
		while ($row = $mysql->fetch_assoc($query)) {
			$items[$index]                = $row;
			$items[$index]["empresa_id"]  = $id_empresa;
			$items[$index]["empresa"]     = NULL;
			$items[$index]["sucursal_id"] = $id_sucursal;
			$items[$index]["sucursal"]    = $sucursal;
			$items[$index]["bodega_id"]   = $id_bodega;
			$items[$index]["bodega"]      = $bodega;
			
			$index++;
		}
		// GENERAR EL MOVIMIENTO DE INVENTARIO
		include '../../../inventario/Clases/Inventory.php';

		$params = [ 
			"documento_id"          => $id_documento,
			"documento_tipo"        => "FV",
			"documento_consecutivo" => $consecutivo,
			"fecha"                 => $fecha,
			"accion_inventario"     => $accion_inventario,
			"accion_documento"      => $accion_documento,    // accion del documento, generar, editar, etc
			"items"                 => $items,
			"mysql"                 => $mysql
		];
		$obj = new Inventario_pp();
		$process = $obj->UpdateInventory($params);
		var_dump($params);
		// if ($opc=='eliminar'){
		// 	$sql   = "UPDATE inventario_totales AS IT, (
		// 					SELECT SUM(cantidad) AS total_factura_venta, id_inventario AS id_item
		// 					FROM ventas_facturas_inventario
		// 					WHERE id_factura_venta='$idFactura'
		// 						AND activo=1
		// 						AND inventariable='true'
		// 						AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )
		// 					GROUP BY id_inventario) AS VFI
		// 				SET IT.cantidad=IT.cantidad-VFI.total_factura_venta,
		// 					IT.id_documento_update          = '$idFactura',
		// 					IT.tipo_documento_update        = 'Factura Venta',
		// 					IT.consecutivo_documento_update = ''
		// 				WHERE IT.id_item=VFI.id_item
	 // 						AND IT.activo = 1
	 // 						AND IT.id_ubicacion = '$idBodega'";

		// 	$query = mysql_query($sql,$link);
		// 	if(!$query){
		// 		echo'<script>
		// 				alert("Error.\nNo se contabilizo los articulos al inventario");
		// 				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
		// 			</script>';
		// 			exit;
		// 	}
		// }
		// else if ($opc=='agregar'){

			// $sql   = "SELECT SUM(cantidad) AS cantidad_total,
			// 				id_inventario AS id_item,
			// 				costo_inventario AS costo_unitario,
			// 				SUM(cantidad * costo_inventario) AS costo_total
			// 				FROM ventas_facturas_inventario
			// 				WHERE id_factura_venta='$idFactura'
			// 					AND activo=1
			// 					AND inventariable='true'
			// 					AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )
			// 				GROUP BY id_inventario ";

		// 	$query = mysql_query($sql,$link);
		// 	if(!$query){
		// 		echo'<script>
		// 				alert("Error.\nNo se contabilizo los articulos al inventario");
		// 				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
		// 			</script>';
		// 			exit;
		// 	}
		// }
		// include '../../../funciones_globales/Clases/ClassInventory.php';
		// global $mysql;
		// $objectInventory = new ClassInventory($mysql);

		// $params['sqlItems']              = $sql;
		// $params['id_bodega']             = $idBodega;
		// $params['event']                 = ($opc=='agregar')? 'add' : 'remove' ;
		// $params['id_documento']          = $idFactura;
		// $params['nombre_documento']      = "FacturaVenta";
		// $params['consecutivo_documento'] = '';
		// $objectInventory->updateInventory($params);
	}

	//============================ FUNCION PARA BUSCAR Y ASIGNAR UNA COTIZACION/PEDIO A UNA FACTURA/PEDIDO ======================================//
	function cargarDocuementoNewFactura($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar){
		//SI SE VA A CARGAR UNA COTIZACION VALIDAR QUE NO ESTE VENCIDA
		if ($opcCargar=='cotizacion') {
			$sql   = "SELECT COUNT(cliente) as cont, nit, id_cliente, cliente
					  FROM ventas_cotizaciones
					  WHERE consecutivo='$id' AND  activo = 1 AND (estado = 1 OR estado=2) AND id_sucursal= '$id_sucursal' AND id_bodega= '$filtro_bodega' AND id_empresa='$id_empresa' AND
					  ('".date('Y-m-d')."' BETWEEN date_format(fecha_inicio,'%Y-%m-%d') AND date_format(fecha_finalizacion,'%Y-%m-%d'))";

        	$query = mysql_query($sql,$link);
        	$mensaje = ' <script>
		        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
		        			alert("Aviso!\nLa Cotizacion ya expiro\nO no existe.");
		        			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus(); },80);
        				</script>';
		}
		else{
			$whereRemision = ($opcCargar == 'remision')? "AND pendientes_facturar > 0": "";

			$sql   = "SELECT COUNT(cliente) as cont, nit, id_cliente, cliente
						FROM $tablaBuscar
						WHERE consecutivo='$id'
							AND  activo = 1
							AND (estado = 1 OR estado=2)
							AND id_sucursal= '$id_sucursal'
							AND id_bodega= '$filtro_bodega'
							AND id_empresa='$id_empresa'
							$whereRemision";
        	$query = mysql_query($sql,$link);
        	$mensaje = '<script>
		        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
		        			alert("Numero invalido!\nDocumento no terminado o ya asignado");
		        			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();}, 100);
		        		</script>';
		}

        $resu = mysql_result($query,0,'cont');
        if ($resu>0) {
        	echo '
        		<script>
        			Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcCargar         : "'.$opcCargar.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
							idConsecutivoCotizacionPedido : '.$id.',
						}
					});

					if (document.getElementById("Win_Ventana_buscar_cotizacionPedido'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_cotizacionPedido'.$opcGrillaContable.'.close();
					}


        		</script>';
        }
        else{ echo $mensaje; }
	}

	function agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$campoSelect = "";
		switch ($typeDoc) {
			case 'cotizacion':
				$campoCantidad          = "cantidad";
				$title                  = 'Eliminar los Articulos de la Cotizacion';
				$referencia_input       = "C";
				$referencia_consecutivo = "Cotizacion";
				$tablaCarga             = "ventas_cotizaciones";
				$idTablaCargar          = "id_cotizacion_venta";
				$tablaCargaInventario   = "ventas_cotizaciones_inventario";

				$tablaBuscar ="ventas_cotizaciones";
				break;

			case 'pedido':
				$campoCantidad          = "saldo_cantidad";
				$title                  = 'Eliminar los Articulos del Pedido';
				$referencia_input       = "P";
				$referencia_consecutivo = "Pedido";
				$tablaCarga             = "ventas_pedidos";
				$idTablaCargar          = "id_pedido_venta";
				$tablaCargaInventario   = "ventas_pedidos_inventario";

				$tablaBuscar ="ventas_pedidos";
				break;

			case 'remision':
				$campoSelect            = ", id_centro_costo";					//VALIDACION CENTRO DE COSTO AL CARGAR REMISION EN FACTURACION
				$campoCantidad          = "saldo_cantidad";
				$title                  = 'Eliminar los Articulos de la Remision';
				$referencia_input       = "R";
				$referencia_consecutivo = "Remision";
				$tablaCarga             = "ventas_remisiones";
				$idTablaCargar          = "id_remision_venta";
				$tablaCargaInventario   = "ventas_remisiones_inventario";

				$tablaBuscar = "ventas_remisiones";
				break;
		}


		$sqlFactura       = "SELECT id_cliente, estado, id_centro_costo FROM ventas_facturas WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$queryFactura     = mysql_query($sqlFactura,$link);

		$idClienteFactura = mysql_result($queryFactura,0,'id_cliente');
		$estadoFactura    = mysql_result($queryFactura,0,'estado');
		$idCcostoDB       = mysql_result($queryFactura,0,'id_centro_costo');

		if($estadoFactura == 1){ echo '<script>alert("Error!,\nLa presenta factura ha sido generada.");</script>'; return; }
		if($estadoFactura == 3){ echo '<script>alert("Error!,\nLa presenta factura ha sido cancelada.");</script>'; return; }
		else if($idClienteFactura == '' || $idClienteFactura == 0){
			cargarDocuementoNewFactura($codDocAgregar,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,'facturacion/', $opcGrillaContable, '', $idTablaPrincipal, $tablaInventario, '',$typeDoc);
			return;
			// echo '<script>alert("Aviso!,\nSeleccione un cliente para la factura.");</script>'; return;
		}

		$whereRemision = ($typeDoc == 'remision')? " AND COI.saldo_cantidad > 0": "";

		//VALIDACION ESTADO DE LA FACTURA
		$idClienteDocAgregar    = '';
		$estadoDocAgregar       = '';
		$sqlValidateDocumento   = "SELECT id_cliente,estado,id,REPLACE(observacion,'\"','') AS observacion $campoSelect FROM $tablaCarga WHERE consecutivo='$codDocAgregar' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND activo = 1";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);

		$idClienteDocAgregar = mysql_result($queryValidateDocumento,0,'id_cliente');
		$idDocumentoAgregar  = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar    = mysql_result($queryValidateDocumento,0,'estado');
		$idCcosDocLoad       = mysql_result($queryValidateDocumento,0,'id_centro_costo');
		$observacion         = mysql_result($queryValidateDocumento,0,'observacion');

		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' esta cancelado");</script>'; return; }
		else if($idClienteDocAgregar <> $idClienteFactura){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' pertenece a un cliente diferente.");</script>'; return; }
		else if($typeDoc == "remision" && $idCcosDocLoad<>$idCcostoDB){ echo '<script>alert("Aviso!,\nLa Remision N. '.$codDocAgregar.' pertenece a un Centro de costo diferente.");</script>'; return; }

		//VALIDACION QUE EL DOCUMENTO NO HAYA SIDO INGRESADO
		$sqlValidateRepetido = "SELECT COUNT(id) AS contDocRepetido
								FROM $tablaInventario
								WHERE activo=1 AND id_empresa='$id_empresa' AND id_bodega='$filtro_bodega' AND id_consecutivo_referencia='$idDocumentoAgregar'
									AND nombre_consecutivo_referencia='$referencia_consecutivo'
									AND id_factura_venta='$id_factura'
								GROUP BY id_tabla_inventario_referencia LIMIT 0,1";
		$docRepetido = mysql_result(mysql_query($sqlValidateRepetido,$link),0,'contDocRepetido');
		if($docRepetido > 0){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' ya ha sido agregado en la presente factura");</script>'; return; }

		if($observacion <> ''){
			$sqlObservacion = "UPDATE ventas_facturas
								SET observacion = IF(
											observacion<>'',
											CONCAT(observacion, ' ', '$referencia_input ', '$codDocAgregar', ': ', '$observacion'),
											CONCAT('$referencia_input ', '$codDocAgregar', ': ', '$observacion')
										)
								WHERE id='$id_factura'
									AND id_empresa='$id_empresa'
									AND activo=1";
			$queryObservacion = mysql_query($sqlObservacion,$link);
		}

		//GENERA CICLO PARA INSERTAR ARTICULOS DEL DOCUMENTO REFERENCIA A TABLA INVENTARIOS FACTURAS
		$sqlConsultaInventario= "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.$campoCantidad AS cantidad,COI.costo_unitario,
                                        COI.tipo_descuento,COI.descuento,
                                        COI.id_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                                        CO.id AS id_documento,CO.consecutivo AS consecutivo_documento
                                FROM $tablaCargaInventario AS COI
                                INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                WHERE CO.consecutivo     ='$codDocAgregar'
                                    AND COI.activo       = 1
                                    AND CO.id_sucursal   ='$id_sucursal'
                                    AND CO.id_bodega     ='$filtro_bodega'
                                    AND CO.id_empresa    ='$id_empresa'
                                    $whereRemision";
        $queryConsultaInventario=mysql_query($sqlConsultaInventario,$link);

        $contInsert=0;
        while ($row = mysql_fetch_array($queryConsultaInventario)) {
        	$contInsert++;
        	$idDocCruce = $row['id_documento'];
            $sqlInsertArticulos="INSERT INTO $tablaInventario
                                            ($idTablaPrincipal,
                                            id_inventario,
                                            cantidad,
                                            costo_unitario,
                                            tipo_descuento,
                                            descuento,
                                            observaciones,
                                            id_tabla_inventario_referencia,
                                            id_consecutivo_referencia,
                                            consecutivo_referencia,
                                            nombre_consecutivo_referencia,
                                            id_impuesto)
                                VALUES ('$id_factura',
                                        '".$row['id_inventario']."',
                                        '".$row['cantidad']."',
                                        '".$row['costo_unitario']."',
                                        '".$row['tipo_descuento']."',
                                        '".$row['descuento']."',
                                        '".$row['observaciones']."',
                                        '".$row['id']."',
                                        '".$row['id_documento']."',
                                        '".$row['consecutivo_documento']."',
                                        '$referencia_consecutivo',
                                        $row[id_impuesto])";
            $queryInsertArticulos=mysql_query($sqlInsertArticulos,$link);
        }

        if($contInsert > 0){
    		$newDocReferencia  ='<div style="width:136px; margin-left:5px; float:left; overflow:hidden;height: 22px;" id="divDocReferenciaFactura_'.$referencia_input.'_'.$idDocumentoAgregar.'">'
							       .'<div class="contenedorInputDocReferenciaFactura">'
							           .'<input type="text" class="inputDocReferenciaFactura" value="'.$referencia_input.' '.$codDocAgregar.'" readonly style="border-bottom: 1px solid #d4d4d4;"/>'
							       .'</div>'
							       .'<div title="'.$title.' # '.$codDocAgregar.' en la presente factura" onclick="eliminaDocReferenciaFactura(\\\''.$idDocumentoAgregar.'\\\',\\\''.$referencia_input.'\\\',\\\''.$id_factura.'\\\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">'
							           .'<div style="overflow:hidden; border-radius:35px; color:#fff; height:16px; width:16px; margin:1px;" id="btn'.$opcGrillaContable.'_'.$referencia_input.'_'.$idDocCruce.'">'
	                                        .'<div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>'
	                                    .'</div>'
							       .'</div>'
							    .'</div>';

			echo'<script>
					divDocsReferenciaFactura = document.getElementById("contenedorDocsReferenciaFactura").innerHTML;
					document.getElementById("contenedorDocsReferenciaFactura").innerHTML =divDocsReferenciaFactura+\''.$newDocReferencia.'\';
	    			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").value="";

	    			Ext.get("renderizaNewArticulo'.$opcGrillaContable.'").load({
			            url     : "facturacion/bd/bd.php",
			            scripts : true,
			            nocache : true,
			            params  :
			            {
							opc               : "reloadBodyAgregarDocumento",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_factura        : "'.$id_factura.'",
			            }
			        });

					actualiza_fila_ventana_busqueda_doc_cruce('.$idDocumentoAgregar.');
        		</script>';
        }
        else{
        	echo'<script>
        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
        			alert("Numero invalido!\nDocumento no terminado o ya asignado");
        			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();}, 100);
        		</script>';
		}
	}

	function reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		echo cargaArticulosSave($id_factura,'',0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
	}

	function eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		$campoDocReferencia = '';
		if($docReferencia=='P'){ $campoDocReferencia = 'Pedido'; }
		else if($docReferencia=='C'){ $campoDocReferencia = 'Cotizacion'; }
		else if($docReferencia=='R'){ $campoDocReferencia = 'Remision'; }
		//Sumatoria de los productos a eliminar
		if($tablaInventario == "ventas_facturas_inventario"){
			//Hay grupos asociados al documento referencia?
			$subtotalGrupos = array();
			$sqlGrupos = "SELECT VFG.id AS id_grupo,
								 VFI.costo_unitario,
								 VFI.cantidad  
						FROM ventas_facturas_grupos AS VFG 
						INNER JOIN ventas_facturas_inventario_grupos AS VFIG ON VFG.id = VFIG.id_grupo_factura_venta 
						INNER JOIN ventas_facturas_inventario AS VFI ON VFI.id=VFIG.id_inventario_factura_venta
								WHERE VFG.id_factura_venta = $id_factura
								AND VFG.activo  = 1
								AND VFIG.activo = 1 
								AND VFI.activo  = 1
								AND VFI.id_consecutivo_referencia=$id_doc_referencia
								AND VFI.nombre_consecutivo_referencia='$campoDocReferencia'";
			$queryGrupos = mysql_query($sqlGrupos, $link);

			if ($queryGrupos) {
			    while ($row = mysql_fetch_assoc($queryGrupos)) {
            		$idGrupo = $row['id_grupo'];
            		$subtotal = $row['costo_unitario'] * $row['cantidad'];
							
            		if (!isset($subtotalGrupos[$idGrupo])) {
            		    $subtotalGrupos[$idGrupo] = 0;
            		}
            		$subtotalGrupos[$idGrupo] += $subtotal;
				    
			    }
			}

			if(!empty($subtotalGrupos)){
				//Si hay grupos calcular el subtotal restado para cada grupo en el que este la remision 
				foreach($subtotalGrupos as $idGrupo => $subtotalGrupo){
					$sqlUpdateGrupos = "UPDATE ventas_facturas_grupos SET costo_unitario = costo_unitario - $subtotalGrupo WHERE id = $idGrupo";
					$queryUpdateGrupos = mysql_query($sqlUpdateGrupos, $link);
				}
			}

		}
		$sql   ="DELETE FROM ventas_facturas_inventario WHERE id_factura_venta=$id_factura AND id_consecutivo_referencia=$id_doc_referencia AND id_empresa='$id_empresa' AND id_bodega='$filtro_bodega' AND nombre_consecutivo_referencia='$campoDocReferencia'";
		$query = mysql_query($sql,$link);

		echo cargaArticulosSave($id_factura,'',0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

		if($query){
			echo'<script>
					document.getElementById("divDocReferenciaFactura_'.$docReferencia.'_'.$id_doc_referencia.'").parentNode.removeChild(document.getElementById("divDocReferenciaFactura_'.$docReferencia.'_'.$id_doc_referencia.'"));
				</script>';
		}
		else{ echo'<script>alert("Error de conexion.\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
	}

	function ventanaUpdateFecha(){
		echo'<div style="margin:0 10px; overflow:hidden;">
				<div style="width:100%; float:left; height:16px; overflow:hidden;" id="loadValidaUpdatefecha"></div>

				<div style="width:80px; float:left; margin-top:10px;">fecha</div>
				<div style="width:calc(100% - 80px); float:left; margin-top:10px;"><input type="text" id="fecha_updateFechafactura" value="'.date('Y-m-d').'"/></div>
				<div style="width:80px; float:left; margin-top:10px;  height: 23px;">Usuario</div>
				<div style="width:calc(100% - 80px); float:left; margin-top:10px;  height: 23px;"><input type="text" id="usuario_updateFechafactura" class="myfield" /></div>
				<div style="width:80px; float:left; margin-top:10px;  height: 23px;">Password</div>
				<div style="width:calc(100% - 80px); float:left; margin-top:10px;  height: 23px;"><input type="password" id="password_updateFechafactura" class="myfield" /></div>
			</div>
			<script>
				new Ext.form.DateField({
				    format     : "Y-m-d",
				    width      : 150,
				    allowBlank : false,
				    showToday  : true,
				    applyTo    : "fecha_updateFechafactura",
				    editable   : false
				});
			</script>';
	}

	function validateUpdateFecha($fecha,$usuario,$password,$id_empresa,$idFacturaVenta,$opcGrillaContable,$link){
		switch($opcGrillaContable){
			case "RemisionesVenta":
				$tablaBuscar = "ventas_remisiones";
				$numeroFactura = "consecutivo";
				$tipoDocumento = "RV";
				$campoFecha = "fecha_finalizacion";
				break;

			case "FacturaVenta":
				$tablaBuscar = "ventas_facturas";
				$numeroFactura = "numero_factura_completo";
				$tipoDocumento = "FV";
				$campoFecha = "fecha_vencimiento";
				break;

			case "CotizacionVenta":
				$tablaBuscar = "ventas_cotizaciones";
				$numeroFactura = "consecutivo";
				$tipoDocumento = "CT";
				$campoFecha = "fecha_finalizacion";
				break;

			case "PedidoVenta":
				$tablaBuscar = "ventas_pedidos";
				$numeroFactura = "consecutivo";
				$tipoDocumento = "PD";
				$campoFecha = "fecha_finalizacion";
				break;
		}

		//Validar el usuario
		$password     = md5($password);
		$sqlUsuario   = "SELECT COUNT(id) AS cont_usuario,id_rol FROM empleados WHERE id_empresa='$id_empresa' AND username='$usuario' AND password='$password' and activo=1 LIMIT 0,1";
		$queryUsuario = mysql_query($sqlUsuario,$link);
		$contUsuario  = mysql_result($queryUsuario,0,'cont_usuario');
		$id_rol       = mysql_result($queryUsuario,0,'id_rol');
		if(!$queryUsuario || $contUsuario == 0){ echo'<script>alert("Aviso,\nNombre de usuario o password incorrectos");</script>'; exit; }

		// CONSULTAR SI EL ROL DEL EMPLEADON TIENE EL PERMISO PARA REALIZAR EL CAMBIO
		$sql="SELECT id FROM empleados_roles_permisos WHERE id_rol=$id_rol AND id_permiso=25";
		$query=mysql_query($sql,$link);
		$id_permiso = mysql_result($query,0,'id');
		if ($id_permiso=='' || $id_permiso==0) {
			echo '<script>alert("Aviso!\nNo tiene los permisos necesarios para realizar el cambio");</script>'; exit;
		}

		// Buscar info del documento
		$sqlFactura = "SELECT estado, $numeroFactura, fecha_inicio FROM $tablaBuscar WHERE id=$idFacturaVenta";
		$query=mysql_query($sqlFactura,$link);
		$estado = mysql_result($query,0,'estado');
		$numeroCompleto = mysql_result($query,0,$numeroFactura);
		$fechDoc = mysql_result($query,0,'fecha_inicio');

		//Validar si el periodo esta cerrado
		$sqlValidaPeriodo = "SELECT COUNT(id) as cont_periodos 
								FROM cierre_por_periodo 
								WHERE 
									fecha_inicio <= '$fechDoc' 
									AND fecha_final >= '$fechDoc' 
									AND estado = 1 
									AND activo = 1";

		$queryValidaPeriodo = mysql_query($sqlValidaPeriodo,$link);
		$contPeriodosCerrados  = mysql_result($queryValidaPeriodo,0,'cont_periodos');
		
		//Validar si el año esta cerrado
		$year = date('Y', strtotime($fechDoc));
		$sqlValidaCierres = "SELECT COUNT(id) AS cont_cieres 
								FROM nota_cierre 
								WHERE 
								YEAR(fecha_nota) = '$year' 
								AND estado = 1 
								AND activo = 1";

		$queryValidaCierres = mysql_query($sqlValidaCierres,$link);
		$contCierres  = mysql_result($queryValidaCierres,0,'cont_cieres');

		if($contPeriodosCerrados > 0 || $contCierres > 0){ 
			echo'<script>alert("Error!\nEl Documento '.$numeroCompleto.' se encuentra en un periodo cerrado");</script>';
			exit;
		}
		
		//Actualizar la tabla del documento
		$sqlUpdateFecha  = "UPDATE $tablaBuscar 
						SET  fecha_inicio = '$fecha',
						$campoFecha = '$fecha' 
						WHERE
							id= '$idFacturaVenta'";
		if(!mysql_query($sqlUpdateFecha,$link)){ echo'<script>alert("Aviso,\nError de conexion con la base de datos al actualizar la cabecera del documento'. mysql_error($link) .'");</script>'; exit; }

		//Si el documento tiene asientos entocnes se actualizan
		if($estado=='1' && ($tipoDocumento == "FV" || $tipoDocumento == "RV")){

			$sqlUpdateFechaColgaap = "UPDATE asientos_colgaap 
									SET fecha = '$fecha' 
									WHERE
										id_documento= '$idFacturaVenta' 
										AND tipo_documento = '$tipoDocumento'";

			$sqlUpdateFechaNiif = "UPDATE asientos_niif 
									SET fecha = '$fecha' 
									WHERE
										id_documento= '$idFacturaVenta' 
										AND tipo_documento = '$tipoDocumento'";

			if(!mysql_query($sqlUpdateFechaColgaap,$link)){ echo'<script>alert("Aviso,\nError de conexion con la base de datos al modificar los asientos colgaap'. mysql_error($link) .'");</script>'; exit; }
			if(!mysql_query($sqlUpdateFechaNiif,$link)){ echo'<script>alert("Aviso,\nError de conexion con la base de datos al modificar los asientos niif'. mysql_error($link) .'");</script>'; exit; }
		}

		else if ($estado==2) {
			echo'<script>alert("Error!\nEl Documento ha sido cruzado \nNo se puede realizar mas acciones sobre el");</script>';
			exit;
		}
		else if ($estado==3) {
			echo'<script>alert("Error!\nEl Documento ha sido cancelado \nNo se puede realizar mas acciones sobre el");</script>';
			exit;
		}

		if($estado=='1'){
			echo'<script>
			document.getElementById("fecha'.$opcGrillaContable.'").value = "'.$fecha.'";
			document.querySelector("#fechaLimitePago'.$opcGrillaContable.' input").value = "'.$fecha.'";
			Win_Ventana_update_fecha_'.$opcGrillaContable.'.close();
			</script>';
		}else{
			echo'<script>
					document.getElementById("fecha'.$opcGrillaContable.'").value = "'.$fecha.'";
					Win_Ventana_update_fecha_'.$opcGrillaContable.'.close();
				</script>';
		}

		//INSERT LOG DE CAMBIOS DE FECHA
		if($tipoDocumento == "FV"){
			$sqlInsert   = "INSERT INTO ventas_facturas_update_fecha(fecha,hora,usuario,id_factura,id_empresa) VALUES (NOW(),NOW(),'$usuario','$idFacturaVenta','$id_empresa')";
			$queryInsert = mysql_query($sqlInsert,$link);
			if(!$queryInsert){ echo'<script>alert("Aviso,\nError de conexion con la base de datos al insertar en el log de cambio de fecha");</script>'; exit; }
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR LA CUENTA DE PAGO ==============================================//
	function UpdateCuentaPago($id,$idCuentaPago,$link){
		$sql   = "UPDATE ventas_facturas SET id_configuracion_cuenta_pago='$idCuentaPago' WHERE id='$id' AND activo=1";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
	}

	//=========================== FUNCION PARA ACTUALIZAR LA PLANTILLA ==============================================//
	function UpdateIdPlantilla($id,$idPlantilla,$link){
		$sql   = "UPDATE ventas_facturas SET plantillas_id='$idPlantilla' WHERE id='$id' AND activo=1";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
	}

	function VentanaAnticipoParcial($opcGrillaContable){
		echo'<div style="margin:5px; overflow:hidden;">
				<div id="loadSaveAnticipoParcial_'.$opcGrillaContable.'" style="float:left; width:100%; height:20px;"></div>
				<div style="float:left; width:90px;">Valor anticipo</div>
				<div style="float:left; width:140px; height:22px;"><input type="text" class="myfield" id="input_valor_parcial_anticipo_'.$opcGrillaContable.'"/></div>
			</div>';
	}

	function validateCcos($codigoCcos,$id_empresa,$link){
		$sqlCcos   = "SELECT COUNT(id) AS contCcos FROM centro_costos WHERE codigo LIKE '$codigoCcos%' AND id_empresa='$id_empresa' AND activo=1 GROUP BY activo";
		$queryCcos = mysql_query($sqlCcos,$link);
		echo $contCcos = mysql_result($queryCcos, 0, 'contCcos');
	}

	function updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id_factura,$id_empresa,$link){
		$sql   = "UPDATE ventas_facturas SET id_centro_costo='$idCcos' WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$link);
		if(!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; exit; }

		echo'<script>
				document.getElementById("cCos_'.$opcGrillaContable.'").value = "'.$codigo.' '.$nombre.'";
				Win_Ventana_Ccos_'.$opcGrillaContable.'.close();
			</script>';
	}

	function updateSucursalCliente($id_factura,$id_scl_cliente,$nombre_scl_cliente,$opcGrillaContable,$id_empresa,$link){
		$sql   = "UPDATE ventas_facturas SET id_sucursal_cliente='$id_scl_cliente',sucursal_cliente='$nombre_scl_cliente' WHERE id='$id_factura' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		$sql_ubicacion 		= "SELECT id_departamento, id_ciudad FROM terceros_direcciones WHERE id=$id_scl_cliente";
		$query_departamento = mysql_query($sql_ubicacion,$link);
		$id_departamento 	= mysql_result($query_departamento,0,'id_departamento');
		$id_ciudad 			= mysql_result($query_departamento,0,'id_ciudad');

		$sql_empresa 		= "SELECT documento FROM empresas WHERE id=$id_empresa";
		$query_empresa 		= mysql_query($sql_empresa,$link);
		$NIT 				= mysql_result($query_empresa,0,'documento');

		$municipios_exentos = array(2293, 4722, 4720);
		//Verificamos que sea plataforma colombia o comunicaciones

		//Insertamos de manera automatica una observacion debido al decreto 1085 del 2 de Julio
		if($NIT == 900013664 || $NIT == 830509557){
			if($id_departamento == 672 && !(in_array($id_ciudad,$municipios_exentos))){
				echo "<script>
					document.getElementById('observacionFacturaVenta').value = 'Servicios Exentos - Decreto 1085 del 2 de julio de 2023';
					</script>";
			}
		}
		else{
			echo "<script>
			let observacion = document.getElementById('observacionFacturaVenta').value;
			if(observacion.includes('Decreto 1085')){document.getElementById('observacionFacturaVenta').value = '';}
			</script>";
		}


	}

	//====================== BUSQUEDA DE LA GRILLA MANUAL ======================//
	function busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa){


		//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
		if ($filtro!='') {
			$sql="SELECT COUNT(id) as cont $whereSum  FROM $tabla WHERE activo=1 $estado $filtro AND id_empresa='$id_empresa'";
			$query=mysql_query($sql,$link);
			$rows_registros=mysql_result($query,0,'cont');
			$paginas=ceil( $rows_registros/$limite );

			//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
			$limit1     = 0;
			$limit2     = $limite;
			$acumScript = '';
			for ($i=1; $i <= $paginas; $i++) {
				$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
				$limit1     =$limit2+1;
				$limit2     =$limit2+$limite;
			}
		}
		//SI NO SE HACE LA BUSQUEDA CON FILTRO SINO DE FORMA NORMAL
		else{
			$sql="SELECT COUNT(id) as cont $whereSum  FROM $tabla WHERE activo=1 $estado AND id_empresa='$id_empresa'";
			$query=mysql_query($sql,$link);
			$rows_registros=mysql_result($query,0,'cont');
			$paginas=ceil( $rows_registros/$limite );

			//CREAR ARRAY CON LOS LIMITES DE LAS CONSULTAS
			$limit1     = 0;
			$limit2     = $limite;
			$acumScript = '';
			for ($i=1; $i <= $paginas; $i++) {
				$acumScript .='arrayLimitGrilla'.$opcGrillaContable.'['.$i.']="'.$limit1.','.$limit2.'";';
				$limit1     =$limit2+1;
				$limit2     =$limit2+$limite;
			}
		}

		//SI SE BUSCA DESDE UNA PAGINA DIFERENTE A LA 1, VALIDAR SI EL RESULTADO DA LA MISMA CANTIDAD DE PAGINAS, SINO, PONER EN PAGINA 1 EJ(9 PAGINAS CONTRA EL RESULTADO DE 1 PAGINA)
		if ($pagina>$paginas) {
			$limit='0,'.$limite;
			$pagina=1;
		}

		$sqlCuentas   = "SELECT $id_tercero,$tercero,$nit $whereSum FROM $tabla WHERE activo=1 $estado $filtro AND id_empresa='$id_empresa' GROUP BY $id_tercero ASC LIMIT $limit";
		$queryCuentas = mysql_query($sqlCuentas,$link);
		while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
			$contFilaCuenta++;

			$divSaldoPendiente=($tabla!='terceros')? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

			$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<div class="campo0">'.$contFilaCuenta.'</div>
									<div class="campo1" id="nit_'.$rowCuentas[$id_tercero].'">'.$rowCuentas['nit'].'</div>
									<div class="campo2" style="border-left:0px;" id="tercero_'.$rowCuentas[$id_tercero].'" title="'.$rowCuentas[$tercero].'">'.$rowCuentas[$tercero].'</div>
									'.$divSaldoPendiente.'
									<div class="campo4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
										<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\')" value="'.$rowCuentas[$id_tercero].'" >
									</div>
								</div>';
		}

		$filaInsertBoleta .= '<script>
								document.getElementById("labelPaginacion").innerHTML="Pagina '.$pagina.' de '.$paginas.' ";
								PaginaActual'.$opcGrillaContable.'='.$pagina.';
								MaxPage'.$opcGrillaContable.'='.$paginas.';
								arrayLimitGrilla'.$opcGrillaContable.'.length=0;
								'.$acumScript.'
								'.$imprimeVar.'

							</script>';

			echo $filaInsertBoleta;
	}

	//============= FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO =============//
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$link){
		$sql="SELECT estado,id_bodega,numero_factura_completo AS consecutivo FROM ventas_facturas WHERE id=$id_documento";
		$query=mysql_query($sql,$link);

		$estado    = mysql_result($query,0,'estado');
		$id_bodega = mysql_result($query,0,'id_bodega');
		$consecutivo = mysql_result($query,0,'consecutivo');
		if ($estado==1) {
			$mensaje='Error!\nEl Documento ha sido generado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==2) {
			$mensaje='Error!\nEl Documento ha sido cruzado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==3) {
			$mensaje='Error!\nEl Documento ha sido cancelado \nNo se puede realizar mas acciones sobre el';
		}

		if ($estado>0) {
			echo'<script>
						alert("'.$mensaje.'");
						if (document.getElementById("Win_Ventana_descripcion_Articulo_factura")) {
							Win_Ventana_descripcion_Articulo_factura.close();
						}
						if (document.getElementById("Win_Ventana_update_fecha_FacturaVenta")) {
							Win_Ventana_update_fecha_FacturaVenta.close();
						}
						Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "bd/grillaContableBloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega     : "'.$id_bodega.'",
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_factura_venta  : "'.$id_documento.'"
							}
						});

						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Factura de Venta<br>N. '.$consecutivo.'";
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			exit;
		}
	}

	function cancelar_ccos($opcGrillaContable,$id_documento,$id_empresa,$link){
		$sqlCcos   = "UPDATE ventas_facturas SET id_centro_costo='' WHERE id_empresa='$id_empresa' AND id='$id_documento' AND activo=1";
		$queryCcos = mysql_query($sqlCcos,$link);

		if($queryCcos){
			echo'<script>
					Win_Ventana_Ccos_'.$opcGrillaContable.'.close();
					document.getElementById("cCos_'.$opcGrillaContable.'").value="";
				</script>';
		}
		else{ echo '<script>alert("Aviso,\nNo se Elimino el Centro de costo de la presente factura!")</script>'; exit; }
	}

	//================================ ANTICIPOS ===============================//
	//**************************************************************************//

	function ventanaValorAnticipo($idFactura,$valor_Factura,$id_cuenta_anticipo,$id_anticipo,$opcGrilla,$id_empresa,$link){

		$sqlAnticipo = "SELECT C.saldo_pendiente AS saldo_cuenta
					FROM recibo_caja AS E,
						recibo_caja_cuentas AS C
					WHERE E.id_empresa='$id_empresa'
						AND E.activo=1
						AND E.id=C.id_recibo_caja
						AND (E.estado=1 || E.estado=2)
						AND C.activo=1
						AND C.id='$id_cuenta_anticipo'";
		$queryAnticipo = mysql_query($sqlAnticipo,$link);
		$saldo_cuenta = mysql_result($queryAnticipo, 0, 'saldo_cuenta');
		if($saldo_cuenta == 0 OR $saldo_cuenta==''){
			echo'<script>
					Win_Ventana_valor_anticipo.close(id);
					alert("Aviso,\nEl Anticipo ya no existe dentro del sistema!")
				</script>';
			exit;
		}
		$saldo_cuenta *= 1;

		//ANTICIPOS EN LA FACTURA DE COMPRA
		$sqlSaldoAnticipo = "SELECT id,valor
							FROM anticipos
							WHERE id_empresa='$id_empresa'
								AND id_documento='$idFactura'
								AND tipo_documento='FV'
								AND activo=1";
		$querySaldoAnticipo = mysql_query($sqlSaldoAnticipo,$link);

		$valorAnticipo  = 0;
		$saldoAnticipos = 0;
		while ($rowSaldoAnticipo = mysql_fetch_assoc($querySaldoAnticipo)) {
			if($rowSaldoAnticipo['id'] == $id_anticipo){ $valorAnticipo = $rowSaldoAnticipo['valor']; continue; }
			$saldoAnticipos += $rowSaldoAnticipo['valor'];
		}
		$valorAnticipo  *= 1;
		$saldoAnticipos *= 1;

		?>
			<div id="load_save_<?php echo $opcGrilla; ?>" style="overflow:hidden; position:fixed; width:18px; height:18px;"></div>
			<div style="margin:2%; overflow:hidden; width:96%;" class="contenedor_ventana_<?php echo $opcGrilla; ?>">
				<div style="overflow:hidden; width:100%; margin-top:15px;">
					<div style="float:left; width:50%; margin-right:1%;">Saldo Actual de factura</div>
					<div style="float:left; width:49%;"><input type="text" class="myfield" style="width:100%;" value="<?php echo $valor_Factura; ?>" readonly/></div>
				</div>
				<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:50%; margin-right:1%;">Saldo Otros Anticipos</div>
					<div style="float:left; width:49%;"><input type="text" class="myfield" style="width:100%;" value="<?php echo $saldoAnticipos; ?>" readonly/></div>
				</div>
				<div style="overflow:hidden; width:100%;" class="EmpSeparador">ANTICIPO HA REALIZAR</div>
				<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:50%; margin-right:1%;">Valor Maximo de Anticipo</div>
					<div style="float:left; width:49%;"><input id="saldo_anticipo" type="text" class="myfield" style="width:100%;" value="<?php echo $saldo_cuenta; ?>" readonly/></div>
				</div>
				<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:50%; margin-right:1%;">valor Anticipo</div>
					<div style="float:left; width:49%;"><input id="valor_anticipo" type="text" class="myfield" style="width:100%;" value="<?php echo $valorAnticipo; ?>"/></div>
				</div>
			</div>
			<style>
				.contenedor_ventana_<?php echo $opcGrilla; ?>{ margin-top:10px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> > div{ margin-bottom:5px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> > div > div{ height:23px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> input{ text-align:right; padding-right:5px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> input[readonly]{ background-color:#ededed; }
			</style>
		<?php
	}

	function guardarAnticipo($valor_Factura,$contFila,$idFactura,$valor_anticipo,$id_cuenta_anticipo,$id_anticipo,$opcGrilla,$id_empresa,$link){
		//VALIDA SI EL ANTICIPO EXISTE
		$sqlAnticipo = "SELECT
							E.id AS id_documento_anticipo,
							E.consecutivo,
							if(C.id_tercero>0,C.id_tercero,E.id_tercero) AS id_tercero,
							if(C.id_tercero>0,C.nit_tercero,E.nit_tercero) AS nit_tercero,
							if(C.id_tercero>0,C.tercero,E.tercero) AS tercero,
							C.saldo_pendiente AS saldo_cuenta,
							C.cuenta AS cuenta_colgaap,
							C.cuenta_niif
						FROM recibo_caja AS E,
							recibo_caja_cuentas AS C
						WHERE E.id_empresa='$id_empresa'
							AND E.activo=1
							AND E.id=C.id_recibo_caja
							AND (E.estado=1 || E.estado=2)
							AND C.activo=1
							AND C.id='$id_cuenta_anticipo'";
		$queryAnticipo = mysql_query($sqlAnticipo,$link);

		$saldo_cuenta   = mysql_result($queryAnticipo, 0, 'saldo_cuenta');
		$cuenta_niif    = mysql_result($queryAnticipo, 0, 'cuenta_niif');
		$cuenta_colgaap = mysql_result($queryAnticipo, 0, 'cuenta_colgaap');

		$consecutivoAnticipo = mysql_result($queryAnticipo, 0, 'consecutivo');
		$idDocumentoAnticipo = mysql_result($queryAnticipo, 0, 'id_documento_anticipo');
		$idTercero           = mysql_result($queryAnticipo, 0, 'id_tercero');
		$nitTercero          = mysql_result($queryAnticipo, 0, 'nit_tercero');
		$tercero             = mysql_result($queryAnticipo, 0, 'tercero');

		if($saldo_cuenta == 0 OR $saldo_cuenta==''){
			echo'<script>
					Win_Ventana_valor_anticipo.close(id);
					alert("Aviso,\nEl Anticipo no existe dentro del sistema!")
				</script>';
			exit;
		}
		else if($saldo_cuenta < $valor_anticipo){ echo '<script>alert("Aviso,\nEl valor del anticipo para la factura '.$valor_anticipo.' no puede exceder el saldo registrado '.$saldo_cuenta.'")</script>'; exit; }

		//ANTICIPOS DUPLICADOS Y SALDO DE ANTICIPO
		$sqlAnticipo = "SELECT id,id_cuenta_anticipo,valor
							FROM anticipos
							WHERE id_empresa='$id_empresa'
								AND id_documento='$idFactura'
								AND tipo_documento='FV'
								AND activo=1";
		$queryAnticipo = mysql_query($sqlAnticipo,$link);

		$saldoAnticipos     = 0;
		$contCuentaAnticipo = 0;
		while ($rowAnticipo = mysql_fetch_assoc($queryAnticipo)) {
			if($rowAnticipo['id_cuenta_anticipo'] == $id_cuenta_anticipo){
				$contCuentaAnticipo++;
				$id_Anticipo_BD = $rowAnticipo['id'];
			}
			if($rowAnticipo['id'] == $id_anticipo){ continue; }

			$saldoAnticipos += $rowAnticipo['valor'];
		}
		if($contCuentaAnticipo > 2){ echo '<script>alert("Aviso,\nExisten mas de un anticipo registrado con la misma cuenta en el recibo de caja")</script>'; exit; }
		else if($id_anticipo == '' && $id_Anticipo_BD > 0){ $id_anticipo = $id_Anticipo_BD; }

		$totalAnticipo = $saldoAnticipos + $valor_anticipo;

		//DATOS DE FACTURA
		$sqlfactura   = "SELECT id_cliente,cliente,nit,total_factura FROM ventas_facturas WHERE id='$idFactura' LIMIT 0,1";
		$queryFactura = mysql_query($sqlfactura,$link);
		$total_factura_anticipo   = mysql_result($queryFactura, 0, 'total_factura');

		if(!$queryFactura){ echo '<script>alert("Aviso,\nNo se encontro la factura de venta!")</script>'; exit; }

		//INSERT OR UPDATE
		if($id_anticipo == ''){
			if($valor_anticipo > $valor_Factura && user_permisos(301)=="true"){
				$valor_anticipo = $valor_Factura;
			}
			$sqlAnticipo = "INSERT INTO anticipos (id_cuenta_anticipo,
													id_empresa,
													id_documento,
													tipo_documento,
													valor,
													cuenta_niif,
													cuenta_colgaap,
													id_tercero,
													nit_tercero,
													tercero,
													id_documento_anticipo,
													tipo_documento_anticipo,
													consecutivo_documento_anticipo)
							VALUES ('$id_cuenta_anticipo',
									'$id_empresa',
									'$idFactura',
									'FV',
									'$valor_anticipo',
									'$cuenta_niif',
									'$cuenta_colgaap',
									'$idTercero',
									'$nitTercero',
									'$tercero',
									'$idDocumentoAnticipo',
									'RC',
									'$consecutivoAnticipo')";
			$queryAnticipo = mysql_query($sqlAnticipo,$link);
			if(!$queryAnticipo){ echo '<script>alert("Aviso,\nNo se guardo el anticipo registrado!")</script>'; exit; }

			$sqlAnticipo = "SELECT LAST_INSERT_ID()";
			$id_anticipo = mysql_result(mysql_query($sqlAnticipo,$link),0,0);
		}
		else{
			if($valor_anticipo > $total_factura_anticipo && user_permisos(301)=="true"){
				$valor_anticipo = $total_factura_anticipo;
			}
			$sqlAnticipo = "UPDATE anticipos
							SET valor='$valor_anticipo',
								cuenta_niif='$cuenta_niif',
								cuenta_colgaap='$cuenta_colgaap',
								id_tercero='$idTercero',
								nit_tercero='$nitTercero',
								tercero='$tercero',
								id_documento_anticipo='$idDocumentoAnticipo',
								tipo_documento_anticipo='RC'
							WHERE id='$id_anticipo'
								AND id_cuenta_anticipo='$id_cuenta_anticipo'
								AND id_empresa='$id_empresa'
								AND id_documento='$idFactura'
								AND tipo_documento='FV'";
			$queryAnticipo = mysql_query($sqlAnticipo,$link);
			if(!$queryAnticipo){ echo '<script>alert("Aviso,\nNo se guardo el anticipo registrado!")</script>'; exit; }
		}

		?>
			<script type="text/javascript">
				document.getElementById("fila_<?php echo $opcGrilla; ?>_<?php echo $contFila; ?>").setAttribute("ondblclick","ventanaValorAnticipo('<?php echo $contFila; ?>','<?php echo $id_anticipo; ?>','<?php echo $id_cuenta_anticipo; ?>')");
				document.getElementById("valor_<?php echo $opcGrilla; ?>_<?php echo $contFila; ?>").innerHTML='<?php echo $valor_anticipo; ?>';
				document.getElementById("total_<?php echo $opcGrilla; ?>").innerHTML='$ <?php echo number_format($totalAnticipo); ?>';

				//CAMPO CABECERA FACTURA DE COMPRA
				document.getElementById("<?php echo $opcGrilla; ?>").value = '$ <?php echo number_format($totalAnticipo); ?>';

				Win_Ventana_valor_anticipo.close();
			</script>

		<?php
	}

	function cancelarAnticipoFactura($idFactura,$id_empresa,$link){
		$sqlDelete   = "UPDATE anticipos SET activo=0 WHERE id_empresa='$id_empresa' AND id_documento='$idFactura' AND tipo_documento='FV'";
		$queryDelete = mysql_query($sqlDelete,$link);

		if(!$queryDelete){ echo '<script>alert("Aviso,\nNo se eliminaron los anticipos agregados!")</script>'; exit; }
		?>
			<script>
				document.getElementById("anticipo_FacturaVenta").value = "$ 0";
				Win_Ventana_cuenta_anticipo_FacturaVenta.close();
			</script>

		<?php
	}

	function filtro_anticipo($idFactura,$idTercero,$opcGrilla,$id_empresa,$link){
		?>
			<div style="margin-top:5px;">
				<select onchange="load_anticipos_ventas(this.value)" class="myfield" style="width:145px;">
					<option value="tercero">Tercero Factura</option>
					<option value="">Todos</option>
				<select>
			</div>
			<script type="text/javascript">

				load_anticipos_ventas("tercero");

				function load_anticipos_ventas(opcAnticipo){

					var terceroAnticipo = (opcAnticipo == '')? '': '<?php echo $idTercero ?>';

					Ext.get('contenedor_cuenta_<?php echo $opcGrilla; ?>').load({
						url     : 'facturacion/grilla_anticipo_factura.php',
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrilla       : '<?php echo $opcGrilla ?>',
							idFactura       : '<?php echo $idFactura ?>',
							terceroAnticipo : terceroAnticipo,
						}
					});
				}

			</script>
		<?php
	}

	//============================= GRUPOS DE ITEMS ============================//
	//**************************************************************************// 

	//===================== GUARDAR LOS GRUPOS DE LOS ITEMS ====================//
	function saveGroup($codigo,$nombre,$cantidad,$observaciones,$id_bodega,$id_documento,$id_impuesto,$nombre_impuesto,$valor_impuesto,$codigo_dian,$opcGrillaContable,$id_sucursal,$id_empresa,$costo_unitario,$descuento,$valor_impuesto,$mysql){
		$id_impuesto = ($id_impuesto=='' || $id_impuesto==0)? 'NULL' : $id_impuesto ;
		$sql="INSERT INTO
				ventas_facturas_grupos
				(
					id_factura_venta,
					codigo,
					nombre,
					cantidad,
					observaciones,
					id_impuesto,
					nombre_impuesto,
					codigo_impuesto,
					porcentaje_impuesto,
					costo_unitario,
					descuento,
					id_empresa,
					id_sucursal,
					id_bodega
				)
				VALUES
				(
					$id_documento,
					'$codigo',
					'$nombre',
					$cantidad,
					'$observaciones',
					'$id_impuesto',
					'$nombre_impuesto',
					'$codigo_dian',
					'$valor_impuesto',
					'$costo_unitario',
					'$descuento',
					$id_empresa,
					$id_sucursal,
					$id_bodega
				)";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$id_row = $mysql->insert_id();

			echo '<script>
					MyLoading2("off");
					var addRow = `<div class="bodyDivArticulosFacturaVenta" id="bodyDivGruposFacturaVenta_'.$id_row.'" data-id="'.$id_row.'">
									<div class="campo" style="width:40px !important; overflow:hidden;text-align:center;">
									<img src="img/grupos.png" style="margin-top: 3px;cursor:hand;" title="Grupo de Items" onclick="showHiddenItems('.$id_row.')">
									<div style="float:left; width:18px; overflow:hidden;" id="renderGrupoFacturaVenta_'.$id_row.'" ></div>
									</div>
									<div class="campo" style="width:12%;"><input type="text" id="codigoGrupoFacturaVenta_'.$id_row.'" readonly value="'.$codigo.'"></div>
									<div class="campoNombreArticulo"><input type="text" id="nombreGrupoFacturaVenta_'.$id_row.'" style="text-align:left;" readonly value="'.$nombre.'"></div>
									<div class="campo"><input type="text" id="unidadGrupoFacturaVenta_'.$id_row.'" readonly value="Unidad"></div>
									<div class="campo"><input type="text" id="cantGrupoFacturaVenta_'.$id_row.'" readonly value="'.$cantidad.'" ></div>
									<div class="campo"><input type="text" id="descuentoArticuloFacturaVenta_'.$id_row.'" readonly ></div>
									<div class="campo"><input type="text" id="costoGrupoFacturaVenta_'.$id_row.'" readonly></div>
									<div class="campo"><input type="text" id="costoTotalGrupoFacturaVenta_'.$id_row.'" readonly ></div>

									<div style="float:right; min-width:80px;">
										<div onclick="ventanaActualizaAgrupacionItems('.$id_row.')" title="Modificar Grupo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/config16.png"></div>
										<div onclick="deleteGrupoFacturaVenta('.$id_row.')" id="" title="Eliminar Grupo" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
									</div>
								</div>
								<div id="content-group-'.$id_row.'" style="display:none;border-bottom: 1px dashed #819cba;">
								</div>`;
					$("#DivArticulosFacturaVenta").prepend(addRow);
					Win_Ventana_agrega_agrupacion.close();
					//DivArticulosFacturaVenta
				</script>';
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al guardar el grupo',icono:'fail'})</script>";
		}
	}

	//=================== ACTUALIZAR LOS GRUPOS DE LOS ITEMS ===================//
	function updateGroup($codigo,$nombre,$cantidad,$observaciones,$id_bodega,$id_documento,$opcGrillaContable,$id_row,$id_sucursal,$id_empresa,$id_impuesto,$costo_unitario,$descuento,$valor_impuesto,$mysql){
		$id_impuesto = ($id_impuesto=='' || $id_impuesto==0)? 'NULL' : $id_impuesto ;
		$sql="UPDATE ventas_facturas_grupos 
				SET codigo     = '$codigo',
				nombre         = '$nombre',
				observaciones  = '$observaciones'
			WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_venta=$id_documento AND id=$id_row ";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					document.getElementById('codigoGrupoFacturaVenta_$id_row').value = '$codigo' ;
					document.getElementById('nombreGrupoFacturaVenta_$id_row').value = '$nombre' ;
					document.getElementById('cantGrupoFacturaVenta_$id_row').value   = '$cantidad' ;
					MyLoading2('off');
					Win_Ventana_agrega_agrupacion.close();
				</script>";
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al actualizar el grupo',icono:'fail'})</script>";
		}
	}

	//======================== ELIMINAR EL GRUPO CREADO ========================//
	function deleteGrupo($id_row,$opcGrillaContable,$id_documento,$id_empresa,$mysql){
		$sql="DELETE FROM ventas_facturas_grupos WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_venta=$id_documento AND id=$id_row";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="DELETE FROM ventas_facturas_inventario_grupos WHERE id_factura_venta=$id_documento AND id_grupo_factura_venta=$id_row";
			$query=$mysql->query($sql,$mysql->link);
			echo "<script>
					MyLoading2('off',{texto:'Registro Eliminado'});
					// SACAR LOS ITEMS DEL CONTENEDOR
					var items = $('#content-group-$id_row > div');
					$('#DivArticulosFacturaVenta').prepend(items);
					$('#bodyDivGruposFacturaVenta_$id_row').remove();
					$('#content-group-$id_row').remove();
					//Win_Ventana_actualiza_agrupacion.close;
				</script>";
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al eliminar el grupo',icono:'fail'})</script>";
		}
	}

	//========== ACTUALIZAR EL COSTO,IMPUESTO Y DESCUENTO DE UN GRUPO ==========//
	function actualizaCamposGrupo($accion,$id_documento,$id_inventario='',$id_empresa,$mysql,$id_grupo='',$itemsId){
		// CONSULTAR LA INFORMACION DE GRUPO
		if($id_grupo===''){
		$sql="SELECT id_grupo_factura_venta FROM ventas_facturas_inventario_grupos
				WHERE activo=1 AND id_factura_venta=$id_documento AND id_inventario_factura_venta=$id_inventario";
		$query=$mysql->query($sql,$mysql->link);
		$id_grupo = $mysql->result($query,0,'id_grupo_factura_venta');
		if ($id_grupo=='' || $id_grupo==0) { echo "<script>console.log('Item Sin grupo!');</script>"; return; }
		}

		// CONSULTAR LA INFORMACION DEL INVENTARIO
		$descuento = 0;
		$subtotal = 0;
		$sqlwhere = ($id_inventario !== '')? "AND id=$id_inventario" : "AND id  IN(".implode(',', $itemsId).")";

		$sqlFacturasInventario="SELECT cantidad,costo_unitario,tipo_descuento,descuento,valor_impuesto
					FROM ventas_facturas_inventario WHERE activo=1 AND id_factura_venta = $id_documento $sqlwhere";
		$queryFacturasInventario=$mysql->query($sqlFacturasInventario,$mysql->link);
		while ($row = $mysql->fetch_assoc($queryFacturasInventario)) {
			$cantidad       = $row['cantidad'];
			$costo_unitario = $row['costo_unitario'];
			$tipo_descuento = $row['tipo_descuento'];
			$descuentofila  = $row['descuento'];
			$valor_impuesto = $row['valor_impuesto'];

			$subtotal += $cantidad * $costo_unitario;
			if($descuentofila > 0 && $tipo_descuento == 'porcentaje'){
				$descuento += round(($subtotal * $descuentofila / 100),$_SESSION['DECIMALESMONEDA']);
			}else{
				$descuento +=  round($descuentofila,$_SESSION['DECIMALESMONEDA']);
			}
			$subtotalNeto += round(($subtotal - $descuento),$_SESSION['DECIMALESMONEDA']);

			$impuesto += round((($subtotalNeto * $valor_impuesto) / 100),$_SESSION['DECIMALESMONEDA']);
		
		}


		$total = $subtotalNeto + $impuesto;

		// AGREGAR VALORES A LOS CAMPOS
		if ($accion=='sumar') {
			$sql="UPDATE ventas_facturas_grupos SET
						descuento      = descuento+$descuento,
						valor_impuesto = valor_impuesto+$impuesto,
						costo_unitario = costo_unitario+($subtotal)
 					WHERE activo=1 AND id_factura_venta=$id_documento AND id=$id_grupo AND id_empresa=$id_empresa";
			$query=$mysql->query($sql,$mysql->link);
		}

		// RESTAR VALORES A LOS CAMPOS
		else if ($accion=='restar') {
			$sql="UPDATE ventas_facturas_grupos SET
						descuento      = descuento-$descuento,
						valor_impuesto = valor_impuesto-$impuesto,
						costo_unitario = costo_unitario-($subtotal)
 					WHERE activo=1 AND id_factura_venta=$id_documento AND id=$id_grupo AND id_empresa=$id_empresa";
			$query=$mysql->query($sql,$mysql->link);
		}
		// echo $sql;
		// CONSULTAR LOS NUEVOS DATOS DEL GRUPO
		$sql="SELECT
					cantidad,
					descuento,
					valor_impuesto,
					costo_unitario
				FROM ventas_facturas_grupos WHERE activo=1 AND id_factura_venta=$id_documento AND id=$id_grupo AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		$cantidad       = $mysql->result($query,0,'cantidad');
		$descuento      = $mysql->result($query,0,'descuento');
		$valor_impuesto = $mysql->result($query,0,'valor_impuesto');
		$costo_unitario = $mysql->result($query,0,'costo_unitario');
		$total_grupo    = $costo_unitario-$descuento;

		// MOSTRAR LOS NUERVOS VALORES
		echo "<script>
				if ( $('#costo_grupo').length > 0 ) $('#costo_grupo').val('$costo_unitario') ;
				if ( $('#descuento_grupo').length > 0 ) $('#descuento_grupo').val('$descuento') ;
				if ( $('#impuesto_grupo').length > 0 ) $('#impuesto_grupo').val('$valor_impuesto') ;

				if ( $('#descuentoArticuloFacturaVenta_$id_grupo').length > 0 ) $('#descuentoArticuloFacturaVenta_$id_grupo').val('$descuento') ;
				if ( $('#costoGrupoFacturaVenta_$id_grupo').length > 0 ) $('#costoGrupoFacturaVenta_$id_grupo').val('$costo_unitario') ;
				if ( $('#costoTotalGrupoFacturaVenta_$id_grupo').length > 0 ) $('#costoTotalGrupoFacturaVenta_$id_grupo').val('$total_grupo') ;

			</script>";

	}

	//========================= AGREGAR ITEMS AL GRUPO =========================//
	function agregarItemsGrupo($id,$codigo,$nombre,$id_documento,$id_grupo,$id_empresa,$mysql){

		$sql="INSERT INTO ventas_facturas_inventario_grupos (id_factura_venta,id_grupo_factura_venta,id_inventario_factura_venta)
				VALUES ($id_documento,$id_grupo,$id)";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$id_row = $mysql->insert_id();

			// ACTUALIZAR LOS TOTALES DEL GRUPO
			actualizaCamposGrupo('sumar',$id_documento,$id,$id_empresa,$mysql);

			// CONSULTAR LA INFORMACION DEL INVENTARIO
			$sql="SELECT codigo,nombre,cantidad,costo_unitario,tipo_descuento,descuento,valor_impuesto
					FROM ventas_facturas_inventario WHERE activo=1 AND id_factura_venta = $id_documento AND id=$id";
			$query=$mysql->query($sql,$mysql->link);
			$codigo         = $mysql->result($query,0,'codigo');
			$nombre         = quitarTildes($mysql->result($query,0,'nombre'));
			$cantidad       = $mysql->result($query,0,'cantidad');
			$costo_unitario = $mysql->result($query,0,'costo_unitario');
			$tipo_descuento = $mysql->result($query,0,'tipo_descuento');
			$descuento      = $mysql->result($query,0,'descuento');
			$valor_impuesto = $mysql->result($query,0,'valor_impuesto');

			$subtotal = $cantidad * $costo_unitario;
			if($descuento > 0 && $tipo_descuento == 'porcentaje'){
				$descuento = round(($subtotal * $descuento / 100),$_SESSION['DECIMALESMONEDA']);
			}

			$subtotalNeto = round(($subtotal - $descuento),$_SESSION['DECIMALESMONEDA']);

			$impuesto = round((($subtotalNeto * $valor_impuesto) / 100),$_SESSION['DECIMALESMONEDA']);
			$total = $subtotalNeto + $impuesto;


			echo "<script>
					MyLoading2('off',{duracion:300});
					MyBusquedabuscarItemsGruposFacturaVenta();
					//Elimina_Div_buscarItemsGruposFacturaVenta($id);
					var addTd = `<tr id='tr_items_$id'>
									<td>$codigo</td>
									<td>$nombre</td>
									<td>$cantidad</td>
									<td>$descuento</td>
									<td>$subtotal</td>
									<td>$total</td>
									<td ><img src='img/delete.png' title ='Eliminar Item' onclick='eliminarItemGrupo($id);'></td>
								</tr>`;
					$('#items_grupos').append(addTd);

					if($('#bodyDivArticulosFacturaVenta_'+$(\"[value='$id']\")[0])){
						$('#bodyDivArticulosFacturaVenta_'+$(\"[value='$id']\")[0].id.split('_')[1]).appendTo('#content-group-$id_grupo');
					}
				</script>";
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al agregar el item',icono:'fail'})</script>";
		}
	}

	//======================= ELIMINAR ITEMS DE UN GRUPO =======================//
	function eliminarItemGrupo($id_documento,$opcGrillaContable,$id_grupo,$id_inventario,$id_empresa,$mysql){
		// ACTUALIZAR LOS TOTALES DEL GRUPO
		actualizaCamposGrupo('restar',$id_documento,$id_inventario,$id_empresa,$mysql);

		$sql="DELETE FROM ventas_facturas_inventario_grupos WHERE activo=1 AND id_factura_venta=$id_documento AND id_grupo_factura_venta=$id_grupo AND id_inventario_factura_venta=$id_inventario";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off',{duracion:300});
					// MOVER EL DIV DEL ITEM FUER DEL DIV DEL GRUPO
					$('#bodyDivArticulosFacturaVenta_'+contArticulosFacturaVenta).before($('#bodyDivArticulosFacturaVenta_'+$(\"[value='$id_inventario']\")[0].id.split('_')[1]));
					$('#tr_items_$id_inventario').remove();
				</script>";
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al quitar el item del grupo',icono:'fail'})</script>";
		}
	}

	function quitarTildes($cadena){
		$caracterEspecial = array("\t","\r","\n",chr(160),"`");
		$originales  = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ°ª&º/';
	    $modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyoayo/';
	    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
		$cadena = str_replace($caracterEspecial,"",$cadena);
	    return utf8_encode($cadena);
	}
	//============================ DOCUMENTOS ANEXOS ===========================//
	//**************************************************************************//

	//======================= DESCARGAR DOCUMENTO ANEXO ========================//
	function downloadFile($nameFile,$ext,$id,$id_empresa,$id_host){
		$nombreImage = md5($nameFile).'_'.$id.'.'.$ext;

		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/facturacion/'.$nombreImage)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/facturacion/'.$nombreImage;
		}	else{
			$url = '';
		}

		if(file_exists($url)){
			header('Content-Disposition: attachment; filename='.$nameFile.'.'.$ext);
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($url));
			ob_clean();
			flush();
			readfile($url);
		} else{
			echo "Error, el archivo no se encuentra almacenado ";
		}

		exit;
	}

	//===================== VER LISTA DE DOCUMENTOS ANEXOS =====================//
	function ventanaViewDocumento($nameFile,$ext,$id,$id_host){
		$nombreImage = md5($nameFile) . '_' . $id . '.' . $ext;

		if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/ARCHIVOS_PROPIOS/empresa_' . $id_host . '/ventas/facturacion/' . $nombreImage)){
			$url = '../../ARCHIVOS_PROPIOS/empresa_' . $id_host . '/ventas/facturacion/' . $nombreImage;
		}
		else{
			$url = '';
		}

		if($ext != 'pdf'){
			echo '<div style="margin:0px; width:100%; height:100%; background-color:#FFF; display:table">
							<div style="display: table-cell; vertical-align: middle; text-align:center;">
								<img src="'.$url.'" id="imagenItems">
							</div>
							<a class="btn_guardar_anexo" href="'.$url.'" download="'.$nameFile.'" title="Click para descargar">
								<img src="../../temas/clasico/images/BotonesTabs/guardar.png" style="margin-top: 3px;"/>
								<div style="color:#000; text-align: center;">Descargar</div>
							</a>
						</div>
				<script>
					document.getElementById("imagenItems").oncontextmenu = function(){ return false; }
				</script>
				<style>
					.btn_guardar_anexo{
						opacity     					: 0.4;
						position    					: absolute;
						width       					: 68px;
						height      					: 55px;
						top         					: 5;
						left        					: 10;
						overflow    					: hidden;
						text-align  					: center;
						color       					: #333;
						padding     					: 0px;
						margin      					: 0px;
						font-weight 					: bold;
						border      					: 3px solid #000;
						background-color      : #FFF;
						-moz-border-radius    : 1px;
						-webkit-border-radius : 1px;
						background 						: -webkit-linear-gradient(#FFF, #CECECE);
						background 						: -moz-linear-gradient(#FFF, #CECECE);
						background 						: -o-linear-gradient(#FFF, #CECECE);
						background 						: linear-gradient(#FFF, #CECECE);
						-webkit-box-shadow		: 4px 7px 45px 2px rgba(255,255,255,1);
						-moz-box-shadow				: 4px 7px 45px 2px rgba(255,255,255,1);
						box-shadow						: 4px 7px 45px 2px rgba(255,255,255,1);
					}

					.btn_guardar_anexo:hover{
						opacity : 1;
						-webkit-animation 				: cssAnimation 1s 1 ease-in;
						-moz-animation    				: cssAnimation 1s 1 ease-in;
						-o-animation      				: cssAnimation 1s 1 ease-in;
						animation-iteration-count : 1;
					}

					@-webkit-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}

					@-moz-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}

					@-o-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}
				</style>';
		}
		else{
			echo '<div style="margin:0px; width:100%; height:100%; background-color:#FFF; display:table">
							<div style="display: table-cell; vertical-align: middle; text-align:center;">
								<iframe src="'.$url.'" id="iframeViewDocumentItems"></iframe>
							</div>
						</div>
						<script>
							cambiaViewPdf();

							function cambiaViewPdf(){
								var iframe=document.getElementById("iframeViewDocumentItems");
								iframe.setAttribute("width",Ext.getBody().getWidth()-110);
								iframe.setAttribute("height",Ext.getBody().getHeight()-150);
							}
						</script>';
		}
	}

	//================ CONSULTAR DIMENSIONES DEL DOCUMENTO ANEXO ===============//
	function consultaSizeDocumento($nameFile,$ext,$id,$id_host){
		$nameFile = md5($nameFile).'_'.$id.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/facturacion/'.$nameFile)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/facturacion/'.$nameFile;
		}
		else{
			$url = '';
		}

		list($size['ancho'], $size['alto'], $tipo, $atributos) = getimagesize($url);
		echo json_encode($size);
	}

	//======================== BORRAR UN DOCUMENTO ANEXO =======================//
	function deleteDocumentoVentasFacturas($id_host,$idDocumento,$nombre,$ext,$link){
		$nombreImage = md5($nombre).'_'.$idDocumento.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/facturacion/'.$nombreImage)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/facturacion/'.$nombreImage;
		} else{
			$url = '';
		}

		$sqlDelete   = "UPDATE ventas_facturas_documentos SET activo = 0 WHERE id = $idDocumento";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){
			echo '<script>
							alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");
						</script>';
			exit;
		} else{
			unlink($url);
			echo "<script>
							Elimina_Div_ventasFacturasDocumentos($idDocumento);
						</script>";
			exit;
		}
	}
?>
