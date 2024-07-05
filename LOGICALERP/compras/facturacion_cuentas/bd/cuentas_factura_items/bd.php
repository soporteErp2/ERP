<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	require("contabilizar_bd.php");
	require("contabilizar_niif_bd.php");
	require("functions_body_article.php");

	$cuentaPago = 0;
	$cuentaPagoNiif = 0;
	$saldoGlobalFactura = 0;
	$saldoGlobalFacturaSinAbono = 0;

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	//=========================== FUNCION PARA TERMINAR LA FACTURA Y CARGAR UNA NUEVA ==============================================//
	function terminarFacturaCompra($idPlantilla,$idProveedor,$idFactura,$prefijoFactura,$numeroFactura,$id_empresa,$id_sucursal,$idBodega,$observacion,$link){
		validaEstadoFactura($idFactura,$link);
		global $saldoGlobalFactura, $saldoGlobalFacturaSinAbono, $cuentaPago, $cuentaPagoNiif;

		$sqlSaldoRemision   = "UPDATE compras_facturas_inventario SET saldo_cantidad=cantidad WHERE id_factura_compra='$idFactura' AND id_empresa='$id_empresa'";
		$querySaldoRemision = mysql_query($sqlSaldoRemision,$link);

		//===================================== CREACION DE ARRAY DOCUMENTOS DE REFERENCIA =============================================//
		/********************************************************************************************************************************/
		$arraySindDoc        = '';
		$arrayOrdenCompra    = '';
		$arrayEntradaAlmacen = '';

		$contOrdenCompra    = 0;
		$contEntradaAlmacen = 0;
		$contSinDoc         = 0;

		$acumIdOrdenCompra    = '';		//CONDICIONAL GLOBAL WHERE SQL IDS ORDEN DE COMPRA
		$acumIdEntradaAlmacen = '';		//CONDICIONAL GLOBAL WHERE SQL IDS ENTRADA DE ALMACEN

		$sqlDocumentoAdjunto = "SELECT id_consecutivo_referencia AS id_referencia, nombre_consecutivo_referencia AS nombre_referencia
								FROM compras_facturas_inventario
								WHERE id_factura_compra='$idFactura' AND activo=1
								GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";

		$queryDocumentoAdjunto = mysql_query($sqlDocumentoAdjunto,$link);
		while($rowDoc = mysql_fetch_array($queryDocumentoAdjunto)){

			$id_referencia     = $rowDoc['id_referencia'];
			$nombre_referencia = $rowDoc['nombre_referencia'];
			$arrayResult       = array('id_referencia' => $id_referencia, 'nombre_referencia' => $nombre_referencia);

			if($id_referencia > 0){																								//CON DOCUMENTO DE REFERENCIA
				if($nombre_referencia == 'Orden de Compra'){ $contOrdenCompra++; $arrayOrdenCompra[$contOrdenCompra] = $arrayResult; }
				else if($nombre_referencia == 'Entrada de Almacen'){ $contEntradaAlmacen++; $arrayEntradaAlmacen[$contEntradaAlmacen] = $arrayResult; }
			}
			else{ $contSinDoc++; $arraySindDoc[$contSinDoc][$id_referencia] = $nombre_referencia; }								//SIN DOCUMENTO DE REFERENCIA
		}

		//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
		/**********************************************************************************/
		if($contOrdenCompra>0){																									//VALIDACION ORDEN DE COMPRA
			for($cont=1; $cont<=$contOrdenCompra; $cont++) {
				$acumIdOrdenCompra .= ($acumIdOrdenCompra=='')? "id=":" OR id=";
				$acumIdOrdenCompra .= $arrayOrdenCompra[$cont]['id_referencia'];
			}

			$sqlEstadoCotizacion   = "SELECT consecutivo,estado,activo FROM compras_ordenes_inventario WHERE id_empresa=$id_empresa AND ($acumIdOrdenCompra)";
			$queryEstadoCotizacion = mysql_query($sqlEstadoCotizacion);
			while ($rowEstadoCotizacion = mysql_fetch_array($queryEstadoCotizacion)) {
				if($rowEstadoCotizacion['estado']==3){
					echo '<script>
							alert("Error!\nLa Orden codigo '.$rowEstadoCotizacion['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>'; exit;
				}
			}
		}

		// if($contEntradaAlmacen>0){																										//VALIDACION PEDIDO
		// 	for($cont=1; $cont<=$contEntradaAlmacen; $cont++) {
		// 		$acumIdEntradaAlmacen .= ($acumIdEntradaAlmacen=='')? "id=":" OR id=";
		// 		$acumIdEntradaAlmacen .= $arrayEntradaAlmacen[$cont]['id_referencia'];
		// 	}

		// 	$sqlEstadoEntradaAlmacen   = "SELECT consecutivo,estado,activo FROM compras_entradas_almacen WHERE id_empresa=$id_empresa AND ($acumIdEntradaAlmacen)";
		// 	$queryEstadoEntradaAlmacen = mysql_query($sqlEstadoEntradaAlmacen);
		// 	while ($rowEstadoEntradaAlmacen = mysql_fetch_array($queryEstadoEntradaAlmacen)) {
		// 		if($rowEstadoEntradaAlmacen['estado']==3){
		// 			echo '<script>alert("Error!\nLa entrada almacen codigo '.$rowEstadoEntradaAlmacen['consecutivo'].' esta Cancelado\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")</script>'; exit;
		// 		}
		// 	}

		// 	// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES A LA REMISION ==>
		// 	$sqlValidaSaldo = " SELECT COUNT(TI.id) AS cont_validate_saldo
		// 						FROM compras_facturas_inventario AS TI, compras_entradas_almacen_inventario AS TS
		// 						WHERE TI.id_factura_compra='$idFactura' AND TI.activo = 1 AND TS.activo = 1 AND TI.nombre_consecutivo_referencia='Entrada de Almacen' AND TS.id=TI.id_tabla_inventario_referencia
		// 							AND TI.cantidad > TS.saldo_cantidad
		// 						GROUP BY TI.id";

		// 	$contValidateSaldo = mysql_result(mysql_query($sqlValidaSaldo,$link),0,'cont_validate_saldo');
		// 	if($contValidateSaldo > 0){ echo '<script>alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en la remision que se adjunto en la presente factura")</script>'; return; }

		// }

		$sqlFacturaCompra   = "SELECT COUNT(id) AS cont_factura,fecha_inicio,estado,activo,id_configuracion_cuenta_pago,cuenta_pago,cuenta_pago_niif,contabilidad_manual,consecutivo
								FROM compras_facturas
								WHERE id_empresa=$id_empresa AND id='$idFactura'";
		$queryfacturaCompra = mysql_query($sqlFacturaCompra,$link);

		$consecutivoFactura  = mysql_result($queryfacturaCompra,0,'consecutivo');
		$contFactura         = mysql_result($queryfacturaCompra,0,'cont_factura');
		$estadoFactura       = mysql_result($queryfacturaCompra,0,'estado');
		$activoFactura       = mysql_result($queryfacturaCompra,0,'activo');
		$fechaInicioFactura  = mysql_result($queryfacturaCompra,0,'fecha_inicio');
		$contabilidad_manual = mysql_result($queryfacturaCompra,0,'contabilidad_manual');

		$idCuentaPago   = mysql_result($queryfacturaCompra,0,'id_configuracion_cuenta_pago');
		$cuentaPago     = mysql_result($queryfacturaCompra,0,'cuenta_pago');
		$cuentaPagoNiif = mysql_result($queryfacturaCompra,0,'cuenta_pago_niif');

		if(!$queryfacturaCompra){
			echo '<script>
						alert("Error!\nNo se ha establecido la comunicacion con el servidor.");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					return;
		}
		else if($contFactura == 0 || $activoFactura == 0){
			echo '<script>
						alert("Aviso!\nLa factura se encuentra cancelada");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else if($estadoFactura > 0){
			echo '<script>
					alert("Aviso!\nLa factura no se encuentra disponible para generar");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}

		//CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Compra'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

		//=================================== UPDATE ======================================//
		/***********************************************************************************/
		$prefijoFactura = str_replace(" ", "", $prefijoFactura);  // QUITAMOS LOS ESPACIOS VACIOS DEL PREFIJO
		$consecutivoDocReferencia = (strlen($prefijoFactura) > 0)? $prefijoFactura.' '.$numeroFactura: $numeroFactura; // COMBINACION DE PREFIJO + NUMERO FACTURA

		// CONTABILIZACION FACTURA
		contabilizar($arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link);
		contabilizarNiif($arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link);


		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
					VALUES ($idFactura,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Agregar configuracion de cuentas factura de compras',$id_sucursal,'$id_empresa')";
		$queryLog = mysql_query($sqlLog,$link);

	}

	//FUNCION PARA VALIDAR EL ESTADO DE LA FACTURA CUANDO SE GUARDA,ACTUALIZA,ELIMINA UN ITEM
	function validaEstadoFactura($idFactura,$link){
		$sql       = "SELECT estado,id_bodega FROM compras_facturas WHERE id=$idFactura";
		$query     = mysql_query($sql,$link);
		$estado    = mysql_result($query,0,'estado');
		$id_bodega = mysql_result($query,0,'id_bodega');

		if ($estado == 1) {
			echo'<script>
					alert("Error!\nEl documento ya ha sido generado\nNo se puede realizar mas acciones sobre el");

					if(document.getElementById("Win_Ventana_update_valores_FacturaCompra")){ Win_Ventana_update_valores_FacturaCompra.close(); }
					if(document.getElementById("Win_Ventana_descripcion_Articulo_factura")){ Win_Ventana_descripcion_Articulo_factura.close(); }

					Ext.get("contenedor_facturacion_compras").load({
						url     : "facturacion/facturacion_compras_bloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_compra : "'.$idFactura.'",
							filtro_bodega     : "'.$id_bodega.'"
						}
					});

					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else if ($estado == 3) {
			echo'<script>
					alert("Error!\nEl documento a sido cancelado\nNo se puede realizar mas acciones sobre el");

					if(document.getElementById("Win_Ventana_update_valores_FacturaCompra")){ Win_Ventana_update_valores_FacturaCompra.close(); }
					if(document.getElementById("Win_Ventana_descripcion_Articulo_factura")){ Win_Ventana_descripcion_Articulo_factura.close(); }

					Ext.get("contenedor_facturacion_compras").load({
						url     : "facturacion/facturacion_compras_bloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_compra : "'.$idFactura.'",
							filtro_bodega     : "'.$id_bodega.'"
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
	}

?>