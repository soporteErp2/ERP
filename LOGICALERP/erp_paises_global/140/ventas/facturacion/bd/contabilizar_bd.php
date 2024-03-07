<?php
	function estadoCuentaColgaap($descripcion){
		switch ($descripcion) {
			case 'COSTO':
				return 'credito';
				break;

			case 'CONTRAPARTIDA COSTO':
				return 'debito';
				break;

			case 'PRECIO':
				return 'credito';
				break;

			case 'IMPUESTO':
				return 'credito';
				break;

			case 'CONTRAPARTIDA PRECIO':
				return 'debito';
				break;

			default:
				return 'error';
				break;
		}
	}

    function contabilizarConPlantilla($fechaFactura,$consecutivoFactura,$idBodega,$idSucursal,$idEmpresa,$idPlantilla,$idFactura,$idCliente,$exento_iva,$link){
    	$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;
    	global $saldoGlobalfactura, $saldoGlobalFacturaSinAbono, $cuentaPago;

    	//PLANTILLA CONFIGURACION
		$sqlPlantilla   = "SELECT codigo_puc,descripcion,porcentaje FROM plantillas_configuracion WHERE plantillas_id='$idPlantilla' AND activo=1";
		$queryPlantilla = mysql_query($sqlPlantilla,$link);

		$tablaDebug = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';

		$porcentajeIva        = 0;
		$contCuentasPantillas = 0;
		while($rowplantilla = mysql_fetch_array($queryPlantilla)){

			$contCuentasPantillas++;
			$estadoCuenta = estadoCuentaColgaap($rowplantilla['descripcion']);
			if($estadoCuenta == 'error'){ echo '<script>alert("Aviso.\nLa plantilla ingresada tiene descripcion sin estado.")</script>'; exit; }

			$idCuenta = $rowplantilla['id'];
			$cuenta   = $rowplantilla['codigo_puc'];
			$arrayCuentas[$cuenta] = array('puc' => $cuenta, 'caracter' => $estadoCuenta, 'descripcion' => $rowplantilla['descripcion']);

			if($rowplantilla['descripcion'] == 'IMPUESTO'){
				if($rowplantilla['porcentaje'] == ''){ echo '<script>alert("Aviso.\nLa plantilla ingresada tiene iva sin porcentaje.")</script>'; exit; }
				$porcentajeIva = $rowplantilla['porcentaje'];
			}

			$valueInsertContabilizacion .= "('$cuenta',
											'$estadoCuenta',
											'".$rowplantilla['descripcion']."',
											'".$rowplantilla['porcentaje']."',
											'$idFactura',
											'FV',
											'$idEmpresa',
											'$idSucursal',
											'$idBodega'),";

			if($rowplantilla['descripcion'] == 'CONTRAPARTIDA PRECIO'){ $cuentaPago = $cuenta; }
		}

		if($contCuentasPantillas == 0){ echo '<script>alert("Aviso.\nLa plantilla ingresada no tiene configuracion contable.")</script>'; exit; }
		else if($contCuentasPantillas == 1){ echo '<script>alert("Aviso.\nLa plantilla ingresada no cumple doble partida en Colgaap.")</script>'; exit; }

		//VENTAS FACTURA INVENTARIO
		$sqlArticulos = "SELECT cantidad,
								costo_unitario AS precio,
								costo_inventario AS costo,
								tipo_descuento,
								descuento,
								nombre_consecutivo_referencia AS doc_referencia
						FROM ventas_facturas_inventario
						WHERE activo=1 AND id_factura_venta='$idFactura'";
		$queryArticulos = mysql_query($sqlArticulos,$link);

		$costoAcumulado  = 0;
		$precioAcumulado = 0;
		$ivaAcumulado    = 0;
		while($rowArticulo = mysql_fetch_array($queryArticulos)){

			if($rowArticulo['doc_referencia'] != 'Remision'){ $costo = $rowArticulo['costo']* $rowArticulo['cantidad']; }	//CALCULO DEL COSTO
			$precio = $rowArticulo['precio']* $rowArticulo['cantidad'];														//CALCULO DEL PRECIO

			if($rowArticulo['descuento'] > 0){
				$precio = ($rowArticulo['tipo_descuento'] == 'porcentaje')? $precio-($rowArticulo['descuento']*100/$precio) : $precio-$rowArticulo['descuento'];
			}

			$costoAcumulado  += $costo;
			$precioAcumulado += $precio;
		}

		//SI HAY CUENTA IVA SE DISCRIMINA DEL PRECIO
		if($porcentajeIva > 0){ $ivaAcumulado = ROUND($precioAcumulado*$porcentajeIva/100, $decimalesMoneda); }

		//RETENCIONES INDEPENDIENTES DE LA PLANTILLA
		$acumRetenciones  = 0;
		$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM ventas_facturas_retenciones WHERE id_factura_venta='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		$contAutoRetencion = 0;
		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			$valorBase           = $rowRetenciones['base'];
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

			if($arrayRetenciones[$codigoRetencion] == ''){ $arrayRetenciones[$codigoRetencion] = 0; }

			if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
				if($exento_iva=='Si' || $ivaAcumulado<$valorBase) { continue; }

				if($porcentajeIva == 0){ echo '<script>alert("Aviso.\nLa plantilla ingresada tiene no cuenta con iva. para continuar quite la RETENCION al iva (Reteiva) o agregue el iva a la plantilla.")</script>'; exit; }
				$acumRetenciones += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$codigoRetencion] += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE, RETECREE Y RETEICA
				if($precioAcumulado<$valorBase) { continue; }

				$acumRetenciones += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$codigoRetencion] += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){																	//DEVOLUCION SALDO AUTORETENCION NO RESTA
				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){ echo '<script>alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.")</script>'; exit; }
				$contAutoRetencion++;
				$arrayAutoRetencion[$contAutoRetencion] = $cuentaAutoretencion;
				$acumRetenciones -= ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$cuentaAutoretencion] += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
			}
		}

		$cuentaContraPartida = $precioAcumulado+$ivaAcumulado-$acumRetenciones;
		$valueInsertAsientos = '';
		$saldoGlobalfactura  = $cuentaContraPartida;																//VARIABLE GLOBAL TOTAL FACTURA

		$globalDebito  = 0;
		$globalCredito = 0;
		$tablaDebug    = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';
		if($contCuentasPantillas > 1){
			foreach ($arrayCuentas AS $cuentaPuc => $valArrayCuenta){
				$saldo         = 0;
				$configCuenta2 = substr($cuentaPuc, 0, 2);
				$configCuenta4 = substr($cuentaPuc, 0, 4);

				if($valArrayCuenta['descripcion'] == 'CONTRAPARTIDA PRECIO'){ $saldo = $cuentaContraPartida; }		//CLIENTES, CAJA, BANCOS
				else if( $valArrayCuenta['descripcion'] == 'IMPUESTO'){ $saldo = $ivaAcumulado; }					//IVA
				else if( $valArrayCuenta['descripcion'] == 'COSTO' || $valArrayCuenta['descripcion'] == 'CONTRAPARTIDA COSTO'){ $saldo = $costoAcumulado; }		//COMERCIO AL MAYOR Y AL POR MENOR -> COSTOS
				else{ $saldo = $precioAcumulado; }																	//CONTRAPARTIDA-RETENCIONES

				$totalDebito  = ($valArrayCuenta['caracter'] == 'debito')? $saldo : 0;
				$totalCredito = ($valArrayCuenta['caracter'] == 'credito')? $saldo : 0;

				if($configCuenta4 == 1305){ $saldoGlobalFacturaSinAbono += $saldo; }								//ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO

				$globalDebito  += $totalDebito;
				$globalCredito += $totalCredito;

				$valueInsertAsientos .= "('$idFactura',
											'$consecutivoFactura',
											'FV',
											'$idFactura',
											'$consecutivoFactura',
											'FV',
											'Factura de Venta',
											'$fechaFactura',
											'$totalDebito',
											'$totalCredito',
											'$cuentaPuc',
											'$idCliente',
											'$idSucursal',
											'$idEmpresa'),";

				$tablaDebug  .='<div style="float:left; width:80px;">'.$totalDebito.'</div><div style="float:left; width:80px;">'.$totalCredito.'</div><div style="float:left; width:80px;">'.$cuentaPuc.'</div><br>';
			}

			foreach ($arrayRetenciones AS $cuentaPuc => $valArrayRetencion){

				$totalDebito  = $valArrayRetencion;
				$totalCredito = 0;

				for ($i=1; $i <= $contAutoRetencion; $i++) { if($arrayAutoRetencion[$i] == $cuentaPuc){ $totalCredito = $valArrayRetencion; $totalDebito = 0; break; } }

				$globalDebito  += $totalDebito;
				$globalCredito += $totalCredito;

				$valueInsertAsientos .= "('$idFactura',
											'$consecutivoFactura',
											'FV',
											'$idFactura',
											'$consecutivoFactura',
											'FV',
											'Factura de Venta',
											'$fechaFactura',
											'$totalDebito',
											'$totalCredito',
											'$cuentaPuc',
											'$idCliente',
											'$idSucursal',
											'$idEmpresa'),";
				$tablaDebug  .='<div style="float:left; width:80px;">'.$totalDebito.'</div><div style="float:left; width:80px;">'.$totalCredito.'</div><div style="float:left; width:80px;">'.$cuentaPuc.'</div><br>';
			}

			$globalDebito  = ROUND($globalDebito, $decimalesMoneda);
			$globalCredito = ROUND($globalCredito, $decimalesMoneda);

			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$globalDebito.'-</div><div style="float:left; width:80px; border-top:1px solid">-'.$globalCredito.'-</div><br>';
			// echo $tablaDebug; exit;

			if($globalDebito != $globalCredito){ echo '<script>alert("Aviso.\nLa plantilla ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }

			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sqlContabilizar     = "INSERT INTO asientos_colgaap (
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

			if(!$queryContabilizar){ echo '<script>alert("Error.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema")</script>'; exit; }

			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
			$sqlContabilizar     = "INSERT INTO contabilizacion_compra_venta (codigo_puc,caracter,descripcion,porcentaje,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega) VALUES $valueInsertContabilizacion";
			$queryContabilizar   = mysql_query($sqlContabilizar,$link);
		}
		else{ echo '<script>alert("Aviso.\nLa plantilla ingresada no tiene configuracion contable.")</script>'; exit; }
    }

    function contabilizarSinPlantilla($arrayAnticipo,$arrayCuentaPago,$idCcos,$fechaFactura,$consecutivoFactura,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idCliente,$exento_iva,$link){

    	global $saldoGlobalfactura, $saldoGlobalFacturaSinAbono;
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

		$sqlDoc = "SELECT VF.id,
						VF.id_consecutivo_referencia AS id_referencia,
						VF.nombre_consecutivo_referencia AS nombre_referencia,
						VF.id_inventario AS id_item,
						VF.codigo,
						VF.cantidad,
						VF.costo_unitario AS precio,
						VF.costo_inventario AS costo,
						VF.descuento,
						VF.tipo_descuento,
						VF.id_impuesto,
						VF.valor_impuesto,
						VF.inventariable,
						I.cuenta_venta AS cuenta_iva
					FROM ventas_facturas_inventario AS VF LEFT JOIN impuestos AS I ON(
							I.activo=1
							AND I.id=VF.id_impuesto
						)
					WHERE VF.id_factura_venta='$idFactura' AND VF.activo=1";
		$queryDoc = mysql_query($sqlDoc,$link);

		while($rowDoc = mysql_fetch_array($queryDoc)){
			$typeDocumento = 'FV';
			$impuesto      = 0;

			//CALCULO DEL PRECIO
			$precio = $rowDoc['precio'] * $rowDoc['cantidad'];
			$costo  = ABS($rowDoc['costo'] * $rowDoc['cantidad']);
			if($rowDoc['descuento'] > 0){
				$precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio-ROUND(($rowDoc['descuento']*$precio)/100, $decimalesMoneda) : $precio-$rowDoc['descuento'];
			}

			// if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0 && $exento_iva!='Si'){  $impuesto = ROUND($precio*$rowDoc['valor_impuesto']/100, $decimalesMoneda); }
			if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0 && $exento_iva!='Si'){  $impuesto = $precio*$rowDoc['valor_impuesto']/100; }

			$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
			$whereIdItemsCuentas .='id_items = '.$rowDoc['id_item'];

			$arrayInventarioFactura[$rowDoc['id']]  = array('id_factura_inventario' =>$rowDoc['id'],
															'precio'                =>$precio,
															'codigo'                =>$rowDoc['codigo'],
															'id_referencia'         =>$rowDoc['id_referencia'],
															'nombre_referencia'     =>$rowDoc['nombre_referencia'],
															'impuesto'              =>$impuesto,
															'costo'                 =>$costo,
															'id_items'              =>$rowDoc['id_item'],
															'inventariable'         =>$rowDoc['inventariable'],
															'cuenta_iva'            =>$rowDoc['cuenta_iva']);
		}

		$sqlItemsCuentas = "SELECT id, id_items,descripcion, puc, tipo
							FROM items_cuentas
							WHERE activo=1
								AND id_empresa='$idEmpresa'
								AND estado='venta'
								AND ($whereIdItemsCuentas)
							GROUP BY id_items,descripcion
							ORDER BY id_items ASC";
		$queryItemsCuentas = mysql_query($sqlItemsCuentas,$link);

		while ($rowCuentasItems = mysql_fetch_array($queryItemsCuentas)) {

			if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; $rowCuentasItems['tipo'] = 'debito'; }
			if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['tipo'] = 'credito'; }

			$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']]= array('estado' => $rowCuentasItems['tipo'], 'cuenta'=> $rowCuentasItems['puc']);

			$valueInsertContabilizacion .= "('".$rowCuentasItems['id_items']."',
											'".$rowCuentasItems['puc']."',
											'".$rowCuentasItems['tipo']."',
											'".$rowCuentasItems['descripcion']."',
											'$idFactura',
											'FV',
											'$idEmpresa',
											'$idSucursal',
											'$idBodega'),";
		}

		$arrayGlobalEstado['debito']  = 0;
		$arrayGlobalEstado['credito'] = 0;

		$arrayItemEstado['debito']    = 0;
		$arrayItemEstado['credito']   = 0;

		$acumSubtotal = 0;
		$acumImpuesto = 0;
		// print_r($arrayInventarioFactura);
		foreach ($arrayInventarioFactura AS $valArrayInventario) {

			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$idItemArray    = $valArrayInventario['id_items'];
			$cuentaPrecio   = $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
			$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
			$cuentaImpuesto = ($valArrayInventario['cuenta_iva'] > 0)? $valArrayInventario['cuenta_iva']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

			$cuentaCosto = $arrayCuentasItems[$idItemArray]['costo']['cuenta'];
			$contraCosto = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['cuenta'];

			//======================================= CALC PRECIO =====================================//
			if($cuentaPrecio > 0){
				$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

				//ARRAY ASIENTO CONTABLE
				if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += ROUND($valArrayInventario['precio'],$_SESSION['DECIMALESMONEDA']); }
				else{ $arrayAsiento[$cuentaPrecio][$estado] = ROUND($valArrayInventario['precio'],$_SESSION['DECIMALESMONEDA']); }
				$arrayAsiento[$cuentaPrecio]['idCcos'] = $idCcos;

				$arrayGlobalEstado[$estado] += ROUND($valArrayInventario['precio'],$_SESSION['DECIMALESMONEDA']);
				$arrayItemEstado[$estado]   += ROUND($valArrayInventario['precio'],$_SESSION['DECIMALESMONEDA']);
				$acumSubtotal               += ROUND($valArrayInventario['precio'],$_SESSION['DECIMALESMONEDA']);

				// $arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
				// $arrayItemEstado[$estado]   += $valArrayInventario['precio'];
				// $acumSubtotal               += $valArrayInventario['precio'];

				//===================================== CALC IMPUESTO ========================================//
				if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
					$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
					else{ $arrayAsiento[$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }
					$arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

					// $arrayGlobalEstado[$estado] += ROUND($valArrayInventario['impuesto'],$_SESSION['DECIMALESMONEDA']);
					// $arrayItemEstado[$estado]   += ROUND($valArrayInventario['impuesto'],$_SESSION['DECIMALESMONEDA']);
					// $acumImpuesto               += ROUND($valArrayInventario['impuesto'],$_SESSION['DECIMALESMONEDA']);

					$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
					$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
					$acumImpuesto               += $valArrayInventario['impuesto'];

				}

				//============================== CALC CONTRA PARTIDA PRECIO =================================//
				if($contraPrecio > 0){
					$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
					$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];



					$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
									:  $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
					else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
					$arrayAsiento[$contraPrecio]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $contraSaldo;
					$arrayItemEstado[$estado]   += $contraSaldo;

					$acumCuentaClientes   = $contraPrecio;
					$estadoCuentaClientes = $estado;
				}
			}
			else if($valArrayInventario['inventariable'] == 'false'){
				echo '<script>
						alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			//======================================= CALC COSTO ===========================================//
			if( $cuentaCosto > 0 && $contraCosto > 0 && $valArrayInventario['nombre_referencia'] != 'Remision' ){

				$estado = $arrayCuentasItems[$idItemArray]['costo']['estado'];

				//ARRAY ASIENTO CONTABLE
				if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $valArrayInventario['costo']; }
				else{ $arrayAsiento[$cuentaCosto][$estado] = $valArrayInventario['costo']; }
				$arrayAsiento[$cuentaCosto]['idCcos'] = 0;

				$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
				$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

				//ARRAY ASIENTO CONTABLE
				$estado = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['estado'];
				if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $valArrayInventario['costo']; }
				else{ $arrayAsiento[$contraCosto][$estado] = $valArrayInventario['costo']; }
				$arrayAsiento[$contraCosto]['idCcos'] = $idCcos;

				$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
				$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

			}
			else if($valArrayInventario['inventariable'] == 'true' && $valArrayInventario['nombre_referencia'] != 'Remision'){
				echo '<script>
						alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado el manejo del costo en la contabilizacion");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

		}


		$arrayGlobalEstado['debito']  = ROUND($arrayGlobalEstado['debito'],$_SESSION['DECIMALESMONEDA']);
		$arrayGlobalEstado['credito'] = ROUND($arrayGlobalEstado['credito'],$_SESSION['DECIMALESMONEDA']);

		print_r($arrayAsiento);
		if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
			echo '<script>
					alert("Aviso.\nEl saldo debito es diferente al credito, favor revise la configuracion de contabilizacion!");
					console.log("'.$arrayGlobalEstado['debito'].' - '.$arrayGlobalEstado['credito'].'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0){
			echo '<script>
					alert("Aviso,\nEl saldo debito y credito en norma colgaap debe ser mayor a 0!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//==================== QUERY RETENCIONES =================//
    	/**********************************************************/
		$acumRetenciones  = 0;
		$contRetencion    = 0;
		$estadoRetencion  = $estadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
		$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM ventas_facturas_retenciones WHERE id_factura_venta='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);
		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			$valorBase           = $rowRetenciones['base'];
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

			if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

			//CALCULO RETEIVA
			if($tipoRetencion == "ReteIva" ){
				if ($exento_iva=='Si' || $acumImpuesto<$valorBase) { continue; }

				$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE, RETECREE Y RETEICA
				if ($acumSubtotal<$valorBase) { continue; }

				$acumRetenciones += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
					echo '<script>
							alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				if(is_nan($arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
				$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
				$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
			}
		}

		$arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $acumRetenciones;
		$saldoGlobalfactura = $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes];												//VARIABLE GLOBAL TOTAL FACTURA

		//=========================// SALDO ANTICIPO //=========================//
		//**********************************************************************//
		$saldoClientes = $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes];
		$saldoAnticipo = $arrayAnticipo['total'];

		if($saldoAnticipo > 0 && $saldoAnticipo > $saldoClientes){
			echo'<script>
					alert("Aviso.\nLos anticipos no pueden ser mayores a la factura de venta!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else{ $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $saldoAnticipo; }

		foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
			$arrayCampo['debito']  = 0;
			$arrayCampo['credito'] = 0;

			$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

			$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
			$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
		}

		$totalDebito  = 0;
		$totalCredito = 0;
		$tablaDebug   = '<div style="float:left; width:80px;">Id</div>
						<div style="float:left; width:80px;">Documento</div>
						<div style="float:left; width:80px;">Debito</div>
						<div style="float:left; width:80px;">Credito</div>
						<div style="float:left; width:80px;">PUC</div>
						<div style="float:left; width:150px;">Id Centro Costos</div><br>';

		//=========================// CONTABILIZACION //=========================//
		//***********************************************************************//
		// print_r($arrayAsiento);
		$contAnticipos = 0;
		foreach ($arrayAsiento AS $cuenta => $arrayCampo) {

			if(is_nan($cuenta) || $cuenta==0){ continue; }
			$cuenta = $cuenta * 1;

			$arrayCampo['debito'] = round($arrayCampo['debito'],$_SESSION['DECIMALESMONEDA']);
			$arrayCampo['credito'] = round($arrayCampo['credito'],$_SESSION['DECIMALESMONEDA']);

			$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
			$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

			if($arrayCampo['debito'] > 0 || $arrayCampo['credito'] > 0){

				// SALDO POR PAGAR EN FACTURACION
				if($estadoCuentaPago == 'Credito' && $arrayCampo['type']=='cuentaPago'){ $saldoGlobalFacturaSinAbono += ($arrayCampo['debito'] > $arrayCampo['credito'])? $arrayCampo['debito']: $arrayCampo['credito']; } //ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO

				// BODY INSERT
				$valueInsertAsientos .= "('$idFactura',
										'$consecutivoFactura',
										'FV',
										'Factura de Venta',
										'$idFactura',
										'FV',
										'$consecutivoFactura',
										'$fechaFactura',
										'".$arrayCampo['debito']."',
										'".$arrayCampo['credito']."',
										'$cuenta',
										'$idCliente',
										'".$arrayCampo['idCcos']."',
										'$idSucursal',
										'$idEmpresa'),";

				$tablaDebug  .='<div style="overflow:hidden;">
									<div style="float:left; width:80px;">-'.$idFactura.'</div>
									<div style="float:left; width:80px;">FV</div>
									<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
									<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
									<div style="float:left; width:80px;">-'.$cuenta.'</div>
								</div><br>';
			}

			//=======================// ANTICIPOS //=======================//
			if($acumCuentaClientes == $cuenta){

				foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
					$contAnticipos++;

					$arrayCampo['debito']  = 0;
					$arrayCampo['credito'] = 0;

					$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

					$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
					$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

					if(is_nan($datosAnticipo['cuenta_colgaap']) || $datosAnticipo['cuenta_colgaap']==0){ continue; }
					$datosAnticipo['cuenta_colgaap'] = $datosAnticipo['cuenta_colgaap'] * 1;

					$valueInsertAsientos .= "('$idFactura',
											'$consecutivoFactura',
											'FV',
											'Factura de Venta',
											'$datosAnticipo[id_anticipo]',
											'$datosAnticipo[tipo_documento]',
											'$datosAnticipo[consecutivo]',
											'$fechaFactura',
											'$arrayCampo[debito]',
											'$arrayCampo[credito]',
											'$datosAnticipo[cuenta_colgaap]',
											'$datosAnticipo[id_tercero]',
											'$idCcos',
											'$idSucursal',
											'$idEmpresa'),";

					$tablaDebug .= '<div style="overflow:hidden;">
										<div style="float:left; width:80px;">-'.$idAnticipo.'</div>
										<div style="float:left; width:80px;">RC</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
										<div style="float:left; width:80px;">-'.$datosAnticipo['cuenta_colgaap'].'</div>
									<div><br>';
				}
			}
		}

		$tablaDebug .= '<div style="overflow:hidden;">
							<div style="float:left; width:80px; border-top:1px solid">-</div>
							<div style="float:left; width:80px; border-top:1px solid">-</div>
							<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div>
							<div style="float:left; width:80px; border-top:1px solid">-'.$totalCredito.'</div>
						</div><br>';

		echo $tablaDebug;
		 // exit;
		$saldoGlobalFacturaSinAbono = ROUND($saldoGlobalFacturaSinAbono,$decimalesMoneda);
		$totalDebito  = ROUND($totalDebito,$decimalesMoneda);
		$totalCredito = ROUND($totalCredito,$decimalesMoneda);
		if($totalDebito != $totalCredito){
			echo '<script>
					alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
					console.log("totalDebito: '.$totalDebito.' totalCredito: '.$totalCredito.'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		if($contAnticipos>0){
			$sqlAnticipo = "UPDATE recibo_caja_cuentas AS C, anticipos AS A
							SET C.saldo_pendiente=C.saldo_pendiente-A.valor
							WHERE C.id=A.id_cuenta_anticipo
								AND C.activo=1
								AND A.activo=1
								AND A.id_documento='$idFactura'
								AND A.tipo_documento='FV'";
			$queryAnticipo = mysql_query($sqlAnticipo,$link);
		}

		$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
		$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

		$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta (
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

		if($valueInsertAsientos != ''){
			$sqlInsertCuentasColgaap   = "INSERT INTO asientos_colgaap (
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
											id_centro_costos,
											id_sucursal,
											id_empresa)
										VALUES $valueInsertAsientos";
			$queryInsertCuentasColgaap = mysql_query($sqlInsertCuentasColgaap,$link);
			// contabilizacionSimultanea($idFactura,'FV',$idSucursal,$idEmpresa,$link);
		}
    }
?>
