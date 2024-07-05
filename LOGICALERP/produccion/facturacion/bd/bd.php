<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../config_var_global.php");
	include("../../facturacion/bd/contabilizar_bd.php");
	include("../../facturacion/bd/contabilizar_niif_bd.php");
	include("../../bd/functions_body_article.php");

	$cuentaPago                 = 0;
	$cuentaPagoNiif             = 0;
	$saldoGlobalfactura         = 0;
	$saldoGlobalFacturaSinAbono = 0;

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

		case 'UpdateCuentaPago':
			verificaEstadoDocumento($id,$opcGrillaContable,$link);
			UpdateCuentaPago($id,$idCuentaPago,$link);
			break;

		case 'UpdateIdPlantilla':
			verificaEstadoDocumento($id,$opcGrillaContable,$link);
			UpdateIdPlantilla($id,$idPlantilla,$link);
			break;

		case 'ventanaAnticipoParcial':
			ventanaAnticipoParcial($opcGrillaContable);
			break;

		case 'anticipoParcial':
			anticipoParcial($opcGrillaContable,$id_factura,$cuenta_anticipo,$valor_anticipo,$id_empresa,$link);
			break;

		case 'anticipoTotal':
			anticipoTotal($opcGrillaContable,$id_factura,$cuenta_anticipo,$valor_anticipo,$id_empresa,$link);
			break;

		case 'cancelarAnticipoFactura':
			cancelarAnticipoFactura($opcGrillaContable,$id_factura,$id_empresa,$link);
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
	}


	function terminarGenerar($id,$id_empresa,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$id_empresa,$idPlantilla,$fechaFactura,$link){
		global $saldoGlobalfactura, $saldoGlobalFacturaSinAbono, $cuentaPago, $cuentaPagoNiif;

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
					echo '<script>alert("Error!\nLa cotizacion codigo '.$rowEstadoCotizacion['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")</script>'; exit;
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
					echo '<script>alert("Error!\nEl pedido codigo '.$rowEstadoPedido['consecutivo'].' esta Cancelado\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")</script>'; exit;
				}
			}

			// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES AL PEDIDO ==>
			$sqlValidaSaldo = " SELECT COUNT(TI.id) AS cont_validate_saldo
								FROM ventas_facturas_inventario AS TI, ventas_pedidos_inventario AS TS
								WHERE TI.id_factura_venta='$id' AND TI.activo = 1 AND TS.activo = 1 AND TI.nombre_consecutivo_referencia='Pedido' AND TS.id=TI.id_tabla_inventario_referencia
									AND TI.cantidad > TS.saldo_cantidad
								GROUP BY TI.id";
			$contValidateSaldo = mysql_result(mysql_query($sqlValidaSaldo,$link),0,'cont_validate_saldo');
			if($contValidateSaldo > 0){ echo '<script>alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en el pedido que se adjunto en la presente Factura")</script>'; return; }


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
					echo '<script>alert("Error!\nLa remision codigo '.$rowEstadoRemision['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")</script>'; exit;
				}
			}


			// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES A LA REMISION ==>
			$sqlValidaSaldo = " SELECT COUNT(TI.id) AS cont_validate_saldo
								FROM ventas_facturas_inventario AS TI, ventas_remisiones_inventario AS TS
								WHERE TI.id_factura_venta='$id' AND TI.activo = 1 AND TS.activo = 1 AND TI.nombre_consecutivo_referencia='Remision' AND TS.id=TI.id_tabla_inventario_referencia
									AND TI.cantidad > TS.saldo_cantidad
								GROUP BY TI.id";
			$contValidateSaldo = mysql_result(mysql_query($sqlValidaSaldo,$link),0,'cont_validate_saldo');
			if($contValidateSaldo > 0){ echo '<script>alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en la remision que se adjunto en la presente factura")</script>'; return; }
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

		$cuentaAnticipo = mysql_result($queryFactBd,0,'cuenta_anticipo');
		$valorAnticipo  = mysql_result($queryFactBd,0,'valor_anticipo');
		$exento_iva     = mysql_result($queryFactBd,0,'exento_iva');

		$arrayAnticipo  = array('cuenta' => $cuentaAnticipo, 'valor' => $valorAnticipo);

		if(!$queryFactBd){ echo '<script>alert("Aviso! No 1,\nSin conexion con la base de datos!");</script>'; exit; }			// ERROR QUERY
		else if($estadoFactBd == 3){ echo '<script>alert("Aviso! No 2,\nLa factura ha sido cancelada");</script>'; exit; }		// FACTURA CANCELADA
		else if ($activoFactBd == 0){ echo '<script>alert("Aviso! No 3,\nLa factura ha sido eliminada");</script>'; exit; }		// FACTURA ELIMINADA

		//CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Venta'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

		$sqlConsulDoc = "SELECT numero_factura AS valor, prefijo,documento_cruce
								FROM ventas_facturas
								WHERE activo=1  AND id_empresa='$id_empresa' AND id='$id'
								LIMIT 0,1";
		$queryConsulDoc=mysql_query($sqlConsulDoc,$link);
		$id_documento_cruce = mysql_result($queryConsulDoc,0,'documento_cruce');

		if ($id_documento_cruce>0) {
			// UPDATE CONSECUTIVO FACTURA ==>
			$updateEstadoFactura = "UPDATE ventas_facturas SET estado = 1, fecha_contabilizado=NOW() WHERE id='$id'";
			$queryEstadoFactura  = mysql_query($updateEstadoFactura,$link);
			echo'<script>
				//PONER EL NUMERO DE LA FACTURA EN EL TITULO DEL DOCUMENTO
				// document.getElementById("titleDocumentoFacturaVenta").innerHTML="Factura de Venta<br>N. '.$consecutivoFactura.'";

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
			</script>';
			exit;
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
			contabilizarSinPlantilla($arrayCuentaPago,$idCcos,$arrayAnticipo,$fechaFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);
			contabilizarSinPlantillaNiif($arrayCuentaPago,$idCcos,$arrayAnticipo,$fechaFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$id,$idCliente,$exento_iva,$link);
		}

		// UPDATE CONSECUTIVO FACTURA ==>
		$updateEstadoFactura = "UPDATE ventas_facturas SET estado = 1, fecha_contabilizado=NOW() WHERE id='$id'";
		$queryEstadoFactura  = mysql_query($updateEstadoFactura,$link);

		if(is_nan($newNumFactBd) || $newNumFactBd == 0){

			// SELECT NUMERO DE FACTURA ==>
			$sqlNumActualFact = "SELECT numero_factura AS valor, prefijo,documento_cruce
								FROM ventas_facturas
								WHERE activo=1  AND id_empresa='$id_empresa' AND id='$id'
								LIMIT 0,1";
			$queryActualFact = mysql_query($sqlNumActualFact,$link);

			$newPrefijoFac   = mysql_result($queryActualFact,0,'prefijo');
			$newNumFactBd    = mysql_result($queryActualFact,0,'valor');
			if (!$queryActualFact) { echo '<script>alert("Error! No 4,\nSin conexion con la base de datos!");</script>'; exit; }

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
								total_factura_sin_abono = '$saldoGlobalFacturaSinAbono'
							WHERE id = '$id' AND id_empresa = '$id_empresa'";
		$queryGeneraFact = mysql_query($sqlGeneraFact,$link);
		if ($queryGeneraFact) { actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'eliminar',$id_empresa,$link); }
		else{ echo '<script>alert("Error!,\nNo se finalizo la factura\nSi el problema continua comuniquese con el administrador del sistema");</script>'; return; }

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

		//INSERTAR EL LOG DE EVENTOS
			$sqlLog="INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
					VALUES
					($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Factura de Venta',$id_sucursal,".$_SESSION['EMPRESA'].")";
			$queryLog=mysql_query($sqlLog,$link);

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
			</script>';
	}

	//================= VALIDACION CANTIDAD DE ARTICULOS A DESCONTAR DE INVENTARIO ===================//
	/**************************************************************************************************/
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

		if(!$queryArticulo){ echo'<script>alert("Error.\nNo se contabilizo los articulos al inventario");</script>'; exit; }
		else if ($codigo > 0){ echo'<script>alert("Aviso.\nHay '.round($sumaCantidad).' unidades del inventario codigo '.$codigo.', lo maximo permitido en ventas de este inventario es '.$cantidadPermitida.' unidades.");</script>'; exit; }
	}

	//======================= FUNCION SUMA O RESTA INVENTARIOS TOTALES Y SALDO ==========================//
	/*****************************************************************************************************/
	function actualizaCantidadArticulos($idFactura,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opc,$id_empresa,$link){
		if ($opc=='eliminar'){
			$sql   = "UPDATE inventario_totales AS IT, (
							SELECT SUM(cantidad) AS total_factura_venta, id_inventario AS id_item
							FROM ventas_facturas_inventario
							WHERE id_factura_venta='$idFactura'
								AND activo=1
								AND inventariable='true'
								AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )
							GROUP BY id_inventario) AS VFI
						SET IT.cantidad=IT.cantidad-VFI.total_factura_venta
						WHERE IT.id_item=VFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$idBodega'";

			$query = mysql_query($sql,$link);
			if(!$query){ echo'<script>alert("Error.\nNo se contabilizo los articulos al inventario");</script>'; exit; }
		}
		else if ($opc=='agregar'){

			$sql   = "UPDATE inventario_totales AS IT, (
							SELECT SUM(cantidad) AS total_factura_venta, id_inventario AS id_item
							FROM ventas_facturas_inventario
							WHERE id_factura_venta='$idFactura'
								AND activo=1
								AND inventariable='true'
								AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )
							GROUP BY id_inventario) AS VFI
						SET IT.cantidad=IT.cantidad+VFI.total_factura_venta
						WHERE IT.id_item=VFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$idBodega'";

			$query = mysql_query($sql,$link);
			if(!$query){ echo'<script>alert("Error.\nNo se contabilizo los articulos al inventario");</script>'; exit; }
		}
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

		$whereRemision = ($typeDoc == 'remision')? "AND CO.pendientes_facturar > 0 AND COI.saldo_cantidad > 0": "";

		//VALIDACION ESTADO DE LA FACTURA
		$idClienteDocAgregar    = '';
		$estadoDocAgregar       = '';
		$sqlValidateDocumento   = "SELECT id_cliente,estado,id,observacion $campoSelect FROM $tablaCarga WHERE consecutivo='$codDocAgregar' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' ";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);

		$idClienteDocAgregar = mysql_result($queryValidateDocumento,0,'id_cliente');
		$idDocumentoAgregar  = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar    = mysql_result($queryValidateDocumento,0,'estado');
		$idCcosDocLoad       = mysql_result($queryValidateDocumento,0,'id_centro_costo');
		$observacion         = mysql_result($queryValidateDocumento,0,'observacion');

		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' esta cancelado");</script>'; return; }
		else if($idClienteDocAgregar <> $idClienteFactura){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' pertenese a un cliente diferente.");</script>'; return; }
		else if($typeDoc == "remision" && $idCcosDocLoad<>$idCcostoDB){ echo '<script>alert("Aviso!,\nLa Remision N. '.$codDocAgregar.' pertenese a un Centro de costo diferente.");</script>'; return; }

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
                                        COI.valor_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
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
                                            nombre_consecutivo_referencia)
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
                                        '$referencia_consecutivo')";
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
				<div style="width:80px; float:left; margin-top:10px;">Usuario</div>
				<div style="width:calc(100% - 80px); float:left; margin-top:10px;"><input type="text" id="usuario_updateFechafactura"/></div>
				<div style="width:80px; float:left; margin-top:10px;">Password</div>
				<div style="width:calc(100% - 80px); float:left; margin-top:10px;"><input type="password" id="password_updateFechafactura"/></div>
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
		$password     = md5($password);
		$sqlUsuario   = "SELECT COUNT(id) AS cont_usuario FROM empleados WHERE id_empresa='$id_empresa' AND username='$usuario' AND password='$password' and activo=1 LIMIT 0,1";
		$queryUsuario = mysql_query($sqlUsuario,$link);
		$contUsuario  = mysql_result($queryUsuario,0,'cont_usuario');

		if(!$queryUsuario || $contUsuario == 0){ echo'<script>alert("Aviso,\nNombre de usuario o password incorrectos");</script>'; exit; }

		$sqlInsert   = "INSERT INTO ventas_facturas_update_fecha (fecha,hora,usuario,id_factura,id_empresa) VALUES (NOW(),NOW(),'$usuario','$idFacturaVenta','$id_empresa')";
		$queryInsert = mysql_query($sqlInsert,$link);

		if(!$queryInsert){ echo'<script>alert("Aviso,\nError de conexion con la base de datos");</script>'; exit; }

		echo'<script>
				document.getElementById("fecha'.$opcGrillaContable.'").value = "'.$fecha.'";
				Win_Ventana_update_fecha_'.$opcGrillaContable.'.close();
			</script>';
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

	function anticipoParcial($opcGrillaContable,$id_factura,$cuenta_anticipo,$valor_anticipo,$id_empresa,$link){
		$sql   = "UPDATE ventas_facturas SET cuenta_anticipo='$cuenta_anticipo', valor_anticipo='$valor_anticipo' WHERE id='$id_factura' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }

		echo'<script>
				document.getElementById("anticipo_cliente_'.$opcGrillaContable.'").value="'.$valor_anticipo.'";
				Win_Ventana_anticipo_parcial_'.$opcGrillaContable.'.close();
				Win_Ventana_cuenta_anticipo_'.$opcGrillaContable.'.close();
			</script>';
	}

	function anticipoTotal($opcGrillaContable,$id_factura,$cuenta_anticipo,$valor_anticipo,$id_empresa,$link){
		$sql   = "UPDATE ventas_facturas SET cuenta_anticipo='$cuenta_anticipo', valor_anticipo='$valor_anticipo' WHERE id='$id_factura' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; exit; }

		echo'<script>
				document.getElementById("anticipo_cliente_'.$opcGrillaContable.'").value="'.$valor_anticipo.'";
				Win_Ventana_cuenta_anticipo_'.$opcGrillaContable.'.close();
			</script>';
	}

	function cancelarAnticipoFactura($opcGrillaContable,$id_factura,$id_empresa,$link){
		$sql   = "UPDATE ventas_facturas SET cuenta_anticipo='', valor_anticipo='0' WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; exit; }

		echo'<script>
				document.getElementById("anticipo_cliente_'.$opcGrillaContable.'").value="0";
				Win_Ventana_cuenta_anticipo_'.$opcGrillaContable.'.close();
			</script>';
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
	}


	//BUSQUEDA DE LA GRILLA MANUAL
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
								// console.log("'.$sqlCuentas.'");
								// console.log(arrayLimitGrilla'.$opcGrillaContable.');
								document.getElementById("labelPaginacion").innerHTML="Pagina '.$pagina.' de '.$paginas.' ";
								PaginaActual'.$opcGrillaContable.'='.$pagina.';
								MaxPage'.$opcGrillaContable.'='.$paginas.';
								arrayLimitGrilla'.$opcGrillaContable.'.length=0;
								'.$acumScript.'
								// console.log(arrayLimitGrilla'.$opcGrillaContable.');
								// console.log("'.$limit.'");
								'.$imprimeVar.'

							</script>';

			echo $filaInsertBoleta;

	}


	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$link){
		$sql="SELECT estado,id_bodega,numero_factura_completo AS consecutivo FROM ventas_facturas WHERE id=$id_documento";
		$query=mysql_query($sql,$link);

		$estado    = mysql_result($query,0,'estado');
		$id_bodega = mysql_result($query,0,'id_bodega');
		$consecutivo = mysql_result($query,0,'consecutivo');
		if ($estado==1) {
			$mensaje='Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==2) {
			$mensaje='Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==3) {
			$mensaje='Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el';
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

?>
