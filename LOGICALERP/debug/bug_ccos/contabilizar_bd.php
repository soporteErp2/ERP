<?php

    function contabilizarSinPlantilla($arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idProveedor,$link){

    	global $saldoGlobalFactura, $saldoGlobalFacturaSinAbono;
		$decimalesMoneda  = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;
		$cuentaPago       = $arrayCuentaPago['cuentaColgaap'];
		$estadoCuentaPago = $arrayCuentaPago['estado'];

		//===================== QUERY CUENTAS ====================//
    	/**********************************************************/
		$ivaAcumulado      = 0;
		$costoAcumulado    = 0;
		$precioAcumulado   = 0;
		$impuestoAcumulado = 0;

		$arrayRemisiones   = '';
		$contRemisiones    = 0;
		$acumIdRemisiones  = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

		$whereIdItemsCuentas = '';

		$sqlDoc = "SELECT id,
						id_inventario AS id_item,
						codigo,
						nombre,
						cantidad,
						costo_unitario AS precio,
						descuento,
						tipo_descuento,
						id_impuesto,
						valor_impuesto,
						cuenta_impuesto,
						inventariable,
						check_opcion_contable,
						id_centro_costos
					FROM compras_facturas_inventario
					WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryDoc = mysql_query($sqlDoc,$link);
		while($rowDoc = mysql_fetch_array($queryDoc)){

			//CALCULO DEL PRECIO
			$impuesto = 0;
			$precio   = $rowDoc['precio'] * $rowDoc['cantidad'];
			$costo    = $rowDoc['costo'] * $rowDoc['cantidad'];

			if($rowDoc['descuento'] > 0){ $precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio-ROUND($rowDoc['descuento']*100/$precio, $decimalesMoneda) : $precio-$rowDoc['descuento']; }
			if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){  $impuesto = ROUND($precio*$rowDoc['valor_impuesto']/100, $decimalesMoneda); }

			$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR id_items = '.$rowDoc['id_item']: 'id_items = '.$rowDoc['id_item'];

			$arrayInventarioFactura[$rowDoc['id']]  = array('id_factura_inventario' =>$rowDoc['id'],
															'codigo'                =>$rowDoc['codigo'],
															'nombre_item'           =>$rowDoc['nombre'],
															'impuesto'              =>$impuesto,
															'cuenta_impuesto'       =>$rowDoc['cuenta_impuesto'],
															'precio'                =>$precio,
															'id_items'              =>$rowDoc['id_item'],
															'inventariable'         =>$rowDoc['inventariable'],
															'cantidad'              =>$rowDoc['cantidad'],
															'id_centro_costos'      =>$rowDoc['id_centro_costos'],
															'check_opcion_contable' =>$rowDoc['check_opcion_contable']);
		}

		$sqlItemsCuentas = "SELECT id, id_items,descripcion, puc, tipo
							FROM items_cuentas
							WHERE activo=1
								AND id_empresa='$idEmpresa'
								AND estado='compra' AND ($whereIdItemsCuentas)
							GROUP BY id_items,descripcion
							ORDER BY id_items ASC";
		$queryItemsCuentas = mysql_query($sqlItemsCuentas,$link);

		$whereCuentaCcos = "";
		while ($rowCuentasItems = mysql_fetch_array($queryItemsCuentas)) {
			if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; }
			/* $arrayCuentasItems['id_item']['descripcion' => 'precio', 'contraprecio', 'impuesto']	=> ['cuenta'=>xxxx]
																									=> ['estado'=>xxxx] */
			$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']] = array('estado' => $rowCuentasItems['tipo'], 'cuenta' => $rowCuentasItems['puc']);

			$cuenta      = $rowCuentasItems['puc'];
			$descripcion = $rowCuentasItems['descripcion'];

			if($descripcion == 'precio' || $descripcion == 'gasto' || $descripcion == 'costo'){ $whereCuentaCcos .= "OR cuenta='$cuenta' "; }
		}

		$whereCuentaCcos = substr($whereCuentaCcos, 3, -1);
		$sqlCcos   = "SELECT cuenta,centro_costo FROM puc WHERE id_empresa='$idEmpresa' AND activo=1 AND ($whereCuentaCcos)";
		$queryCcos = mysql_query($sqlCcos, $link);

		while ($row = mysql_fetch_assoc($queryCcos)) {
			$cuenta = $row['cuenta'];
			$cCos   = $row['centro_costo'];

			$arrayCuentaCcos[$cuenta] = $cCos;
		}

		$arrayGlobalEstado['debito']  = 0;
		$arrayGlobalEstado['credito'] = 0;

		$arrayItemEstado['debito']    = 0;
		$arrayItemEstado['credito']   = 0;

		$acumImpuesto = 0;
		$acumSubtotal = 0;

		$msjErrorCcosto = '';
		foreach ($arrayInventarioFactura AS $valArrayInventario) {
			$totalContabilizacionItem   = 0;
			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$idItemArray       = $valArrayInventario['id_items'];										//ID ITEM
			$descripcionCuenta = $valArrayInventario['check_opcion_contable'];							//GASTO, COSTO, ACTIVO FIJO
			$descripcionCuenta = (strlen($descripcionCuenta) > 4)? $descripcionCuenta : 'precio';

			$cuentaOpcional    = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['cuenta'];		//CUENTA OPCION CONTABILIZACION
			// echo json_encode($arrayCuentasItems[$idItemArray]);
			$cuentaPrecio   = ($descripcionCuenta != 'precio')? $cuentaOpcional: $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
			$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
			$cuentaImpuesto = ($valArrayInventario['cuenta_impuesto'] > 0)? $valArrayInventario['cuenta_impuesto']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

			//========================= CONTABILIZACION MERCANCIA PARA LA VENTA, COSTO, GASTO =========================//
			//*********************************************************************************************************//
			if($descripcionCuenta != 'activo_fijo'){

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += $valArrayInventario['precio']; }
					else{ $arrayAsiento[$cuentaPrecio][$estado] = $valArrayInventario['precio']; }

					$arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
					$arrayItemEstado[$estado]   += $valArrayInventario['precio'];
					$acumSubtotal               += $valArrayInventario['precio'];

					if($valArrayInventario['precio'] == 120916 && $descripcionCuenta != 'costo'){
						echo '-'.$arrayCuentaCcos[$cuentaPrecio].'-'.$cuentaPrecio.'<br>';
					}
					//===================================== LOGICA CCOS ========================================//
					if($descripcionCuenta == 'costo' || $descripcionCuenta == 'gasto' || $arrayCuentaCcos[$cuentaPrecio] == 'Si'){
						//CUENTA PRECIO POR CENTRO DE COSTO

						$arrayAsiento[$cuentaPrecio]['typeCuenta'] = $descripcionCuenta == 'costo'? 'Costo': 'Gasto';
						$idCentroCostos = $valArrayInventario['id_centro_costos'];

						if($idCentroCostos > 0){
							if($arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] == ''){ $arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] = 0; }
							$arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] += $valArrayInventario['precio'];
							$arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['estado']  = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
						}
						else{
							$idCentroCostos = 0;

							if($arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] == ''){ $arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] = 0; }
							$arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['precio'] += $valArrayInventario['precio'];
							$arrayCuentaPrecio[$idCentroCostos][$cuentaPrecio]['estado']  = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];

							$msjErrorCcosto = '\n'.$valArrayInventario['codigo'].' '.$valArrayInventario['nombre_item'];
						}
					}

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
						else{ $arrayAsiento[$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }

						$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
						$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
						$acumImpuesto               += $valArrayInventario['impuesto'];
					}

					//============================== CALC CONTRA PARTIDA PRECIO =================================//
					if($contraPrecio > 0){
						$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
										: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
						else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }

						$arrayGlobalEstado[$estado] += $contraSaldo;
						$arrayItemEstado[$estado]   += $contraSaldo;

						$acumCuentaClientes       = $contraPrecio;
						$acumEstadoCuentaClientes = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion")</script>'; exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion")</script>'; exit;
				}
				else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion")</script>'; exit;
				}
				// else if($msjErrorCcosto != ''){ echo '<script>alert("Aviso.\nLos siguientes items no tienen centro de costo \n'.$msjErrorCcosto.'")</script>'; exit; }
			}
			//======================================= CONTABILIZACION ACTIVO FIJO ======================================//
			//**********************************************************************************************************//
			else{
				if($cuentaPrecio == ''){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' no tiene asignado la cuenta de Activo Fijo en configuracion items")</script>'; exit;
				}

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$totalContabilizacionItem += $valArrayInventario['precio'];

					//========================================= CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado                   = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
						$acumImpuesto             += $valArrayInventario['impuesto'];
						$totalContabilizacionItem += $valArrayInventario['impuesto'];
					}

					//================================ CALC CONTRA PRECIO ACTIVO FIJO ================================//
					if($contraPrecio > 0){
						$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$arrayGlobalEstado[$estado] += $totalContabilizacionItem;
						$arrayItemEstado[$estado]   += $totalContabilizacionItem;

						if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $totalContabilizacionItem; }
						else{ $arrayAsiento[$contraPrecio][$estado] = $totalContabilizacionItem; }

						//ARRAY ASIENTO CONTABLE PRECIO
						$estadoPrecio                     = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
						$arrayGlobalEstado[$estadoPrecio] += $totalContabilizacionItem;
						$arrayItemEstado[$estadoPrecio]   += $totalContabilizacionItem;
						$acumSubtotal                     += $valArrayInventario['precio'];

						//ARRAY ASIENTO CONTABLE ACTIVO FIJO O GASTO
						if($arrayAsiento[$cuentaPrecio][$estadoPrecio] > 0){ $arrayAsiento[$cuentaPrecio][$estadoPrecio] += $totalContabilizacionItem; }
						else{ $arrayAsiento[$cuentaPrecio][$estadoPrecio] = $totalContabilizacionItem; }

						$acumCuentaClientes       = $contraPrecio;
						$acumEstadoCuentaClientes = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion")</script>'; exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion contable")</script>'; exit;
				}
				else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito']==0){
					echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion")</script>'; exit;
				}
			}
		}

		if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
			echo '<script>alert("Aviso.\nHa ocurrido un problema de contabilizacion, favor revise la configuracion de contabilizacion.\nSi el problema persiste consulte al administrador del sistema")</script>'; exit;
		}
		else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0 || $arrayGlobalEstado['debito'] =='' || $arrayGlobalEstado['credito'] ==''){
			echo '<script>alert("Aviso.\nContabilizacion en saldo 0.\nSi el problema persiste consulte al administrador del sistema")</script>'; exit;
		}

		//==================== QUERY RETENCIONES =================//
    	/**********************************************************/
		$acumRetenciones  = 0;
		$contRetencion    = 0;
		$estadoRetencion  = $acumEstadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
		$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM compras_facturas_retenciones WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			$valorBase           = $rowRetenciones['base'];
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

			if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

			//CALCULO RETEIVA
			if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
				if($acumImpuesto<$valorBase) { continue; }															//BASE RETENCION

				$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE Y RETEICA
				if($acumSubtotal<$valorBase) { continue; }															//BASE RETENCION

				$acumRetenciones += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){ echo '<script>alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.")</script>'; exit; }

				if(is_nan($arrayAsiento[$cuentaAutoretencion][$acumEstadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$acumEstadoCuentaClientes] = 0; }
				$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
				$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
			}
		}

		$arrayAsiento[$acumCuentaClientes][$acumEstadoCuentaClientes] -= $acumRetenciones;
		$saldoGlobalFactura = $arrayAsiento[$acumCuentaClientes][$acumEstadoCuentaClientes];						//VARIABLE GLOBAL TOTAL FACTURA

		$totalDebito  = 0;
		$totalCredito = 0;
		$tablaDebug   = '<div style="float:left; width:80px;">Debito</div>
						<div style="float:left; width:80px;">Credito</div>
						<div style="float:left; width:80px;">PUC</div>
						<div style="float:left; width:150px;">Id Centro Costos</div><br>';

		//ASIENTO CONTABLE
		foreach ($arrayAsiento AS $cuenta => $arrayCampo) {
			$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
			$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

			if(is_nan($cuenta) || $cuenta==0){ continue; }

			$cuenta     = $cuenta * 1;
			$subString2 = substr($cuenta, 0, 2);
			$subString4 = substr($cuenta, 0, 4);

			//SALDO POR PAGAR EN FACTURACION
			if($estadoCuentaPago == 'Credito' && $arrayCampo['type']=='cuentaPago'){ $saldoGlobalFacturaSinAbono += ($arrayCampo['debito'] > $arrayCampo['credito'])? $arrayCampo['debito']: $arrayCampo['credito']; }

			//SI TIENE CUENTA DE PRECIO POR CENTRO DE COSTO
			if($arrayCampo['typeCuenta'] == 'Costo' || $arrayCampo['typeCuenta'] == 'Gasto'){ continue; }

			//ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO
			$valueInsertAsientos .= "('$idFactura',
									'$consecutivoFactura',
									'FC',
									'Factura de Compra',
									'$idFactura',
									'FC',
									'$consecutivoFactura',
									'$fechaInicioFactura',
									'".$arrayCampo['debito']."',
									'".$arrayCampo['credito']."',
									'$cuenta',
									'$idProveedor',
									'$idSucursal',
									'$idEmpresa',
									0,
									3),";

			$tablaDebug .= '<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
							<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
							<div style="float:left; width:80px;">-'.$cuenta.'</div>
							<div style="float:left; width:150px;">-</div><br>';
		}

		//CUENTA GASTO POR CENTRO DE COSTO
		foreach ($arrayCuentaPrecio AS $idCentroCosto => $arrayCentroCosto) {
			foreach ($arrayCentroCosto AS $cuentaPrecio => $arrayCampo) {
				$arrayCuenta['debito']  = '';
				$arrayCuenta['credito'] = '';

				$arrayCuenta[$arrayCampo['estado']] = $arrayCampo['precio'];

				$valueInsertAsientos .= "('$idFactura',
										'$consecutivoFactura',
										'FC',
										'Factura de Compra',
										'$idFactura',
										'FC',
										'$consecutivoFactura',
										'$fechaInicioFactura',
										'".$arrayCuenta['debito']."',
										'".$arrayCuenta['credito']."',
										'$cuentaPrecio',
										'$idProveedor',
										'$idSucursal',
										'$idEmpresa',
										'$idCentroCosto',
										3),";

				$tablaDebug .= '<div style="float:left; width:80px;">*'.$arrayCuenta['debito'].'</div>
								<div style="float:left; width:80px;">-'.$arrayCuenta['credito'].'</div>
								<div style="float:left; width:80px;">-'.$cuentaPrecio.'</div>
								<div style="float:left; width:150px;">-'.$idCentroCosto.'</div><br>';
			}
		}
		$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$totalCredito.'</div><br>';
		// echo $tablaDebug; exit;
		if($totalDebito != $totalCredito){ echo '<script>alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }

		$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
		$sqlInsertCuentasColgaap = "INSERT INTO asientos_colgaap (
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
										id_centro_costos,
										debug_ccos)
									VALUES $valueInsertAsientos";
		$queryInsertCuentasColgaap = mysql_query($sqlInsertCuentasColgaap,$link);
    }

?>
