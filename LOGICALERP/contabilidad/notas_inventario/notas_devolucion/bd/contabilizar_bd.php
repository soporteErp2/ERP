<?php

  function contabilizarNotaFacturaCompraConPlantilla($idNota,$idBodega,$idSucursal,$idEmpresa,$idPlantilla,$idFactura,$idProveedor,$link,$fecha,$numero_documento_cruce,$totalFactura){
    	$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;
    	global $saldoGlobalNotaSinAbono;

    	//PLANTILLA CONFIGURACION
		$sqlPlantilla   = "SELECT codigo_puc,caracter,porcentaje,descripcion FROM contabilizacion_compra_venta WHERE id_documento='$idFactura' AND tipo_documento='FC'";
		$queryPlantilla = mysql_query($sqlPlantilla,$link);

		$tablaDebug = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';

		$porcentajeIva        = 0;
		$contCuentasPantillas = 0;
		while($rowplantilla = mysql_fetch_array($queryPlantilla)){

			$contCuentasPantillas++;
			$caracter = ($rowplantilla['caracter'] == 'debito')? 'credito' : 'debito';

			$idCuenta = $rowplantilla['id'];
			$cuenta   = $rowplantilla['codigo_puc'];
			$arrayCuentas[$cuenta] = array('puc' => $cuenta, 'caracter' => $caracter, 'descripcion' => $rowplantilla['descripcion']);

			if($rowplantilla['descripcion'] == 'IMPUESTO'){
				if($rowplantilla['porcentaje'] == ''){ echo '<script>alert("Aviso.\nLa plantilla ingresada tiene iva sin porcentaje.")</script>'; exit; }
				$porcentajeIva = $rowplantilla['porcentaje'];
			}
		}

		if($contCuentasPantillas == 0){ echo '<script>alert("Aviso.\nLa plantilla ingresada no tiene configuracion contable.")</script>'; exit; }
		else if($contCuentasPantillas == 1){ echo '<script>alert("Aviso.\nLa plantilla ingresada no cumple doble partida.")</script>'; exit; }

		//VENTAS FACTURA INVENTARIO
		$sqlArticulos = "SELECT cantidad,
								costo_unitario AS precio,
								tipo_descuento,
								descuento
						FROM devoluciones_compra_inventario
						WHERE activo=1 AND id_devolucion_compra='$idNota'";
		$queryArticulos = mysql_query($sqlArticulos,$link);

		$precioAcumulado = 0;
		$ivaAcumulado    = 0;
		while($rowArticulo = mysql_fetch_array($queryArticulos)){

			$precio = $rowArticulo['precio']* $rowArticulo['cantidad'];														//CALCULO DEL PRECIO

			if($rowArticulo['descuento'] > 0){
				$precio = ($rowArticulo['tipo_descuento'] == 'porcentaje')? $precio-ROUND($rowArticulo['descuento']*100/$precio, $decimalesMoneda) : $precio-$rowArticulo['descuento'];
			}

			$precioAcumulado += $precio;
		}

		//SI HAY CUENTA IVA SE DISCRIMINA DEL PRECIO
		if($porcentajeIva > 0){ $ivaAcumulado = ROUND($precioAcumulado*$porcentajeIva/100, $decimalesMoneda); }

		//==================== QUERY RETENCIONES =================//
    	/**********************************************************/
		$acumRetenciones  = 0;
		$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM compras_facturas_retenciones WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		$contAutoRetencion = 0;
		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			// $valorBase           = $rowRetenciones['base'];
			if ($tipoRetencion=='ReteIva') {
            	$valorBase =($ivaAcumulado>=$rowRetenciones['base'])? 0 : $rowRetenciones['base'] ;
	        }
	        else{
	            $valorBase =($precio>=$rowRetenciones['base'])? 0 : $rowRetenciones['base'] ;
	        }

			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

			if($arrayRetenciones[$codigoRetencion] == ''){ $arrayRetenciones[$codigoRetencion] = 0; }

			if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
				if($ivaAcumulado<$valorBase) { continue; }													//VALIDACION BASE

				if($porcentajeIva == 0){ echo '<script>alert("Aviso.\nLa plantilla ingresada tiene no cuenta Colgaap con iva. para continuar quite la RETENCION al iva (Reteiva).")</script>'; exit; }
				$acumRetenciones += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$codigoRetencion] += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE Y RETEICA
				if($precioAcumulado<$valorBase) { continue; }													//VALIDACION BASE

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
		$saldoGlobalNotaSinAbono = 0;

		$globalDebito  = 0;
		$globalCredito = 0;
		$tablaDebug    = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';
		if($contCuentasPantillas > 1){
			foreach ($arrayCuentas AS $cuentaPuc => $valArrayCuenta){
				$saldo         = 0;
				$configCuenta2 = substr($cuentaPuc, 0, 2);

				if($valArrayCuenta['descripcion'] == 'CONTRAPARTIDA PRECIO'){ $saldo = $cuentaContraPartida; }		//CAJA, BANCOS, PROVEEDORES, OBLIGACIONES FINANCIERAS
				else if( $valArrayCuenta['descripcion'] == 'IMPUESTO'){ $saldo = $ivaAcumulado; }					//IVA
				else{ $saldo = $precioAcumulado; }														//CONTRAPARTIDA-RETENCIONES

				if($configCuenta2 == 22){ $saldoGlobalNotaSinAbono += $saldo; }						//ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO

				$totalDebito  = ($valArrayCuenta['caracter'] == 'debito')? $saldo : 0;
				$totalCredito = ($valArrayCuenta['caracter'] == 'credito')? $saldo : 0;

				$globalDebito  += $totalDebito;
				$globalCredito += $totalCredito;

				$valueInsertAsientos .= "('$idNota',
											'NDFC',
											'Nota Devolucion Factura de Compra',
											'$idFactura',
											'FC',
											'$numero_documento_cruce',
											'$fecha',
											'$totalDebito',
											'$totalCredito',
											'$cuentaPuc',
											'$idProveedor',
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

				$valueInsertAsientos .= "('$idNota',
											'NDFC',
											'Nota Devolucion Factura de Compra',
											'$idFactura',
											'FC',
											'$numero_documento_cruce',
											'$fecha',
											'$totalDebito',
											'$totalCredito',
											'$cuentaPuc',
											'$idProveedor',
											'$idSucursal',
											'$idEmpresa'),";
				$tablaDebug  .='<div style="float:left; width:80px;">'.$totalDebito.'</div><div style="float:left; width:80px;">'.$totalCredito.'</div><div style="float:left; width:80px;">'.$cuentaPuc.'</div><br>';
			}

			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">'.$globalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$globalCredito.'</div><br>';
			//echo $tablaDebug; exit;
			if($globalDebito != $globalCredito){ echo '<script>alert("Aviso.\nLa plantilla ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }

			//VALIDACION SALDO FACURA Y SALDO NOTA
			if($saldoGlobalNotaSinAbono > $totalFactura && $saldoGlobalNotaSinAbono > 0){ echo '<script>alert("Aviso.\nEl saldo de la factura es insuficiente para realizar la nota!")</script>'; exit; }


			if($saldoGlobalNotaSinAbono > 0){
				$updateSaldoFactura = "UPDATE compras_facturas SET total_factura_sin_abono=(total_factura_sin_abono - $saldoGlobalNotaSinAbono) WHERE activo=1 AND id_empresa='$idEmpresa' AND id='$idFactura'";
				$querySaldoFactura  = mysql_query($updateSaldoFactura,$link);

				if(!$querySaldoFactura){ echo '<script>alert("Error No 2.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema")</script>'; exit; }
			}

			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sqlContabilizar     = "INSERT INTO asientos_colgaap (
										id_documento,
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
										id_empresa)
									VALUES $valueInsertAsientos";
			$queryContabilizar   = mysql_query($sqlContabilizar,$link);

			contabilizacionSimultanea($idDocumento,'NCG',$id_sucursal,$idEmpresa,$link);

			if(!$queryContabilizar){ echo '<script>alert("Error No 3.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema")</script>'; exit; }
		}
		else{ echo '<script>alert("Aviso.\nLa plantilla ingresada no tiene configuracion contable.")</script>'; exit; }
  }

  function contabilizarNotaFacturaCompraSinPlantilla($estadoCuentaPago,$idNota,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idProveedor,$link,$fecha,$numero_documento_cruce,$totalFactura){

    global $saldoGlobalNotaSinAbono;
		$typeDocumento   = 'NDFC';
		$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;

		//============================= QUERY CUENTAS ============================//
		$ivaAcumulado      = 0;
		$costoAcumulado    = 0;
		$precioAcumulado   = 0;

		$whereIdItemsCuentas  = '';

		$sqlDoc  = "SELECT
                  D.id,
      						D.id_inventario AS id_item,
      						D.codigo,
      						D.cantidad,
      						D.costo_unitario AS precio,
      						D.descuento,
      						D.tipo_descuento,
      						D.id_impuesto,
      						D.valor_impuesto,
      						D.inventariable,
      						D.id_centro_costos,
      						D.check_opcion_contable,
                  I.cruzar_costo_activo_fijo,
                  I.cuenta_compra,
      						if(I.cuenta_compra_devolucion > 0, I.cuenta_compra_devolucion, I.cuenta_compra) AS cuenta_iva
      					    FROM devoluciones_compra_inventario AS D LEFT JOIN impuestos AS I ON(
      							D.id_impuesto = I.id
      							AND I.activo = 1
      						)
      					WHERE D.id_devolucion_compra = '$idNota'
                AND D.activo = 1
      					GROUP BY D.id";
		$queryDoc = mysql_query($sqlDoc,$link);
		while($rowDoc = mysql_fetch_array($queryDoc)){

      //CALCULO DEL PRECIO
			$precio = $rowDoc['precio'] * $rowDoc['cantidad'];
			$costo  = $rowDoc['costo'] * $rowDoc['cantidad'];

			if($rowDoc['descuento'] > 0){ $precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio - ROUND(($rowDoc['descuento'] * $precio) / 100, $decimalesMoneda) : $precio - $rowDoc['descuento']; }

			$impuesto = 0;
			if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){ $impuesto = ROUND($precio*$rowDoc['valor_impuesto']/100, $decimalesMoneda); }

			$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR id_item = '.$rowDoc['id_item']: 'id_item = '.$rowDoc['id_item'];

			$arrayInventarioFactura[$rowDoc['id']]  = array(
                                                        'id_factura_inventario'     => $rowDoc['id'],
                          															'codigo'                    => $rowDoc['codigo'],
                          															'impuesto'                  => $impuesto,
                          															'precio'                    => $precio,
                          															'id_items'                  => $rowDoc['id_item'],
                          															'inventariable'             => $rowDoc['inventariable'],
                                                        'cruzar_costo_activo_fijo'	=> $rowDoc['cruzar_costo_activo_fijo'],
  																											'cuenta_compra'							=> $rowDoc['cuenta_compra'],
                          															'cantidad'                  => $rowDoc['cantidad'],
                          															'id_centro_costos'          => $rowDoc['id_centro_costos'],
                          															'check_opcion_contable'     => $rowDoc['check_opcion_contable'],
                          															'cuenta_iva'                => $rowDoc['cuenta_iva']
                                                      );
		}

		$sqlItemsCuentas = "SELECT id, id_item, descripcion, codigo_puc, caracter
          							FROM contabilizacion_compra_venta
          							WHERE id_empresa = '$idEmpresa'
                        AND tipo_documento = 'FC'
                        AND id_documento = '$idFactura'
                        AND ($whereIdItemsCuentas)
          							ORDER BY id_item ASC";
		$queryItemsCuentas = mysql_query($sqlItemsCuentas,$link);

		$whereCuentaCcos = "";
 		while($rowCuentasItems = mysql_fetch_array($queryItemsCuentas)){
			$arrayCuentasItems[$rowCuentasItems['id_item']][$rowCuentasItems['descripcion']] = array(
                                                                                                'estado' => $rowCuentasItems['caracter'],
                                                                                                'cuenta'=> $rowCuentasItems['codigo_puc']
                                                                                              );

			$cuenta      = $rowCuentasItems['codigo_puc'];
			$descripcion = $rowCuentasItems['descripcion'];

			if($descripcion == 'precio' || $descripcion == 'gasto' || $descripcion == 'costo' || $descripcion == 'activo_fijo'){
        $whereCuentaCcos .= "OR cuenta = '$cuenta' ";
      }
		}

		//CONSULTA CUENTAS CCOS
		$whereCuentaCcos = substr($whereCuentaCcos, 3, -1);
		$sqlCcos   = "SELECT cuenta,centro_costo FROM puc WHERE id_empresa='$idEmpresa' AND activo=1 AND ($whereCuentaCcos)";
		$queryCcos = mysql_query($sqlCcos, $link);

		while($row = mysql_fetch_assoc($queryCcos)){
			$cuenta = $row['cuenta'];
			$cCos   = $row['centro_costo'];

			$arrayCuentaCcos[$cuenta] = $cCos;
		}

		$arrayGlobalEstado['debito']  = 0;
		$arrayGlobalEstado['credito'] = 0;

		$arrayItemEstado['debito']    = 0;
		$arrayItemEstado['credito']   = 0;

		$acumSubtotal = 0;
		$acumImpuesto = 0;

		$msjErrorCcosto = '';

		foreach($arrayInventarioFactura AS $valArrayInventario){

			$totalContabilizacionItem   = 0;
			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$idItemArray       = $valArrayInventario['id_items'];
			$descripcionCuenta = $valArrayInventario['check_opcion_contable'];							//GASTO, COSTO, ACTIVO FIJO
			$cuentaOpcional    = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['cuenta'];		//CUENTA OPCION CONTABILIZACION

			$cuentaPrecio   = ($descripcionCuenta == '')? $arrayCuentasItems[$idItemArray]['precio']['cuenta']: $cuentaOpcional;
			$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
			$cuentaImpuesto = ($valArrayInventario['cuenta_iva'] > 0)? $valArrayInventario['cuenta_iva']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

			//========================= CONTABILIZACION MERCANCIA PARA LA VENTA, COSTO, GASTO =========================//
      //*********************************************************************************************************//
      //CONDICIONAL SI TIENE CCOS
			$cCosPrecio = 0;
			if($arrayCuentaCcos[$cuentaPrecio] == 'Si'){
				if($valArrayInventario['id_centro_costos'] > 0){
          $cCosPrecio = $valArrayInventario['id_centro_costos'];
        }
				else{
          $msjErrorCcosto = '\n'.$valArrayInventario['codigo'].' '.$valArrayInventario['nombre_item'];
        }
			}

			if($descripcionCuenta != 'activo_fijo'){

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] > 0){
            $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] += $valArrayInventario['precio'];
          }
					else{
            $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] = $valArrayInventario['precio'];
          }

					$arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
					$arrayItemEstado[$estado]   += $valArrayInventario['precio'];
					$acumSubtotal               += $valArrayInventario['precio'];

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[0][$cuentaImpuesto][$estado] > 0){
              $arrayAsiento[0][$cuentaImpuesto][$estado] += $valArrayInventario['impuesto'];
            }
						else{
              $arrayAsiento[0][$cuentaImpuesto][$estado] = $valArrayInventario['impuesto'];
            }

						$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
						$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
						$acumImpuesto               += $valArrayInventario['impuesto'];
					}

					//============================== CALC CONTRA PARTIDA PRECIO =================================//
					if($contraPrecio > 0){
						$arrayAsiento[0][$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']	: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[0][$contraPrecio][$estado] > 0){
              $arrayAsiento[0][$contraPrecio][$estado] += $contraSaldo;
            }
						else{
              $arrayAsiento[0][$contraPrecio][$estado] = $contraSaldo;
            }

						$arrayGlobalEstado[$estado] += $contraSaldo;
						$arrayItemEstado[$estado]   += $contraSaldo;

						$acumCuentaClientes       = $contraPrecio;
						$acumEstadoCuentaClientes = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
					echo '<script>
    							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
    							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    						</script>';
					exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
					echo '<script>
      						alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion");
      					</script>';
					exit;
				}
				else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
					echo '<script>
    							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
    							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    						</script>';
					exit;
				}
			}
			//================================= CONTABILIZACION ACTIVO FIJO Y/O GASTOS =================================//
			//**********************************************************************************************************//
			else{
				if($cuentaPrecio == ''){
					echo '<script>
    							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' no tiene asignado una cuenta en configuracion items");
    							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    						</script>';
					exit;
				}

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$totalContabilizacionItem += $valArrayInventario['precio'];

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado                   = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
						$acumImpuesto             += $valArrayInventario['impuesto'];
						$totalContabilizacionItem += $valArrayInventario['impuesto'];
					}

					//============================ CALC CONTRA PRECIO ACTIVO FIJO ===============================//
					if($contraPrecio > 0){
						$arrayAsiento[0][$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$arrayGlobalEstado[$estado] += $totalContabilizacionItem;
						$arrayItemEstado[$estado]   += $totalContabilizacionItem;

						if($arrayAsiento[0][$contraPrecio][$estado] > 0){
              $arrayAsiento[0][$contraPrecio][$estado] += $totalContabilizacionItem;
            }
						else{
              $arrayAsiento[0][$contraPrecio][$estado] = $totalContabilizacionItem;
            }

						//ARRAY ASIENTO CONTABLE PRECIO
						$estadoPrecio                     = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
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

            //ARRAY ASIENTO CONTABLE IMPUESTO
						if($valArrayInventario['cuenta_iva'] != "" && $valArrayInventario['cruzar_costo_activo_fijo'] == "false" && $valArrayInventario['check_opcion_contable'] == "activo_fijo"){
							$estadoImpuesto = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
							$cuentaImpuesto = $valArrayInventario['cuenta_iva'];

							//VERIFICAMOS SI EL COSTO DLE ACTIVO FIJO SE CRUZA CON EL COSTO DEL IMPUESTO
							$arrayAsiento[0][$cuentaPrecio][$estadoPrecio] = $arrayAsiento[0][$cuentaPrecio][$estadoPrecio] - $valArrayInventario['impuesto'];
							$arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto] += $valArrayInventario['impuesto'];
						}

						$acumCuentaClientes       = $contraPrecio;
						$acumEstadoCuentaClientes = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
					echo '<script>
    							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
    							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    						</script>';
					exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
					echo '<script>
    							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion");
    							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    						</script>';
					exit;
				}
				else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
					echo '<script>
    							alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0dsad, por favor revise la configuracion de contabilizacion");
    							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    						</script>';
					exit;
				}
			}
			if($msjErrorCcosto != ''){
				echo '<script>
    						alert("Aviso.\nLos siguientes items no tienen centro de costo \n'.$msjErrorCcosto.'");
    						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    					</script>';
				exit;
			}
		}

    $arrayGlobalEstado['debito'] = round($arrayGlobalEstado['debito']);
		$arrayGlobalEstado['credito'] = round($arrayGlobalEstado['credito']);

    if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
			echo '<script>
    					alert("Aviso.\nHa ocurrido un problema de contabilizacion--, favor revise la configuracion de contabilizacion.\nSi el problema persiste consulte al administrador del sistema");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    				</script>';
			exit;
		}
		else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0 || $arrayGlobalEstado['debito'] == '' || $arrayGlobalEstado['credito'] == ''){
			echo '<script>
    					alert("Aviso.\nContabilizacion en saldo 0.\nSi el problema persiste consulte al administrador del sistema");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    				</script>';
			exit;
		}

		//==================== QUERY RETENCIONES =================//
		$acumRetenciones  = 0;
		$contRetencion    = 0;
		$estadoRetencion  = $acumEstadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
		$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM compras_facturas_retenciones WHERE id_factura_compra='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			if($tipoRetencion == 'ReteIva'){
        $valorBase = ($acumImpuesto >= $rowRetenciones['base'])? 0 : $rowRetenciones['base'];
      }
      else{
        $valorBase = ($acumSubtotal >= $rowRetenciones['base'])? 0 : $rowRetenciones['base'];
      }

      echo $rowRetenciones['tipo_retencion'].' - '.$rowRetenciones['base'].' <-> '.$acumCuentaClientes.'<br>';
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

			if(is_nan($arrayAsiento[0][$codigoRetencion][$estadoRetencion])){
        $arrayAsiento[0][$codigoRetencion][$estadoRetencion] = 0;
      }

			if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
				if($acumImpuesto < $valorBase){ continue; }													//VALIDACION BASE

				$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE Y RETEICA
				if($acumSubtotal<$valorBase) { continue; }													//VALIDACION BASE

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

				if(is_nan($arrayAsiento[0][$cuentaAutoretencion][$acumEstadoCuentaClientes])){
          $arrayAsiento[0][$cuentaAutoretencion][$acumEstadoCuentaClientes] = 0;
        }

        $estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
				$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[0][$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[0][$codigoRetencion][$estadoRetencion];
			}
		}

		$arrayAsiento[0][$acumCuentaClientes][$acumEstadoCuentaClientes] -= $acumRetenciones;

		$totalDebito  = 0;
		$totalCredito = 0;
		$tablaDebug   =  '<div style="float:left; width:80px;">Debito</div>
          						<div style="float:left; width:80px;">Credito</div>
          						<div style="float:left; width:80px;">PUC</div>
          						<div style="float:left; width:150px;">Id Centro Costos</div><br>';

		foreach($arrayAsiento AS $idCcos => $arrayCuenta){
			foreach($arrayCuenta AS $cuenta => $arrayCampo){
				$totalDebito  += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
				$totalCredito += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;

				if(is_nan($cuenta) || $cuenta==0){ continue; }
				$cuenta = $cuenta * 1;

				if($estadoCuentaPago == 'Credito' && $arrayCampo['type'] == 'cuentaPago'){
					$saldoGlobalNotaSinAbono += ($arrayCampo['debito'] > $arrayCampo['credito'])? $arrayCampo['debito']: $arrayCampo['credito'];
				}

				$valueInsertAsientos .= "('$idNota',
              										'NDFC',
              										'Nota Devolucion Factura de Compra',
              										'$idFactura',
              										'FC',
              										'$numero_documento_cruce',
              										'$fecha',
              										'".$arrayCampo['credito']."',
              										'".$arrayCampo['debito']."',
              										'$cuenta',
              										'$idProveedor',
              										'$idSucursal',
              										'$idEmpresa',
              										'$idCcos'),";

				$tablaDebug .= '<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
        								<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
        								<div style="float:left; width:80px;">-'.$cuenta.'</div>
        								<div style="float:left; width:150px;">-'.$idCcos.'</div><br>';
			}
		}

		$totalDebito  = round($totalDebito,$_SESSION['DECIMALESMONEDA']);
		$totalCredito = round($totalCredito,$_SESSION['DECIMALESMONEDA']);

		$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$totalCredito.'</div><br>';
		if($totalDebito != $totalCredito){
			echo '<script>
    					alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    				</script>';
			exit;
		}
    echo round($saldoGlobalNotaSinAbono,$_SESSION['DECIMALESMONEDA'])." > ".round($totalFactura,$_SESSION['DECIMALESMONEDA'])." && ".round($saldoGlobalNotaSinAbono,$_SESSION['DECIMALESMONEDA'])." > 0";
		//VALIDACION SALDO FACURA Y SALDO NOTA
		if(round($saldoGlobalNotaSinAbono,$_SESSION['DECIMALESMONEDA']) > round($totalFactura,$_SESSION['DECIMALESMONEDA']) && round($saldoGlobalNotaSinAbono,$_SESSION['DECIMALESMONEDA']) > 0){
			echo '<script>
    					alert("Aviso.\nEl saldo de la factura es insuficiente para realizar la nota!");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    				</script>';
			exit;
		}

		$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
		$sqlInsertCuentasColgaap = "INSERT INTO asientos_colgaap(
              										id_documento,
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

		// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
		contabilizacionSimultanea($idNota,'NDFC',$idSucursal,$idEmpresa,$link);
  }

  function contabilizarNotaRemisionVenta($idNota,$idBodega,$idSucursal,$idEmpresa,$idRemision,$idCliente,$link,$fecha,$numero_documento_cruce){

		// CONSULTAR LAS CUENTAS POR ITEM
		$consultaCuentasItems = "SELECT DVI.id,DVI.id_inventario, CCV.id_puc, CCV.codigo_puc AS puc, CCV.caracter AS estado, DVI.costo_inventario AS costo, DVI.cantidad, CCV.descripcion
								FROM devoluciones_venta_inventario AS DVI, contabilizacion_compra_venta AS CCV
								WHERE DVI.activo = 1
									AND DVI.id_devolucion_venta = '$idNota'
									AND DVI.id_inventario = CCV.id_item
									AND CCV.id_documento   = '$idRemision'
									AND CCV.tipo_documento = 'RV'
									AND (CCV.descripcion   = 'costo' OR CCV.descripcion='contraPartida_costo')
									GROUP BY CCV.id_item, CCV.descripcion";
		$queryCuentasItems = mysql_query($consultaCuentasItems,$link);

		$valueInsertArticulo = '';
		while ($rowCuentaItems = mysql_fetch_array($queryCuentasItems)) {
			$cuenta          = $rowCuentaItems['puc'];
			$id_item         = $rowCuentaItems['id_inventario'];
			$idDocInventario = $rowCuentaItems['id'];
			$id_puc          = $rowCuentaItems['id_puc'];
			$estado          = $rowCuentaItems['estado'];
			$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];

			$estadoAsiento = ($estado=='debito')? 'haber' : 'debe';

			if(is_nan($arrayAsiento[$cuenta][$estadoAsiento])){ $arrayAsiento[$cuenta][$estadoAsiento] = 0; }
			$arrayAsiento[$cuenta][$estadoAsiento] += $costo;

			$arrayCuenta['debito']  = 0;
			$arrayCuenta['credito'] = 0;
		}

		$contAsientos = 0;
		foreach ($arrayAsiento as $cuenta => $arrayCuenta) {
			$contAsientos++;

			$valueInsertAsientos .= "('$idNota',
										'NDRV',
										'Nota Devolucion Remision de Venta',
										'$fecha',
										'$idRemision',
										'RV',
										'$numero_documento_cruce',
										'".$arrayCuenta['debe']."',
										'".$arrayCuenta['haber']."',
										'$cuenta',
										'$idCliente',
										'$idSucursal',
										'$idEmpresa'
									),";
		}

		if($contAsientos == 0){ return; }

		//INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
		$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
		$sqlInsertColgaap    = "INSERT INTO asientos_colgaap (id_documento,
												tipo_documento,
												tipo_documento_extendido,
												fecha,
												id_documento_cruce,
												tipo_documento_cruce,
												numero_documento_cruce,
												debe,
		 										haber,
												codigo_cuenta,
												id_tercero,
												id_sucursal,
												id_empresa)
								VALUES $valueInsertAsientos";
		$queryInsertColgaap  = mysql_query($sqlInsertColgaap,$link);
		// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
		contabilizacionSimultanea($idNota,'NDRV',$idSucursal,$idEmpresa,$link);
	}

	function contabilizarNotaFacturaVentaConPlantilla($idNota,$idSucursal,$idEmpresa,$idPlantilla,$idFactura,$idCliente,$exento_iva,$link,$fecha,$numero_documento_cruce,$totalFactura){
    	$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;
		global $saldoGlobalNotaSinAbono;

    	//PLANTILLA CONFIGURACION
		$sqlPlantilla   = "SELECT codigo_puc,caracter,porcentaje,descripcion FROM contabilizacion_compra_venta WHERE id_documento='$idFactura' AND tipo_documento='FV'";
		$queryPlantilla = mysql_query($sqlPlantilla,$link);
		$tablaDebug     = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';

		$porcentajeIva        = 0;
		$contCuentasPantillas = 0;
		while($rowplantilla = mysql_fetch_array($queryPlantilla)){

			$contCuentasPantillas++;

			$idCuenta = $rowplantilla['id'];
			$cuenta   = $rowplantilla['codigo_puc'];
			$cuenta4  = substr($rowplantilla['codigo_puc'], 0, 4);
			$caracter = ($rowplantilla['caracter'] == 'debito')? 'credito' : 'debito';

			if($rowplantilla['descripcion'] == 'PRECIO' && $cuenta4 == 4135){
				$cuenta = 417501;
				$rowplantilla['codigo_puc'] = 417501;
			}
			$arrayCuentas[$cuenta] = array('puc' => $cuenta, 'caracter' => $caracter, 'descripcion' => $rowplantilla['descripcion']);

			if($rowplantilla['descripcion'] == 'IMPUESTO'){
				if($rowplantilla['porcentaje'] == ''){ echo '<script>alert("Aviso.\nLa plantilla ingresada tiene iva sin porcentaje.")</script>'; exit; }
				$porcentajeIva = $rowplantilla['porcentaje'];
			}
		}

		if($contCuentasPantillas == 0){ echo '<script>alert("Aviso.\nLa plantilla ingresada no tiene configuracion contable.")</script>'; exit; }
		else if($contCuentasPantillas == 1){ echo $sqlPlantilla.'<script>alert("Aviso.\nLa plantilla ingresada no cumple doble partida.")</script>'; exit; }

		//VENTAS FACTURA INVENTARIO
		$sqlArticulos = "SELECT cantidad,
								costo_unitario AS precio,
								costo_inventario AS costo,
								tipo_descuento,
								descuento
						FROM devoluciones_venta_inventario
						WHERE activo=1 AND id_devolucion_venta='$idNota'";
		$queryArticulos = mysql_query($sqlArticulos,$link);

		$costoAcumulado  = 0;
		$precioAcumulado = 0;
		$ivaAcumulado    = 0;
		while($rowArticulo = mysql_fetch_array($queryArticulos)){

			$costo  = $rowArticulo['costo']* $rowArticulo['cantidad']; 														//CALCULO DEL COSTO
			$precio = $rowArticulo['precio']* $rowArticulo['cantidad'];														//CALCULO DEL PRECIO

			if($rowArticulo['descuento'] > 0){
				$precio = ($rowArticulo['tipo_descuento'] == 'porcentaje')? $precio-(($rowArticulo['descuento']*$precio)/100) : $precio-$rowArticulo['descuento'];
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

			if($tipoRetencion == "ReteIva"){ 																			//CALCULO RETEIVA
				if ($exento_iva!='Si' || $ivaAcumulado<$valorBase) { continue; }										//EXCENTO IVA O NO CUMPLE BASE

				if($porcentajeIva == 0){ echo '<script>alert("Aviso.\nLa plantilla ingresada tiene no cuenta con iva. para continuar quite la RETENCION al iva (Reteiva) o agregue el iva a la plantilla.")</script>'; exit; }
				$acumRetenciones += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$codigoRetencion] += ROUND($ivaAcumulado * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																										//CALCULO RETE, RETECREE Y RETEICA
				if ($precioAcumulado<$valorBase) { continue; }

				$acumRetenciones += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$codigoRetencion] += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){ 																		//DEVOLUCION SALDO AUTORETENCION NO RESTA
				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){ echo '<script>alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.")</script>'; exit; }
				$contAutoRetencion++;
				$arrayAutoRetencion[$contAutoRetencion] = $cuentaAutoretencion;

				$acumRetenciones -= ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
				$arrayRetenciones[$cuentaAutoretencion] += ROUND($precioAcumulado*$valorRetencion/100, $decimalesMoneda);
			}
		}

		$cuentaContraPartida = $precioAcumulado+$ivaAcumulado-$acumRetenciones;
		$valueInsertAsientos = '';

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
				else{ $saldo = $precioAcumulado; }														//CONTRAPARTIDA-RETENCIONES

				if($configCuenta4 == 1305){ $saldoGlobalNotaSinAbono += $saldo; }						//ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO

				$totalDebito  = ($valArrayCuenta['caracter'] == 'debito')? $saldo : 0;
				$totalCredito = ($valArrayCuenta['caracter'] == 'credito')? $saldo : 0;

				$globalDebito  += $totalDebito;
				$globalCredito += $totalCredito;

				$valueInsertAsientos .= "('$idNota',
											'NDFV',
											'$idFactura',
											'FV',
											'$numero_documento_cruce',
											'Nota Devolucion Factura de Venta',
											'$fecha',
											'$totalDebito',
											'$totalCredito',
											'".$valArrayCuenta['puc']."',
											'$idCliente',
											'$idSucursal',
											'$idEmpresa'),";

				$tablaDebug  .='<div style="float:left; width:80px;">'.$totalDebito.'</div><div style="float:left; width:80px;">'.$totalCredito.'</div><div style="float:left; width:80px;">'.$valArrayCuenta['puc'].'</div><br>';
			}

			foreach ($arrayRetenciones AS $cuentaPuc => $valArrayRetencion){

				$totalDebito  = 0;
				$totalCredito = $valArrayRetencion;

				for ($i=1; $i <= $contAutoRetencion; $i++) { if($arrayAutoRetencion[$i] == $cuentaPuc){ $totalCredito = 0; $totalDebito = $valArrayRetencion; break; } }

				$globalDebito  += $totalDebito;
				$globalCredito += $totalCredito;

				$valueInsertAsientos .= "('$idNota',
											'NDFV',
											'$idFactura',
											'FV',
											'$numero_documento_cruce',
											'Nota Devolucion Factura de Venta',
											'$fecha',
											'$totalDebito',
											'$totalCredito',
											'$cuentaPuc',
											'$idCliente',
											'$idSucursal',
											'$idEmpresa'),";
				$tablaDebug  .='<div style="float:left; width:80px;">'.$totalDebito.'</div><div style="float:left; width:80px;">'.$totalCredito.'</div><div style="float:left; width:80px;">'.$cuentaPuc.'</div><br>';
			}

			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">'.$globalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$globalCredito.'</div><br>';

			if($globalDebito != $globalCredito){ echo '<script>alert("Aviso.\nLa plantilla ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }

			//VALIDACION SALDO FACURA Y SALDO NOTA
			if($saldoGlobalNotaSinAbono > $totalFactura && $saldoGlobalNotaSinAbono > 0){ echo '<script>alert("Aviso.\nEl saldo de la factura es insuficiente para realizar la nota!")</script>'; exit; }
			else if($saldoGlobalNotaSinAbono > 0){
				$updateSaldoFactura = "UPDATE ventas_facturas SET total_factura_sin_abono=(total_factura_sin_abono - $saldoGlobalNotaSinAbono) WHERE activo=1 AND id_empresa='$idEmpresa' AND id='$idFactura'";
				$querySaldoFactura  = mysql_query($updateSaldoFactura,$link);

				if(!$querySaldoFactura){ echo '<script>alert("Error No 2.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema")</script>'; exit; }
			}


			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sqlContabilizar     = "INSERT INTO asientos_colgaap (
										id_documento,
										tipo_documento,
										id_documento_cruce,
										tipo_documento_cruce,
										numero_documento_cruce,
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

			if(!$queryContabilizar){ echo '<script>alert("Error No 3.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema")</script>'; exit; }
		}
		else{ echo '<script>alert("Aviso.\nLa plantilla ingresada no tiene configuracion contable.")</script>'; exit; }
  }

  function contabilizarNotaFacturaVentaSinPlantilla($arrayCuentaPago,$idCcos,$idNota,$idBodega,$idSucursal,$idEmpresa,$idFactura,$idCliente,$exento_iva,$link,$fecha,$numero_documento_cruce,$totalFactura){
  	
    	global $saldoGlobalNotaSinAbono;
    	$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;
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

		$sqlDoc = "SELECT D.id,
						D.id_inventario AS id_item,
						D.codigo,
						D.cantidad,
						D.costo_unitario AS precio,
						D.costo_inventario AS costo,
						D.descuento,
						D.tipo_descuento,
						D.id_impuesto,
						D.valor_impuesto,
						D.inventariable,
						if(I.cuenta_venta_devolucion > 0, I.cuenta_venta_devolucion, I.cuenta_venta) AS cuenta_iva
					FROM devoluciones_venta_inventario AS D LEFT JOIN impuestos AS I ON(
							D.id_impuesto = I.id
							AND I.activo=1
						)
					WHERE D.id_devolucion_venta='$idNota' AND D.activo=1";
		$queryDoc = mysql_query($sqlDoc,$link);

		while($rowDoc = mysql_fetch_array($queryDoc)){

			//CALCULO DEL PRECIO
			$precio = $rowDoc['precio'] * $rowDoc['cantidad'];
			$costo  = $rowDoc['costo'] * $rowDoc['cantidad'];
			if($rowDoc['descuento'] > 0){
				$precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio-ROUND(($rowDoc['descuento']*$precio)/100,$decimalesMoneda) : $precio-$rowDoc['descuento'];
			}

			$impuesto = 0;
    	//VERIFICAMOS SI EL TERCERO ES EXENTO DE IVA
    	if($exento_iva == "Si"){
      	$impuesto = 0;
    	}else{
	      if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){ 
          // $impuesto = ROUND($precio * $rowDoc['valor_impuesto'] / 100, $decimalesMoneda); 

          // 2019-10-24 Al igual que en la interfaz grafica, solo se redondean la suma final de los impuestos
          $impuesto = $precio * $rowDoc['valor_impuesto'] / 100;  
        }
    	}

			$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
			$whereIdItemsCuentas .= 'id_item = '.$rowDoc['id_item'];

			$arrayInventarioFactura[$rowDoc['id']]  = array('id_factura_inventario' =>$rowDoc['id'],
															'precio'                =>$precio,
															'codigo'                =>$rowDoc['codigo'],
															'impuesto'              =>$impuesto,
															'costo'                 =>$costo,
															'id_items'              =>$rowDoc['id_item'],
															'inventariable'         =>$rowDoc['inventariable'],
															'cuenta_iva'            =>$rowDoc['cuenta_iva']);
		}

		$sqlItemsCuentas = "SELECT id, id_item, descripcion, codigo_puc, caracter
							FROM contabilizacion_compra_venta
							WHERE id_empresa='$idEmpresa' AND tipo_documento='FV' AND id_documento='$idFactura' AND ($whereIdItemsCuentas)
							ORDER BY id_item ASC";

		$queryItemsCuentas = mysql_query($sqlItemsCuentas,$link);

		while ($rowCuentasItems = mysql_fetch_array($queryItemsCuentas)) {
      //para plataforma colombia siempre devolvera sobre estas cuentas
      if($idEmpresa == 1 || $idEmpresa == 47){
        if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['codigo_puc'] = $cuentaPago; }
        else if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['codigo_puc'] = 24080201; }

        $cuenta4 = substr($rowCuentasItems['codigo_puc'], 0, 1);
        if($cuenta4 == 4){ $rowCuentasItems['codigo_puc'] = 41750101; }  
      } else{
        if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['codigo_puc'] = $cuentaPago; }
      }

			$arrayCuentasItems[$rowCuentasItems['id_item']][$rowCuentasItems['descripcion']]= array('estado' => $rowCuentasItems['caracter'], 'cuenta'=> $rowCuentasItems['codigo_puc']);
		}

		$arrayGlobalEstado['debito']  = 0;
		$arrayGlobalEstado['credito'] = 0;

		$arrayItemEstado['debito']    = 0;
		$arrayItemEstado['credito']   = 0;

		$acumSubtotal = 0;
		$acumImpuesto = 0;

		foreach ($arrayInventarioFactura AS $valArrayInventario) {

			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$idItemArray    = $valArrayInventario['id_items'];

      //para plataforma colombia siempre devolvera sobre estas cuentas
      if($idEmpresa == 1 || $idEmpresa == 47){
			  $cuentaPrecio   = $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
      } 
      else{
        $cuentaPrecio   = $arrayCuentasItems[$idItemArray]['devprecio']['cuenta'];
      }
			
      $contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
			$cuentaImpuesto = ($valArrayInventario['cuenta_iva'] > 0)? $valArrayInventario['cuenta_iva']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

			$cuentaCosto = $arrayCuentasItems[$idItemArray]['costo']['cuenta'];
			$contraCosto = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['cuenta'];

			//======================================= CALC PRECIO =====================================//
			if($cuentaPrecio > 0){
				$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
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
				if($cuentaImpuesto > 0){
					$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
					else{ $arrayAsiento[$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }
					$arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
					$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
					$acumImpuesto               += $valArrayInventario['impuesto'];
				}

				//============================== CALC CONTRA PARTIDA PRECIO =================================//
				if($contraPrecio > 0){
					$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

					$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
									: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
					else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
					$arrayAsiento[$contraPrecio]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $contraSaldo;
					$arrayItemEstado[$estado]   += $contraSaldo;

					$acumCuentaClientes 		 = $contraPrecio;
					$acumEstadoCuentaClientes	 = $estado;
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
			if($cuentaCosto > 0 && $contraCosto > 0){

				$estado = $arrayCuentasItems[$idItemArray]['costo']['estado'];

				//ARRAY ASIENTO CONTABLE
				if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $valArrayInventario['costo']; }
				else{ $arrayAsiento[$cuentaCosto][$estado] = $valArrayInventario['costo']; }
				$arrayAsiento[$cuentaCosto]['idCcos'] = 0;

				$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
				$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

				$estado = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['estado'];

				//ARRAY ASIENTO CONTABLE
				if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $valArrayInventario['costo']; }
				else{ $arrayAsiento[$contraCosto][$estado] = $valArrayInventario['costo']; }
				$arrayAsiento[$contraCosto]['idCcos'] = $idCcos;

				$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
				$arrayItemEstado[$estado]   += $valArrayInventario['costo'];
			}
			else if($valArrayInventario['inventariable'] == 'true'){
				echo '<script>
						alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado el manejo del costo en la contabilizacion");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
				echo '<script>
						alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			// else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito']==0){
			// 	echo '<script>
			// 			alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
			// 			document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			// 		</script>';
			// 	exit;
			// }
		}

		if(round($arrayGlobalEstado['debito'],$_SESSION['DECIMALESMONEDA']) != round($arrayGlobalEstado['credito'],$_SESSION['DECIMALESMONEDA'])){
			echo '<script>
    					alert("Aviso.\nHa ocurrido un problema de contabilizacion**, favor revise la configuracion de contabilizacion.\nSi el problema persiste consulte al administrador del sistema");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    				</script>';
			exit;
		}

		//==================== QUERY RETENCIONES =================//
    	/**********************************************************/
		$acumRetenciones  = 0;
		$contRetencion    = 0;
		$estadoRetencion  = $acumEstadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
		$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM ventas_facturas_retenciones WHERE id_factura_venta='$idFactura' AND activo=1";
		$queryRetenciones = mysql_query($sqlRetenciones,$link);

		while($rowRetenciones = mysql_fetch_array($queryRetenciones)){
			$valorBase           = $rowRetenciones['base'];
			$valorRetencion      = $rowRetenciones['valor'];
			$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
			$tipoRetencion       = $rowRetenciones['tipo_retencion'];
			$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

			if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

			if($tipoRetencion == "ReteIva"){ 																		//CALCULO RETEIVA
				if ($exento_iva=='Si' || $acumImpuesto<$valorBase) { continue; }									//EXCENTO RETEIVA O NO CUMPLE BASE

				$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
			}
			else{ 																									//CALCULO RETE, RETECREE Y RETEICA
				if ($acumSubtotal<$valorBase) { continue; }

				$acumRetenciones += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
			}

			if($tipoRetencion == "AutoRetencion"){ 																		//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES
				if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
					echo '<script>
							alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				if(is_nan($arrayAsiento[$cuentaAutoretencion][$acumEstadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$acumEstadoCuentaClientes] = 0; }
				$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
				$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
				$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
			}
		}

		$arrayAsiento[$acumCuentaClientes][$acumEstadoCuentaClientes] -= $acumRetenciones;

		$totalDebito  = 0;
		$totalCredito = 0;

		$tablaDebug = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';

		foreach ($arrayAsiento AS $cuenta => $arrayCampo) {

			if($arrayCampo['debito'] == 0 && $arrayCampo['credito'] == 0){ continue; }

			$arrayCampo['debito'] = round($arrayCampo['debito'],$_SESSION['DECIMALESMONEDA']);
			$arrayCampo['credito'] = round($arrayCampo['credito'],$_SESSION['DECIMALESMONEDA']);

			$totalDebito  += $arrayCampo['credito'];
			$totalCredito += $arrayCampo['debito'];

			if($estadoCuentaPago == 'Credito' && $arrayCampo['type']=='cuentaPago'){ $saldoGlobalNotaSinAbono += ($totalDebito > $totalCredito)? $totalDebito: $totalCredito; } //ACUMULADOR VARIABLE GLOBAL TOTAL NOTA SIN ABONO

			$valueInsertAsientos .= "('$idNota',
									'NDFV',
									'Nota Devolucion Factura de Venta',
									'$fecha',
									'$idFactura',
									'FV',
									'$numero_documento_cruce',
									'".$arrayCampo['credito']."',
									'".$arrayCampo['debito']."',
									'$cuenta',
									'$idCliente',
									'".$arrayCampo['idCcos']."',
									'$idSucursal',
									'$idEmpresa'),";

			$tablaDebug .= '<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div><div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div><div style="float:left; width:80px;">-'.$cuenta.'</div><br>';
		}

		$saldoGlobalNotaSinAbono = ROUND($saldoGlobalNotaSinAbono, $decimalesMoneda);

		$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$totalCredito.'</div><br>';

		// echo $tablaDebug; exit;

		$totalDebito  = ROUND($totalDebito,$decimalesMoneda);
		$totalCredito = ROUND($totalCredito,$decimalesMoneda);
		if($totalDebito != $totalCredito){
			echo '<script>
					alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//VALIDACION SALDO FACURA Y SALDO NOTA
		if($saldoGlobalNotaSinAbono > $totalFactura && $saldoGlobalNotaSinAbono > 0){
			echo '<script>
					alert("Aviso.\nEl saldo de la factura es insuficiente para realizar la nota!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>'.$saldoGlobalNotaSinAbono.'-'.$totalFactura;
			exit;
		}
		else if($saldoGlobalNotaSinAbono > 0){
			$updateSaldoFactura = "UPDATE ventas_facturas SET total_factura_sin_abono=(total_factura_sin_abono - $saldoGlobalNotaSinAbono) WHERE activo=1 AND id_empresa='$idEmpresa' AND id='$idFactura'";
			$querySaldoFactura  = mysql_query($updateSaldoFactura,$link);

			if(!$querySaldoFactura){
				echo '<script>
						alert("Error No 2.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
		}
    
		$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
		$sqlInsertCuentasColgaap = "INSERT INTO asientos_colgaap (
										id_documento,
										tipo_documento,
										tipo_documento_extendido,
										fecha,
										id_documento_cruce,
										tipo_documento_cruce,
										numero_documento_cruce,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_centro_costos,
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
		$queryInsertCuentasColgaap = mysql_query($sqlInsertCuentasColgaap,$link);

		if(!$queryInsertCuentasColgaap){
			echo '<script>
					alert("Aviso No 3.\nSin conexion con la bade de datos, intente de nuevo si el problema persiste consulte al administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
		contabilizacionSimultanea($idNota,'NDFV',$idSucursal,$idEmpresa,$link);
  }
?>
