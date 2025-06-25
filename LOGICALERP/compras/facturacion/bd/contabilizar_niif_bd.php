<?php
	function estadoCuentaNiif($descripcion){
		switch ($descripcion) {
			case 'PRECIO':
				return 'debito';
				break;

			case 'IMPUESTO':
				return 'debito';
				break;

			case 'CONTRAPARTIDA PRECIO':
				return 'credito';
				break;

			default:
				return 'error';
				break;
		}
	}

  function contabilizarConPlantillaNiif($fechaInicioFactura ,$consecutivoFactura,$idBodega,$idSucursal,$idEmpresa,$idPlantilla,$idFactura,$idProveedor,$link){
	  	$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;
	  	global $cuentaPagoNiif;

  		//PLANTILLA CONFIGURACION
		$sqlPlantilla   = "SELECT codigo_niif,descripcion,porcentaje FROM plantillas_configuracion WHERE plantillas_id='$idPlantilla' AND activo=1";
		$queryPlantilla = mysql_query($sqlPlantilla,$link);

		$tablaDebug = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';

		$porcentajeIva        = 0;
		$contCuentasPantillas = 0;
		while($rowplantilla = mysql_fetch_array($queryPlantilla)){

			$contCuentasPantillas++;
			$estadoCuenta = estadoCuentaColgaap($rowplantilla['descripcion']);

			$idCuenta = $rowplantilla['id'];
			$cuenta   = $rowplantilla['codigo_niif'];
			$arrayCuentas[$cuenta] = array('puc' => $cuenta, 'caracter' => $estadoCuenta, 'descripcion' => $rowplantilla['descripcion']);

			if($rowplantilla['descripcion'] == 'IMPUESTO'){
				if($rowplantilla['porcentaje'] == ''){ echo'<script>alert("Aviso.\nLa plantilla Niif ingresada tiene iva sin porcentaje.")</script>'; exit; }
				$porcentajeIva = $rowplantilla['porcentaje'];
			}

			$valueInsertContabilizacion .= "('$cuenta',
											'$estadoCuenta',
											'".$rowplantilla['descripcion']."',
											'".$rowplantilla['porcentaje']."',
											'$idFactura',
											'FC',
											'$idEmpresa',
											'$idSucursal',
											'$idBodega'),";

			if($rowplantilla['descripcion'] == 'CONTRAPARTIDA PRECIO'){ $cuentaPagoNiif = $cuenta; }
		}

		if($contCuentasPantillas == 0){ echo'<script>alert("Aviso.\nLa plantilla Niif ingresada no tiene configuracion contable.")</script>'; exit; }
		else if($contCuentasPantillas == 1){ echo'<script>alert("Aviso.\nLa plantilla Niif ingresada no cumple doble partida.")</script>'; exit; }

		//VENTAS FACTURA INVENTARIO
		$sqlArticulos = "SELECT id,
								cantidad,
								costo_unitario AS precio,
								tipo_descuento,
								descuento,
								id_inventario AS id_item,
								nombre AS nombre_equipo,
								nombre_consecutivo_referencia AS doc_referencia,
								check_opcion_contable,
								opcion_contable
						FROM compras_facturas_inventario
						WHERE activo=1 AND id_factura_compra='$idFactura'";
		$queryArticulos = mysql_query($sqlArticulos,$link);

		$precioAcumulado         = 0;
		$ivaAcumulado            = 0;
		$ivaIncluido             = 0;
		$ivaIncluidoAcumulado    = 0;
		$contActivosFijos        = 0;
		$valueInsertActivosFijos = "";
		while($rowArticulo = mysql_fetch_array($queryArticulos)){

			$precio = $rowArticulo['precio']* $rowArticulo['cantidad'];														//CALCULO DEL PRECIO

			if($rowArticulo['descuento'] > 0){
				$precio = ($rowArticulo['tipo_descuento'] == 'porcentaje')? $precio-(($rowArticulo['descuento']*$precio)/100) : $precio-$rowArticulo['descuento'];
			}

			$precioAcumulado += $precio;

			if($rowArticulo['opcion_contable'] == 'true'){
				$ivaIncluido           =  ROUND($precio*$porcentajeIva/100, $decimalesMoneda);
				$ivaIncluidoAcumulado += $ivaIncluido;

				if($rowArticulo['check_opcion_contable'] == 'activo_fijo'){													//SI ES ACTIVO FIJO
					$contActivosFijos++;
					//SI HAY CUENTA IVA SE DISCRIMINA DEL PRECIO
					$precioActivoFijo = $precio;
					if($porcentajeIva > 0){ $precioActivoFijo = $precioActivoFijo + $ivaIncluido; }

					for($i=1; $i <= $rowArticulo['cantidad']; $i++){
						// $valorUnitario = ROUND($precioActivoFijo/$rowArticulo['cantidad'], $decimalesMoneda);
						$valorUnitario = ROUND($precioActivoFijo, $decimalesMoneda);
						$valueInsertActivosFijos .= "('$idEmpresa',
														'$idSucursal',
														'$idBodega',
														'".$rowArticulo['id_item']."',
														'".$rowArticulo['nombre_equipo']."',
														'$idFactura',
														'".$rowArticulo['id']."',
														'FC',
														'$consecutivoFactura',
														'$valorUnitario',
														NOW(),
														ADDDATE(NOW(), INTERVAL 1 MONTH),
														'$idProveedor'),";
					}
				}
			}
		}

		//SI HAY CUENTA IVA SE DISCRIMINA DEL PRECIO
		if($porcentajeIva > 0){ $ivaAcumulado = ROUND($precioAcumulado*$porcentajeIva/100, $decimalesMoneda); }

		//==================== QUERY RETENCIONES =================//
    	/**********************************************************/
		$acumRetenciones  = 0;
		$sqlRetenciones   = "SELECT valor,codigo_cuenta_niif,tipo_retencion,cuenta_autoretencion_niif,base FROM compras_facturas_retenciones WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);
		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			$valorBase           = $rowRetenciones['base'];
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta_niif'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion_niif'];

			if($arrayRetenciones[$codigoRetencion] == ''){ $arrayRetenciones[$codigoRetencion] = 0; }

			if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
				if($ivaAcumulado<$valorBase) { continue; }															//VALIDACION BASE

				if($porcentajeIva == 0){ echo'<script>alert("Aviso.\nLa plantilla ingresada tiene no cuenta con iva. para continuar quite la RETENCION al iva (Reteiva) o agregue el iva a la plantilla.")</script>'; exit; }
				$acumRetenciones += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$codigoRetencion] += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE Y RETEICA
				if($precioAcumulado<$valorBase) { continue; }															//VALIDACION BASE

				$acumRetenciones += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$codigoRetencion] += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){																	//DEVOLUCION SALDO AUTORETENCION NO RESTA
				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){ echo'<script>alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.")</script>'; exit; }
				$contAutoRetencion++;
				$arrayAutoRetencion[$contAutoRetencion] = $cuentaAutoretencion;
				$acumRetenciones -= ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$cuentaAutoretencion] += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
			}
		}
		$precioAcumulado     += $ivaIncluidoAcumulado;
		$ivaAcumulado        -= $ivaIncluidoAcumulado;
		$cuentaContraPartida  = $precioAcumulado+$ivaAcumulado-$acumRetenciones;
		$valueInsertAsientos  = '';

		$globalDebito  = 0;
		$globalCredito = 0;
		$tablaDebug    = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';
		if($contCuentasPantillas > 1){
			foreach ($arrayCuentas AS $cuentaPuc => $valArrayCuenta){
				$saldo         = 0;
				$configCuenta2 = substr($cuentaPuc, 0, 2);
				$configCuenta4 = substr($cuentaPuc, 0, 4);

				if($valArrayCuenta['descripcion'] == 'CONTRAPARTIDA PRECIO'){ $saldo = $cuentaContraPartida; }		//CAJA, BANCOS, PROVEEDORES, OBLIGACIONES FINANCIERAS
				else if( $valArrayCuenta['descripcion'] == 'IMPUESTO'){ $saldo = $ivaAcumulado; }					//IVA
				else{ $saldo = $precioAcumulado; }														//CONTRAPARTIDA-RETENCIONES

				if($saldo == 0){ continue; }
				$totalDebito  = ($valArrayCuenta['caracter'] == 'debito')? $saldo : 0;
				$totalCredito = ($valArrayCuenta['caracter'] == 'credito')? $saldo : 0;

				$globalDebito  += $totalDebito;
				$globalCredito += $totalCredito;

				$valueInsertAsientos .= "('$idFactura',
											'$consecutivoFactura',
											'FC',
											'$idFactura',
											'$consecutivoFactura',
											'FC',
											'Factura de Compra',
											'$fechaInicioFactura',
											'$totalDebito',
											'$totalCredito',
											'$cuentaPuc',
											'$idProveedor',
											'$idSucursal',
											'$idEmpresa'),";

				$tablaDebug  .='<div style="float:left; width:80px;">'.$totalDebito.'</div><div style="float:left; width:80px;">'.$totalCredito.'</div><div style="float:left; width:80px;">'.$cuentaPuc.'</div><br>';
			}

			foreach ($arrayRetenciones AS $cuentaPuc => $valArrayRetencion){

				$totalDebito  = 0;
				$totalCredito = $valArrayRetencion;

				for ($i=1; $i <= $contAutoRetencion; $i++) { if($arrayAutoRetencion[$i] == $cuentaPuc){ $totalDebito = $valArrayRetencion; $totalCredito = 0; break; } }

				$globalDebito  += $totalDebito;
				$globalCredito += $totalCredito;

				$valueInsertAsientos .= "('$idFactura',
											'$consecutivoFactura',
											'FC',
											'$idFactura',
											'$consecutivoFactura',
											'FC',
											'Factura de Compra',
											'$fechaInicioFactura',
											'$totalDebito',
											'$totalCredito',
											'$cuentaPuc',
											'$idProveedor',
											'$idSucursal',
											'$idEmpresa'),";
				$tablaDebug  .='<div style="float:left; width:80px;">'.$totalDebito.'</div><div style="float:left; width:80px;">'.$totalCredito.'</div><div style="float:left; width:80px;">'.$cuentaPuc.'</div><br>';
			}

			$globalDebito  = ROUND($globalDebito, $decimalesMoneda);
			$globalCredito = ROUND($globalCredito, $decimalesMoneda);

			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$globalDebito.'-</div><div style="float:left; width:80px; border-top:1px solid">-'.$globalCredito.'-</div><br>';
			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.gettype($globalDebito).'-</div><div style="float:left; width:80px; border-top:1px solid">-'.gettype($globalCredito).'-</div><br>';
			//echo $tablaDebug; exit;
			if($globalDebito != $globalCredito){echo'<script>alert("Aviso.\nLa plantilla ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }
			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sqlContabilizar     = "INSERT INTO asientos_niif (
										id_documento,
										consecutivo_documento,
										tipo_documento,
										id_documento_cruce,
										numero_documento_cruce,
										tipo_documento_cruce,
										tipo_documento_extendido,
										fecha,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
			$queryContabilizar   = mysql_query($sqlContabilizar,$link);

			if(!$queryContabilizar){ echo'<script>alert("Error.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema")</script>'; exit; }

			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
			$sqlContabilizar     = "INSERT INTO contabilizacion_compra_venta_niif (codigo_puc,caracter,descripcion,porcentaje,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega) VALUES $valueInsertContabilizacion";
			$queryContabilizar   = mysql_query($sqlContabilizar,$link);

			if($contActivosFijos > 0){
				$valueInsertActivosFijos = substr($valueInsertActivosFijos, 0, -1);
				$sqlInsertActivoFijo = "INSERT INTO activos_fijos (
											id_empresa,
											id_sucursal,
											id_bodega,
											id_item,
											nombre_equipo,
											id_documento_referencia,
											id_documento_referencia_inventario,
											documento_referencia,
											documento_referencia_consecutivo,
											costo,
											fecha_compra,
											fecha_inicio_depreciacion,
											id_proveedor)
										VALUES $valueInsertActivosFijos";
				$queryInsertActivoFijo = mysql_query($sqlInsertActivoFijo,$link);
			}
		}
		else{ echo'<script>alert("Aviso.\nLa plantilla ingresada no tiene configuracion contable.")</script>'; exit; }
  }

  function contabilizarSinPlantillaNiif($arrayAnticipo,$arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$consecutivoDocReferencia,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idProveedor,$link){

		$decimalesMoneda  = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA'] : 0;
		$cuentaPago       = $arrayCuentaPago['cuentaNiif'];
		$estadoCuentaPago = $arrayCuentaPago['estado'];

		//============================= QUERY CUENTAS ============================//
		$ivaAcumulado      = 0;
		$precioAcumulado   = 0;

		$whereIdItemsCuentas = '';

		$sqlDoc = "SELECT
								CFI.id,
								CFI.id_inventario AS id_item,
								CFI.codigo,
								CFI.nombre AS nombre_equipo,
								CFI.cantidad,
								CFI.costo_unitario AS precio,
								CFI.descuento,
								CFI.tipo_descuento,
								CFI.id_impuesto,
								CFI.valor_impuesto,
								CFI.cuenta_impuesto_niif AS cuenta_impuesto,
								I.cruzar_costo_activo_fijo,
								I.cuenta_compra_niif AS cuenta_compra,
								CFI.inventariable,
								CFI.check_opcion_contable,
								CFI.opcion_gasto,
								CFI.opcion_costo,
								CFI.opcion_activo_fijo,
								CFI.id_centro_costos,
								CFI.id_consecutivo_referencia AS id_referencia,
								CFI.nombre_consecutivo_referencia AS nombre_referencia
							FROM
								compras_facturas_inventario AS CFI
							LEFT JOIN
								impuestos AS I
							ON
								CFI.id_impuesto = I.id
							WHERE
								CFI.id_factura_compra = '$idFactura'
							AND
								CFI.activo = 1";
		$queryDoc = mysql_query($sqlDoc,$link);
		while($rowDoc = mysql_fetch_array($queryDoc)){

			//CALCULO DEL PRECIO
			$impuesto = 0;
			$precio   = $rowDoc['precio'] * $rowDoc['cantidad'];
			$costo    = $rowDoc['costo'] * $rowDoc['cantidad'];

			if($rowDoc['descuento'] > 0){ $precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio - ROUND(($rowDoc['descuento'] * $precio) / 100, $decimalesMoneda) : $precio - $rowDoc['descuento']; }
			if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){ $impuesto = $precio * $rowDoc['valor_impuesto'] / 100; }

			$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR id_items = '.$rowDoc['id_item']: 'id_items = '.$rowDoc['id_item'];

			$arrayInfoItems[$rowDoc['id_item']] = array(
														'codigo'                => $rowDoc['codigo'],
														'nombre_item'           => $rowDoc['nombre'],
														'check_opcion_contable' => $rowDoc['check_opcion_contable'],
														'opcion_gasto'          => $rowDoc['opcion_gasto'],
														'opcion_costo'          => $rowDoc['opcion_costo'],
														'opcion_activo_fijo'    => $rowDoc['opcion_activo_fijo'],
													);

			$arrayInventarioFactura[$rowDoc['id']] = array(
																											'id_factura_inventario' 		=> $rowDoc['id'],
																											'codigo'                		=> $rowDoc['codigo'],
																											'nombre_equipo'         		=> $rowDoc['nombre_equipo'],
																											'impuesto'              		=> $impuesto,
																											'cuenta_impuesto'       		=> $rowDoc['cuenta_impuesto'],
																											'cruzar_costo_activo_fijo'	=> $rowDoc['cruzar_costo_activo_fijo'],
																											'cuenta_compra'							=> $rowDoc['cuenta_compra'],
																											'precio'                    => $precio,
																											'id_items'                  => $rowDoc['id_item'],
																											'inventariable'             => $rowDoc['inventariable'],
																											'cantidad'                  => $rowDoc['cantidad'],
																											'id_centro_costos'          => $rowDoc['id_centro_costos'],
																											'check_opcion_contable'     => $rowDoc['check_opcion_contable'],
																											'id_referencia'             => $rowDoc['id_referencia'],
																											'nombre_referencia'         => $rowDoc['nombre_referencia']
																										);
		}

		$sqlItemsCuentas = "SELECT id,id_items,descripcion,id_puc,puc,tipo,estado
												FROM items_cuentas_niif
												WHERE activo = 1
												AND id_empresa = '$idEmpresa'
												AND estado = 'compra'
												AND ($whereIdItemsCuentas)
												GROUP BY id_items,descripcion
												ORDER BY id_items ASC";
		$queryItemsCuentas = mysql_query($sqlItemsCuentas,$link);

		$whereCuentaCcos = "";
		while($rowCuentasItems = mysql_fetch_array($queryItemsCuentas)){
			if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; $rowCuentasItems['tipo'] = 'credito'; }
			if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['tipo'] = 'debito'; }

			// VALIDAR QUE EL ITEM TENGA LAS CUENTAS CONFIGURADAS
			switch ($rowCuentasItems['descripcion']){
				case 'costo':
						if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_costo']=='true'){
							echo'<script>
										alert("Aviso.\nEl item Codigo '.$arrayInfoItems[$rowCuentasItems['id_items']]['codigo'].' No se ha configurado en la cuenta de '.$rowCuentasItems['descripcion'].' en '.$rowCuentasItems['estado'].'");
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							exit;
						}
					break;
				case 'gasto':
						if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_gasto']=='true'){
							echo'<script>
										alert("Aviso.\nEl item Codigo '.$arrayInfoItems[$rowCuentasItems['id_items']]['codigo'].' No se ha configurado en la cuenta de '.$rowCuentasItems['descripcion'].' en '.$rowCuentasItems['estado'].'");
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							exit;
						}
					break;
				case 'precio':
						if ($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0){
							echo'<script>
										alert("Aviso.\nEl item Codigo '.$arrayInfoItems[$rowCuentasItems['id_items']]['codigo'].' No se ha configurado en la cuenta de '.$rowCuentasItems['descripcion'].' en '.$rowCuentasItems['estado'].'");
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							exit;
						}
					break;
				case 'activo_fijo':
						if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_activo_fijo']=='true'){
							echo'<script>
										alert("Aviso.\nEl item Codigo '.$arrayInfoItems[$rowCuentasItems['id_items']]['codigo'].' No se ha configurado en la cuenta de '.$rowCuentasItems['descripcion'].' en '.$rowCuentasItems['estado'].'");
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							exit;
						}
					break;
			}

			$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']] = array('estado' => $rowCuentasItems['tipo'], 'cuenta' => $rowCuentasItems['puc']);

			$valueInsertContabilizacion .=  "('".$rowCuentasItems['id_items']."',
																				'".$rowCuentasItems['puc']."',
																				'".$rowCuentasItems['tipo']."',
																				'".$rowCuentasItems['descripcion']."',
																				'$idFactura',
																				'FC',
																				'$idEmpresa',
																				'$idSucursal',
																				'$idBodega'),";

			$cuenta      = $rowCuentasItems['puc'];
			$descripcion = $rowCuentasItems['descripcion'];

			if($descripcion == 'precio' || $descripcion == 'gasto' || $descripcion == 'costo' || $descripcion == 'activo_fijo'){ $whereCuentaCcos .= "OR cuenta='$cuenta' "; }
		}

		// VALIDAR LA CUENTA DE PAGO
		foreach($arrayCuentasItems as $id_item => $arrayResult){
			if(!array_key_exists('contraPartida_precio', $arrayResult)){
				$arrayCuentasItems[$id_item]['contraPartida_precio'] = array(
																																			'estado' => 'credito',
																																			'cuenta' => $cuentaPago
																																		);
				$valueInsertContabilizacion .= "('".$arrayResult['id_items']."',
																				 '".$cuentaPago."',
																				 'credito',
																				 'contraPartida_precio',
																				 '$idFactura',
																				 'FC',
																				 '$idEmpresa',
																				 '$idSucursal',
																				 '$idBodega'),";
			}
		}

		$whereCuentaCcos = substr($whereCuentaCcos, 3, -1);
		$sqlCcos   = "SELECT cuenta,centro_costo FROM puc_niif WHERE id_empresa = '$idEmpresa' AND activo = 1 AND ($whereCuentaCcos)";
		$queryCcos = mysql_query($sqlCcos, $link);

		while($row = mysql_fetch_assoc($queryCcos)){
			$cuenta = $row['cuenta'];
			$cCos   = $row['centro_costo'];

			$arrayCuentaCcos[$cuenta] = $cCos;
		}

		// CONSULTAR LAS CUENTAS DE TRANSITO DE LAS ENTRADAS DE ALMACEN
		$sql = "SELECT
							id_cuenta_niif_debito,
							cuenta_niif_debito,
							id_cuenta_niif_credito,
							cuenta_niif_credito
						FROM
							costo_cuentas_transito
						WHERE
						  activo = 1
						AND
							id_empresa = $idEmpresa";
		$query = mysql_query($sql,$link);

		$arrayCuentasTransito['id_cuenta_niif_debito']  = mysql_result($query,0,'id_cuenta_niif_debito');
		$arrayCuentasTransito['cuenta_niif_debito']     = mysql_result($query,0,'cuenta_niif_debito');
		$arrayCuentasTransito['id_cuenta_niif_credito'] = mysql_result($query,0,'id_cuenta_niif_credito');
		$arrayCuentasTransito['cuenta_niif_credito']    = mysql_result($query,0,'cuenta_niif_credito');

		$arrayGlobalEstado['debito']  = 0;
		$arrayGlobalEstado['credito'] = 0;

		$arrayItemEstado['debito']  = 0;
		$arrayItemEstado['credito'] = 0;

		$acumSubtotal = 0;
		$acumImpuesto = 0;

		$msjErrorCcosto = '';

		foreach($arrayInventarioFactura AS $valArrayInventario){

			$totalContabilizacionItem   = 0;
			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$idItemArray       = $valArrayInventario['id_items'];										//ID ITEM
			$descripcionCuenta = $valArrayInventario['check_opcion_contable'];							//GASTO, COSTO, ACTIVO FIJO
			$descripcionCuenta = (strlen($descripcionCuenta) > 4)? $descripcionCuenta : 'precio';

			$cuentaOpcional    = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['cuenta'];		//CUENTA OPCION CONTABILIZACION

			$cuentaPrecio   = ($descripcionCuenta != 'precio')? $cuentaOpcional: $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
			$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
			$cuentaImpuesto = ($valArrayInventario['cuenta_impuesto'] > 0)? $valArrayInventario['cuenta_impuesto']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

			//========================= CONTABILIZACION MERCANCIA PARA LA VENTA, COSTO, GASTO =========================//
			//*********************************************************************************************************//
			//CONDICIONAL SI TIENE CCOS
			$cCosPrecio = 0;
			if($arrayCuentaCcos[$cuentaPrecio] == 'Si'){
				if($valArrayInventario['id_centro_costos'] > 0){ $cCosPrecio = $valArrayInventario['id_centro_costos']; }
				else{ $msjErrorCcosto = '\n'.$valArrayInventario['codigo'].' '.$valArrayInventario['nombre_item']; }
			}

			if($descripcionCuenta != 'activo_fijo'){

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] += $valArrayInventario['precio']; }
					else{ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] = $valArrayInventario['precio']; }

					//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
					if($valArrayInventario === end($arrayInventarioFactura)){
						if($arrayAnticipo['total'] > 0){
							$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] = ROUND($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado],$_SESSION['DECIMALESMONEDA']);
						}
						else{
							$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] = $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado];
						}
		    	}

					$arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
					$arrayItemEstado[$estado]   += $valArrayInventario['precio'];
					$acumSubtotal               += $valArrayInventario['precio'];

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){

						$estado = 'debito';

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[0][$cuentaImpuesto][$estado] > 0){ $arrayAsiento[0][$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
						else{ $arrayAsiento[0][$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }

						$array_cuentas_impuestos[] = $cuentaImpuesto;

						$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
						$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
						$acumImpuesto               += $valArrayInventario['impuesto'];
					}

					//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
					if($valArrayInventario === end($arrayInventarioFactura)){
						$array_cuentas_impuestos = array_unique($array_cuentas_impuestos);
						
						if($arrayAnticipo['total'] > 0){
							foreach($array_cuentas_impuestos as $key => $value){
								$arrayAsiento[0][$value][$estado] = ROUND($arrayAsiento[0][$value][$estado],$_SESSION['DECIMALESMONEDA']);
							}
						}
						else{
							foreach($array_cuentas_impuestos as $key => $value){
								$arrayAsiento[0][$value][$estado] = $arrayAsiento[0][$value][$estado];
							}
						}
		    	}

					//============================== CALC CONTRA PARTIDA PRECIO =================================//
					if($contraPrecio > 0){
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
										: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $contraSaldo; }
						else{ $arrayAsiento[0][$contraPrecio][$estado] = $contraSaldo; }

						//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
						if($valArrayInventario === end($arrayInventarioFactura)){
							if($arrayAnticipo['total'] > 0){
								$arrayAsiento[0][$contraPrecio][$estado] = ROUND($arrayAsiento[0][$contraPrecio][$estado],$_SESSION['DECIMALESMONEDA']);
							}
							else{
								$arrayAsiento[0][$contraPrecio][$estado] = $arrayAsiento[0][$contraPrecio][$estado];
							}
			    	}

						$arrayGlobalEstado[$estado] += $contraSaldo;
						$arrayItemEstado[$estado]   += $contraSaldo;

						$acumCuentaClientes   = $contraPrecio;
						$estadoCuentaClientes = $estado;
					}

					//============================== SI PERTENECE A UNA ENTRADA DE ALMACEN CERRAR CUENTAS TRANSITO =================================//
					if ($valArrayInventario['nombre_referencia']=='Entrada de Almacen') {
						if($arrayCuentasTransito['id_cuenta_niif_debito'] =='' || $arrayCuentasTransito['id_cuenta_niif_credito'] ==''){

							echo'<script>
									alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' pertenece a una entrada de Almacen pero no hay cuentas de transito niif configiradas en el panel de control");
									document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							exit;
						}

						$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_debito']]['credito'] += $valArrayInventario['precio'];
						$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_credito']]['debito'] += $valArrayInventario['precio'];
					}

				}
				else if($valArrayInventario['inventariable'] == 'false'){
					echo'<script>
							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
					echo'<script>
							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion");
							console.log("'.$arrayItemEstado['debito'].' - '.$arrayItemEstado['credito'].'");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
				else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
					echo'<script>
							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}
			//======================================= CONTABILIZACION ACTIVO FIJO ======================================//
			//**********************************************************************************************************//
			else{
				if($cuentaPrecio == ''){
					echo'<script>
							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' no tiene asignado la cuenta de Activo Fijo Niif en configuracion items");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$totalContabilizacionItem += $valArrayInventario['precio'];

					//======================================= CALC IMPUESTO =======================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado                   = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
						$acumImpuesto             += $valArrayInventario['impuesto'];
						$totalContabilizacionItem += $valArrayInventario['impuesto'];
					}

					//============================= CALC CONTRA PRECIO ACTIVO FIJO ================================//
					if($contraPrecio > 0){
						$arrayAsiento[0][$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$arrayGlobalEstado[$estado] += $totalContabilizacionItem;
						$arrayItemEstado[$estado]   += $totalContabilizacionItem;

						if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $totalContabilizacionItem; }
						else{ $arrayAsiento[0][$contraPrecio][$estado] = $totalContabilizacionItem; }

						//ARRAY ASIENTO CONTABLE PRECIO
						$estadoPrecio                      = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
						$arrayGlobalEstado[$estadoPrecio] += $totalContabilizacionItem;
						$arrayItemEstado[$estadoPrecio]   += $totalContabilizacionItem;
						$acumSubtotal                     += $valArrayInventario['precio'];

						//ARRAY ASIENTO CONTABLE ACTIVO FIJO O GASTO
						if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] > 0){
							$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] += $totalContabilizacionItem;
						}
						else{
							$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] = $totalContabilizacionItem;
						}
						
						//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
						if($valArrayInventario === end($arrayInventarioFactura)){
							if($arrayAnticipo['total'] > 0){
								$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] = ROUND($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio],$_SESSION['DECIMALESMONEDA']);
							}
							else{
								$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] = $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio];
							}
						}

						//ARRAY ASIENTO CONTABLE IMPUESTO
						if($valArrayInventario['cuenta_compra'] != "" && $valArrayInventario['cruzar_costo_activo_fijo'] == "false" && $valArrayInventario['check_opcion_contable'] == "activo_fijo"){
							$estadoImpuesto = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
							$cuentaImpuesto = $valArrayInventario['cuenta_compra'];

							//VERIFICAMOS SI EL COSTO DLE ACTIVO FIJO SE CRUZA CON EL COSTO DEL IMPUESTO
							$arrayAsiento[0][$cuentaPrecio][$estadoPrecio] = $arrayAsiento[0][$cuentaPrecio][$estadoPrecio] - $valArrayInventario['impuesto'];
							$arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto] += $valArrayInventario['impuesto'];

							//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
							if($valArrayInventario === end($arrayInventarioFactura)) {
				        $arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto] = ROUND($arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto],$decimalesMoneda);
				    	}
						}

						$acumCuentaClientes   = $contraPrecio;
						$estadoCuentaClientes = $estado;
					}

					//============================== SI PERTENECE A UNA ENTRADA DE ALMACEN CERRAR CUENTAS TRANSITO =================================//
					if ($valArrayInventario['nombre_referencia']=='Entrada de Almacen') {
						if($arrayCuentasTransito['id_cuenta_niif_debito'] =='' || $arrayCuentasTransito['id_cuenta_niif_credito'] ==''){

							echo'<script>
									alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' pertenece a una entrada de Almacen pero no hay cuentas de transito niif configiradas en el panel de control");
									document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							exit;
						}

						$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_debito']]['credito'] += $valArrayInventario['precio'];
						$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_credito']]['debito'] += $valArrayInventario['precio'];
					}

				}
				else if($valArrayInventario['inventariable'] == 'false'){
					echo'<script>
							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
					echo'<script>
							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
				else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
					echo'<script>
							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}
			if($msjErrorCcosto != ''){
				echo'<script>
						alert("Aviso.\nLos siguientes items no tienen centro de costo \n'.$msjErrorCcosto.'");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					$sql   = "UPDATE compras_facturas SET estado=0 WHERE activo=1 AND id=$idFactura";
					$query = mysql_query($sql,$link);
				exit;
			}
		}

		$arrayGlobalEstado['debito']  = round($arrayGlobalEstado['debito'],$_SESSION['DECIMALESMONEDA']);
		$arrayGlobalEstado['credito'] = round($arrayGlobalEstado['credito'],$_SESSION['DECIMALESMONEDA']);

		if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
			echo'<script>
						alert("Aviso.\nHa ocurrido un problema de contabilizacion, favor revise la configuracion de contabilizacion.\nSi el problema persiste consulte al administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0 || $arrayGlobalEstado['debito'] =='' || $arrayGlobalEstado['credito'] ==''){
			echo'<script>
						alert("Aviso.\nContabilizacion en saldo 0.\nSi el problema persiste consulte al administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$acumImpuesto = ROUND($acumImpuesto,$decimalesMoneda);

		//=========================== QUERY RETENCIONES ==========================//
		$acumRetenciones  = 0;
		$contRetencion    = 0;
		$estadoRetencion  = $estadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
		$sqlRetenciones   = "SELECT valor,codigo_cuenta_niif,tipo_retencion,cuenta_autoretencion_niif,base,base_modificada FROM compras_facturas_retenciones WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			$valorBase           = $rowRetenciones['base'];
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta_niif'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion_niif'];
			$baseModificada      = $rowRetenciones['base_modificada'];
			$acumSubtotal = ($baseModificada > 0)? $baseModificada : $acumSubtotal;
			$acumImpuesto = ($baseModificada > 0)? $baseModificada : $acumImpuesto;

			if(is_nan($arrayAsiento[0][$codigoRetencion][$estadoRetencion])){ $arrayAsiento[0][$codigoRetencion][$estadoRetencion] = 0; }

			//CALCULO RETEIVA
			if($tipoRetencion == "ReteIva"){ 																		      //CALCULO RETEIVA
				if($acumImpuesto < $valorBase){ continue; }															//BASE RETENCION

				$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE Y RETEICA
				if($acumSubtotal<$valorBase) { continue; }															//BASE RETENCION

				$acumRetenciones += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
					echo '<script>
									alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.");
									document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
						exit;
				}

				if(is_nan($arrayAsiento[0][$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[0][$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
				$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
				$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[0][$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[0][$codigoRetencion][$estadoRetencion];
			}
		}

		//TOTAL CUENTA PAGO (CLIENTES CAJA Y CUENTA ANTICIPO)
		$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] -= $acumRetenciones;

		//============================ SALDO ANTICIPO ============================//
		$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] = $arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes];
		$saldoClientes = $arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes];
		$saldoAnticipo = $arrayAnticipo['total'];

		if(round($saldoAnticipo,$_SESSION['DECIMALESMONEDA']) > 0 && round($saldoAnticipo,$_SESSION['DECIMALESMONEDA']) > round($saldoClientes,$_SESSION['DECIMALESMONEDA'])){
			echo'<script>
					alert("Aviso.\nLos anticipos no puedeeeen ser mayores a la factura de compra!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else{
			$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] -= $saldoAnticipo;
		}

		foreach($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo){
			$arrayCampo['debito']  = 0;
			$arrayCampo['credito'] = 0;

			$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

			$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
			$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
		}

		$totalDebito  = 0;
		$totalCredito = 0;
		$tablaDebug   =  '<div style="float:left; width:80px;">Id</div>
											<div style="float:left; width:80px;">Documento</div>
											<div style="float:left; width:80px;">Debito</div>
											<div style="float:left; width:80px;">Credito</div>
											<div style="float:left; width:80px;">PUC</div>
											<div style="float:left; width:150px;">Id Centro Costos</div><br>';

		//============================ CONTABILIZACION ===========================//
		foreach($arrayAsiento AS $idCcos => $arrayCuenta){
			foreach($arrayCuenta AS $cuenta => $arrayCampo){
				if(is_nan($cuenta) || $cuenta == 0){ continue; }
				$cuenta = $cuenta * 1;

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

				if($arrayCampo['debito'] > 0 || $arrayCampo['credito'] > 0){

					// BODY INSERT
					$valueInsertAsientos .= "('$idFactura',
																		'$consecutivoFactura',
																		'FC',
																		'Factura de Compra',
																		'$idFactura',
																		'FC',
																		'$consecutivoDocReferencia',
																		'$fechaInicioFactura',
																		'".$arrayCampo['debito']."',
																		'".$arrayCampo['credito']."',
																		'$cuenta',
																		'$idProveedor',
																		'$idSucursal',
																		'$idEmpresa',
																		'$idCcos'),";

					$tablaDebug .= '<div style="overflow:hidden;">
														<div style="float:left; width:80px;">-'.$idFactura.'</div>
														<div style="float:left; width:80px;">FC</div>
														<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
														<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
														<div style="float:left; width:80px;">-'.$cuenta.'</div>
														<div style="float:left; width:150px;">'.$idCcos.'</div>
													</div><br>';
				}

				//============================= ANTICIPOS ============================//
				if($acumCuentaClientes == $cuenta){
					foreach($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo){

						$arrayCampo['debito']  = 0;
						$arrayCampo['credito'] = 0;

						$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

						$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
						$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

						if(is_nan($datosAnticipo['cuenta_niif']) || $datosAnticipo['cuenta_niif']==0){ continue; }
						$datosAnticipo['cuenta_niif'] = $datosAnticipo['cuenta_niif'] * 1;

						$valueInsertAsientos .= "('$idFactura',
																			'$consecutivoFactura',
																			'FC',
																			'Factura de Compra',
																			'$datosAnticipo[id_documento]',
																			'$datosAnticipo[tipo_documento]',
																			'',
																			'$fechaInicioFactura',
																			'$arrayCampo[debito]',
																			'$arrayCampo[credito]',
																			'$datosAnticipo[cuenta_niif]',
																			'$datosAnticipo[id_tercero]',
																			'$idSucursal',
																			'$idEmpresa',
																			'$idCcos'),";

						$tablaDebug .= '<div style="overflow:hidden;">
															<div style="float:left; width:80px;">-'.$idAnticipo.'</div>
															<div style="float:left; width:80px;">CE</div>
															<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
															<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
															<div style="float:left; width:80px;">-'.$datosAnticipo['cuenta_niif'].'</div>
															<div style="float:left; width:150px;">'.$idCcos.'</div>
														</div><br>';
					}
				}
			}
		}

		$tablaDebug .= '<div style="overflow:hidden;">
											<div style="float:left; width:80px; border-top:1px solid">-</div>
											<div style="float:left; width:80px; border-top:1px solid">-</div>
											<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div>
											<div style="float:left; width:80px; border-top:1px solid">'.$totalCredito.'</div>
										</div><br>
										<script>
											document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
										</script>';
		$totalDebito  = round($totalDebito,$_SESSION['DECIMALESMONEDA']);
		$totalCredito = round($totalCredito,$_SESSION['DECIMALESMONEDA']);

		if($totalDebito != $totalCredito){
			echo '<script>
							alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			exit;
		}

		$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
		$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

		$sqlContabilizar = "INSERT INTO
													contabilizacion_compra_venta_niif(
														id_item,
														codigo_puc,
														caracter,
														descripcion,
														id_documento,
														tipo_documento,
														id_empresa,
														id_sucursal,
														id_bodega
													)
												VALUES
													$valueInsertContabilizacion";
		$queryContabilizar = mysql_query($sqlContabilizar,$link);

		if(!$queryContabilizar){
			echo '<script>
							alert("Aviso.\nSin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			exit;
		}

		//CUENTAS NIIF
		$sqlInsertCuentasNiif =  "INSERT INTO
																asientos_niif(
																	id_documento,
																	consecutivo_documento,
																	tipo_documento,
																	tipo_documento_extendido,
																	id_documento_cruce,
																	tipo_documento_cruce,
																	numero_documento_cruce,
																	fecha,
																	debe,
																	haber,
																	codigo_cuenta,
																	id_tercero,
																	id_sucursal,
																	id_empresa,
																	id_centro_costos
																)
															VALUES
																$valueInsertAsientos";
		$queryInsertCuentasNiif = mysql_query($sqlInsertCuentasNiif,$link);
  }

  function contabilizarSinPlantillaManualNiif($arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$consecutivoDocReferencia,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idProveedor,$link){

		$decimalesMoneda  = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;
		$cuentaPago       = $arrayCuentaPago['cuentaNiif'];
		$estadoCuentaPago = $arrayCuentaPago['estado'];

		//===================================// GUARDA CONTABILIDAD AUTO PARA DEVOLUCIONES //===================================//
		//**********************************************************************************************************************//
		$whereIdItemsCuentas = '';
		$sqlDoc = "SELECT id,
						id_inventario AS id_item,
						codigo,
						cantidad,
						costo_unitario AS precio,
						descuento,
						tipo_descuento,
						id_impuesto,
						valor_impuesto,
						inventariable,
						check_opcion_contable,
						id_centro_costos
					FROM compras_facturas_inventario
					WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryDoc = mysql_query($sqlDoc,$link);
		while($rowDoc = mysql_fetch_array($queryDoc)){ $whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR id_items = '.$rowDoc['id_item']: 'id_items = '.$rowDoc['id_item']; }

		$sqlItemsCuentas = "SELECT id, id_items,descripcion, puc, tipo
							FROM items_cuentas
							WHERE activo=1
								AND id_empresa='$idEmpresa'
								AND estado='compra' AND ($whereIdItemsCuentas)
							GROUP BY id_items,descripcion
							ORDER BY id_items ASC";
		$queryItemsCuentas = mysql_query($sqlItemsCuentas,$link);

		while ($rowCuentasItems = mysql_fetch_array($queryItemsCuentas)) {
			if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; }

			$valueInsertContabilizacion .= "('".$rowCuentasItems['id_items']."',
											'".$rowCuentasItems['puc']."',
											'".$rowCuentasItems['tipo']."',
											'".$rowCuentasItems['descripcion']."',
											'$idFactura',
											'FC',
											'$idEmpresa',
											'$idSucursal',
											'$idBodega'),";
		}

		//===========================================// GUARDA CONTABILIDAD MANUAL //===========================================//
		//**********************************************************************************************************************//

	   	$sql="SELECT subtotal_manual,
    				iva_manual,
    				total_manual,
    				id_centro_costos,
    				codigo_centro_costos,
    				nombre_centro_costos,
					cuenta_niif_subtotal,
					cuenta_niif_iva,
					cuenta_niif_total
    			FROM compras_facturas_contabilidad_manual
    			WHERE id_factura_compra=$idFactura AND activo=1 AND id_empresa=$idEmpresa";
    	$query=mysql_query($sql,$link);

		$subtotal_manual  = mysql_result($query,0,'subtotal_manual');
		$iva_manual       = mysql_result($query,0,'iva_manual');
		$total_manual     = mysql_result($query,0,'total_manual');
		$id_centro_costos = mysql_result($query,0,'id_centro_costos');
		$cuenta_subtotal  = mysql_result($query,0,'cuenta_niif_subtotal');
		$cuenta_iva       = mysql_result($query,0,'cuenta_niif_iva');
		$cuenta_total     = mysql_result($query,0,'cuenta_niif_total');

		$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;


		//==================== QUERY RETENCIONES =================//
    	/**********************************************************/
		$acumRetenciones  = 0;
		$contRetencion    = 0;
		$estadoRetencion  = 'credito'; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
		$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion_niif,base FROM compras_facturas_retenciones WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			$valorBase           = $rowRetenciones['base'];
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion_niif'];

			if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

			if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
				if($iva_manual<$valorBase) { continue; }															//BASE RETENCION

				$acumRetenciones += ROUND($iva_manual * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion]['typeCuenta']      = 'Retencion';
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($iva_manual * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE Y RETEICA
				if($subtotal_manual<$valorBase) { continue; }															//BASE RETENCION

				$acumRetenciones += ROUND($subtotal_manual * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion]['typeCuenta']      = 'Retencion';
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($subtotal_manual * $valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
					echo'<script>
							alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
						exit;
				}

				if(is_nan($arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
				$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
				$acumRetenciones -= ROUND($subtotal_manual*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
			}
		}
		$total_calculado=$subtotal_manual+$iva_manual-$acumRetenciones;
		if ($total_calculado!=$total_manual) {
			echo'<script>
					alert("Error!\nlos saldos manuales ingresados no cumplen doble partida");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$arrayAsiento[$cuentaPago]['credito']     = $total_manual;
		$arrayAsiento[$cuenta_iva]['debito']      = $iva_manual;
		$arrayAsiento[$cuenta_subtotal]['debito'] = $subtotal_manual;
		$arrayAsiento[$cuenta_subtotal]['idCcos'] = $id_centro_costos;

		//ASIENTO CONTABLE
		foreach ($arrayAsiento AS $cuenta => $arrayCampo) {
			$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
			$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

			$cuenta     = $cuenta * 1;
			$subString2 = substr($cuenta, 0, 2);
			$subString4 = substr($cuenta, 0, 4);

			//ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO
			$valueInsertAsientos .= "('$idFactura',
									'$consecutivoFactura',
									'FC',
									'Factura de Compra',
									'$idFactura',
									'FC',
									'$consecutivoDocReferencia',
									'$fechaInicioFactura',
									'".$arrayCampo['debito']."',
									'".$arrayCampo['credito']."',
									'$cuenta',
									'$idProveedor',
									'$idSucursal',
									'$idEmpresa',
									'".$arrayCampo['idCcos']."'
									),";

			$tablaDebug .= '<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
							<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
							<div style="float:left; width:80px;">-'.$cuenta.'</div>
							<div style="float:left; width:150px;">-</div><br>';
		}


		$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$totalCredito.'</div><br>';
		// echo $tablaDebug; exit;
		$totalDebito  = round($totalDebito,$_SESSION['DECIMALESMONEDA']);
		$totalCredito = round($totalCredito,$_SESSION['DECIMALESMONEDA']);
		if($totalDebito != $totalCredito){
			echo'<script>
					alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
		$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

		$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta_niif (
								id_item,
								codigo_puc,
								caracter,
								descripcion,
								id_documento,
								tipo_documento,
								id_empresa,
								id_sucursal,
								id_bodega)
							VALUES $valueInsertContabilizacion";
		$queryContabilizar = mysql_query($sqlContabilizar,$link);
		if(!$queryContabilizar){
			echo'<script>
						alert("Aviso.\nSin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}

		$sqlInsertCuentasColgaap = "INSERT INTO asientos_niif (
										id_documento,
										consecutivo_documento,
										tipo_documento,
										tipo_documento_extendido,
										id_documento_cruce,
										tipo_documento_cruce,
										numero_documento_cruce,
										fecha,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_sucursal,
										id_empresa,
										id_centro_costos)
									VALUES $valueInsertAsientos";
		$queryInsertCuentasColgaap = mysql_query($sqlInsertCuentasColgaap,$link);
  }
?>
