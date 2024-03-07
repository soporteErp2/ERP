<?php
	//===================// CONFIGURACION DE INFORMACION //===================//
	//************************************************************************//
	$tablaPrincipal = 'ventas_facturas';

	$arrayWs['documento']['consecutivo']      = $consecutivo;
	$arrayWs['documento']['codigo_tipo_nota'] = 1;
	$arrayWs['documento']['fecha_documento']  = $fecha_metodo;
	$arrayWs['documento']['nit_tercero']      = $arrayWs['nit_empresa'];

	//=======================// CONSULTA IMPUESTOS //=======================//
	//**********************************************************************//
	$sqlImpuestos = "SELECT COUNT(id) AS contImpuesto,
							id,
							valor,
							cuenta_venta AS cuenta_iva_colgaap,
							cuenta_venta_niif AS cuenta_iva_niif
					FROM impuestos
					WHERE id_empresa='$arrayWs[id_empresa]'
						AND activo=1
						AND venta='Si'
					GROUP BY id";
	$queryImpuestos = mysql_query($sqlImpuestos,$link);
	while ($rowImpuesto= mysql_fetch_assoc($queryImpuestos)) {
		$valor_iva = $rowImpuesto['valor'] * 1;
		$arrayImpuesto["$valor_iva"] = $rowImpuesto;
	}

	//===================// CONSULTA CUENTA DE PAGO //===================//
	//*******************************************************************//
	$sqlCuentaPago   = "SELECT COUNT(id) AS contCuentaPago, id, cuenta, id_cuenta, cuenta_niif, estado, nombre
						FROM configuracion_cuentas_pago
						WHERE activo=1 AND id_empresa='$arrayWs[id_empresa]' AND tipo='Venta'
						GROUP BY id";
	$queryCuentaPago = mysql_query($sqlCuentaPago,$link);
	while ($rowCuentaPago = mysql_fetch_assoc($queryCuentaPago)) {
		$cuenta = $rowCuentaPago['cuenta'];
		$arrayCuentaPago[$cuenta] = array('idConfigCuentaPago'=>$rowCuentaPago['id'],
											'cuentaPago'=>$rowCuentaPago['cuenta'],
											'idCuentaPago'=>$rowCuentaPago['id_cuenta'],
											'cuentaPagoNiif'=>$rowCuentaPago['cuenta_niif'],
											'contCuentaPago'=>$rowCuentaPago['contCuentaPago'],
											'estadoPago'=>$rowCuentaPago['estado'],
											'nombre'=>$rowCuentaPago['nombre']
										);
	}
	// print_r($responseWs);
	foreach ($responseWs as $indice => $arrayCuentas) {
		$numeroFactura = $arrayCuentas['N_Factura'];
		$valor_iva     = $arrayCuentas['procentage_iva'] * 1;

		$arrayCuentas['cuenta_iva_niif']    = 0;
		$arrayCuentas['cuenta_iva_colgaap'] = 0;
		$arrayCuentas['id_cuenta_pago']     = 0;
		$arrayCuentas['cuenta_niif']        = 0;
		$arrayCuentas['estado_pago']        = '';
		$arrayCuentas['total_factura']      = 0;
		$arrayCuentas['total_factura_sin_abono'] = 0;

		if($valor_iva > 0 && $arrayImpuesto["$valor_iva"]["id"] > 0 && $arrayCuentas['tipo']!='F'){
			$arrayCuentas['cuenta_iva_niif']    = $arrayImpuesto["$valor_iva"]["cuenta_iva_niif"];
			$arrayCuentas['cuenta_iva_colgaap'] = $arrayImpuesto["$valor_iva"]["cuenta_iva_colgaap"];
		}
		else if($valor_iva > 0 && $arrayCuentas['tipo']!='F'){
			$facturasFail[$numeroFactura] = true;
			response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'No se han configurado los siguientes impuestos '.$valor_iva.'% en la Factura #'.$numeroFactura),false); continue;
		}

		//=====================================// VALIDAR LA CUENTA DE PAGO //=====================================//
		//*********************************************************************************************************//
		if($arrayCuentas['tipo']=='F'){

			$arrayNit = explode('-', str_replace('.', '', $arrayCuentas['Identificacion']));
			$arrayCuentas['Identificacion'] = $arrayNit[0];

			if(!isset($arrayCuentaPago[$arrayCuentas['Cuenta']]['idConfigCuentaPago'])){
				$facturasFail[$numeroFactura] = true;
				response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'La cuenta de pago '.$arrayCuentas['Cuenta'].' no esta configurada en la Factura #'.$numeroFactura),false); continue;
			}

			$arrayCuentas['id_config_cuenta_pago'] = $arrayCuentaPago[$arrayCuentas['Cuenta']]['idConfigCuentaPago'];
			$arrayCuentas['id_cuenta_pago']        = $arrayCuentaPago[$arrayCuentas['Cuenta']]['idCuentaPago'];
			$arrayCuentas['cuenta_niif_pago']      = $arrayCuentaPago[$arrayCuentas['Cuenta']]['cuentaPagoNiif'];
			$arrayCuentas['estado_pago']           = $arrayCuentaPago[$arrayCuentas['Cuenta']]['estadoPago'];
			$arrayCuentas['nombre_pago']           = $arrayCuentaPago[$arrayCuentas['Cuenta']]['nombre'];

			if($estadoPago == 'Credito'){ $arrayCuentas['total_factura_sin_abono'] = $arrayCuentas['valor']; }

			$arrayFacturas[$numeroFactura]['head'] = $arrayCuentas;
		}

		// AGRUPAR ITEMS PARA VALIDAR SI EXISTEN
		if ($arrayCuentas['cargo']=='1') {
			$whereItems .= ($whereItems=='')? " codigo=$arrayCuentas[codigo] " : " OR codigo=$arrayCuentas[codigo] ";
			$arrayItems[$arrayCuentas['codigo']]['nombre']   = $arrayCuentas['Descripcion'];
			$arrayItems[$arrayCuentas['codigo']]['impuesto'] = $arrayCuentas['porcentaje_impuesto'];
		}
		// AGRUPAR IMPUESTOS
		else{
			// SI SON RETENCIONES
			if ($arrayCuentas['tipo']=='R') {
				$arrayRetencionesWs[$arrayCuentas['abr']][$arrayCuentas['porcentaje_impuesto']]['nombre']=$arrayCuentas['Descripcion'];
				$whereRetenciones .= ($whereRetenciones=='')? " ( tipo_retencion='$arrayCuentas[abr]' AND valor=$arrayCuentas[porcentaje_impuesto] ) " :
															  " OR ( tipo_retencion='$arrayCuentas[abr]' AND valor=$arrayCuentas[porcentaje_impuesto] ) ";
			}
			// SI ES IVA
			else{
				//
			}
		}

		$arrayFacturas[$numeroFactura]['cuentas'][] = $arrayCuentas;
	}

	// VALIDAR QUE LOS ITEMS EXISTAN
	$sql="SELECT
				id,
				codigo,
				nombre_equipo,
				(SELECT valor FROM impuestos WHERE activo=1 AND id=id_impuesto) AS impuesto
			FROM items WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND ($whereItems) ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$whereIdItems .= ($whereIdItems=="")? " id_items=$row[id] " : " OR id_items=$row[id] " ;
		$arrayItems[$row['codigo']]['id'] = $row['id'];
		$arrayItems[$row['codigo']]['impuesto_bd'] = $row['impuesto'];
	}

	// VERIFICAR LAS CUENTAS DE LOS ITEMS INSERTADOS
	$sql="SELECT id_items,descripcion,id_puc FROM items_cuentas WHERE activo=1 AND ($whereIdItems) ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayCuentas[$row['id_items']][$row['descripcion']] = $row['id_puc'];
	}

	// VERIFICAR LAS CUENTAS NIIF DE LOS ITEMS INSERTADOS
	$sql="SELECT id_items,descripcion,id_puc FROM items_cuentas_niif WHERE activo=1 AND ($whereIdItems) ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayCuentasNiif[$row['id_items']][$row['descripcion']] = $row['id_puc'];
	}

	// VERIFICAR LA EXISTENCIA DE LAS RETENCIONES
	$sql="SELECT id,retencion,tipo_retencion,ROUND(valor,2) AS valor,base,cuenta,cuenta_niif FROM retenciones WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND modulo='Venta' AND ($whereRetenciones) ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayRetenciones[$row['tipo_retencion']][$row['valor']] = array(
																'id'          => $row['id'],
																'retencion'   => $row['retencion'],
																'base'        => $row['base'],
																'cuenta'      => $row['cuenta'],
																'cuenta_niif' => $row['cuenta_niif'],
															 );
	}

	// RECORRER PARA VALIDAR LOS ITEMS
	foreach ($arrayItems as $codigo => $arrayResult) {
		if ($arrayResult['id']=='' || $arrayResult['id']==0) {
			$msjError .= "<br>* El item $codigo - $arrayResult[nombre] no existe en el sistema. ";
		}
		else{
			if($arrayResult['impuesto'] <> $arrayResult['impuesto_bd'] ){
			$msjError .= "<br>* El item $codigo - $arrayResult[nombre] tiene un impuesto diferente en SIHO ($arrayResult[impuesto]) y ERP ($arrayResult[impuesto_bd]) ";
			}

			if ($arrayCuentas[$arrayResult['id']]['precio']=='' || $arrayCuentas[$arrayResult['id']]['precio']==0 || $arrayCuentas[$arrayResult['id']]['devprecio']=='' || $arrayCuentas[$arrayResult['id']]['devprecio']==0) {
				$msjError .= "<br>* El item $codigo - $arrayResult[nombre] no tiene las cuentas colgaap configuradas ";
			}
			if ($arrayCuentasNiif[$arrayResult['id']]['precio']=='' || $arrayCuentasNiif[$arrayResult['id']]['precio']==0 ) {
				$msjError .= "<br>* El item $codigo - $arrayResult[nombre] no tiene las cuentas niif configuradas ";
			}
		}
	}

	// RECORRER PARA VALIDAR LAS RETENCIONES
	foreach ($arrayRetencionesWs as $tipo_retencion => $arrayRetencionesResult) {
		foreach ($arrayRetencionesResult as $valor => $arrayResult) {
			if ($arrayRetenciones[$tipo_retencion][$valor]['id']=='' || $arrayRetenciones[$tipo_retencion][$valor]['id']==0) {
				$msjError .= "<br>* La retencion <i>$arrayResult[nombre] ( $valor %) no esta creada en venta</i> . ";
			}
		}
	}

	// MOSTRAR MENSAJES DE ERROR EN LA VALIDACION
	if ($msjError<>'') {
		response_error(array('estado' => 'error','msj'=>$msjError),false);
		exit;
	}

	// echo json_encode($arrayFacturas); exit;
	print_r($arrayItems);
	exit;

	foreach ($arrayFacturas as $numeroFactura => $arrayFactura) {
		if($facturasFail[$numeroFactura]){ continue; }

		$arrayWs['documento']['cuentas'] = array();

		$headFactura    = $arrayFactura['head'];
		$cuentasFactura = $arrayFactura['cuentas'];

		$cuentaHead = $headFactura['Cuenta'];
		$nitTercero = $headFactura['Identificacion'];
		// $nitTercero = 14469098;

		//CONFIGURACION DE LA CUENTA DE PAGO
		$idConfigCuentaPago = $headFactura['id_config_cuenta_pago'];
		$cuentaNiifPago     = $headFactura['cuenta_niif_pago'];
		$idCuentaPago       = $headFactura['id_cuenta_pago'];
		$tipo_pago          = $headFactura['estado_pago'];
		$nombre_pago        = $headFactura['nombre_pago'];

		$fecha_inicio      = $headFactura['inv_date_audit'];
		$fecha_vencimiento = $headFactura['inv_date_vencimiento'];

		$total_factura           = $headFactura['total_factura'];
		$total_factura_sin_abono = $headFactura['total_factura_sin_abono'];

		$validaDebito  = 0;
		$validaCredito = 0;

		$saldo_pago_debito    = 0;
		$saldo_pago_credito   = 0;
		$saldoCredito_factura = 0;

		$j = 0;
		foreach ($cuentasFactura as $i => $arrayCuenta) {
			if($arrayCuenta['Debito'] == 0 && $arrayCuenta['Credito'] == 0){ continue; }		// VALIDACION

			$cuenta_pago = false;
			$saldo       = ($arrayCuenta['Debito'] - $arrayCuenta['Credito']);
			$absSaldo    = ABS($saldo);

			$debito  = ($saldo > 0)? $absSaldo: 0;
			$credito = ($saldo < 0)? $absSaldo: 0;

			if($cuentaHead == $arrayCuenta['Cuenta']){
				$cuenta_pago = true;
				$saldo_pago_debito  += $debito;
				$saldo_pago_credito += $credito;
			}

			$validaDebito  += $debito;
			$validaCredito += $credito;

			$j++;
			$arrayWs['documento']['cuentas'][$j] = array('cuenta_niif'=> $arrayCuenta['cuenta_niif'],
															'cuenta_colgaap' => $arrayCuenta['Cuenta'],
															'centro_costo'   => $arrayCuenta['Centro_Costo'],
															'concepto'       => $arrayCuenta['Descripcion'],
															'debito'         => $debito,
															'credito'        => $credito,
															'cuenta_pago'    => $cuenta_pago
														);
		}
		$validaDebito  = ROUND($validaDebito, 2);
		$validaCredito = ROUND($validaCredito, 2);
		if($validaDebito != $validaCredito) { response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'El saldo Debito $'.$validaDebito.' y Credito $'.$validaCredito.' son diferentes! en la Factura de venta #'.$numeroFactura),false); continue; }
		else if($validaCredito==0) { response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'El saldo Debito/Credito es igual a cero! en la Factura de venta #'.$numeroFactura),false); continue; }
		$arrayDoc = $arrayWs['documento'];

		//=====================================// VALIDACION FACTURA UNICA //=====================================//
		//********************************************************************************************************//
		$sqlFactura   = "SELECT COUNT(id) AS contFactura FROM ventas_facturas WHERE activo=1 AND id_empresa='$arrayWs[id_empresa]' AND numero_factura = '$numeroFactura' AND activo=1 LIMIT 0,1";
		$queryFactura = mysql_query($sqlFactura,$link);
		$contFactura  = mysql_result($queryFactura, 0, 'contFactura');
		if($contFactura > 0){ response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'La factura de venta # '.$numeroFactura.' ya ha sido ingresada!'),false); continue; }

		//TERCERO PRINCIPAL
		$sql   = "SELECT COUNT(id) AS cont,id,codigo,tipo_identificacion,numero_identificacion,nombre_comercial
					FROM terceros
					WHERE activo=1
						AND id_empresa='$arrayWs[id_empresa]'
						AND numero_identificacion='$nitTercero'";
		$query = mysql_query($sql,$link);

		$cont       = mysql_result($query,0,'cont');
		$codigo     = mysql_result($query,0,'codigo');
		$id_tercero = mysql_result($query,0,'id');
		$tipo_nit   = mysql_result($query,0,'tipo_identificacion');
		$nit        = mysql_result($query,0,'numero_identificacion');
		$tercero    = mysql_result($query,0,'nombre_comercial');

		if($cont == 0){ response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'No existe el tercero numero de identificacion #'.$nitTercero.' de la Factura # '.$numeroFactura.' en la empresa'),false); continue; }

		$total_factura = $saldo_pago_debito - $saldo_pago_credito;
		$total_factura_sin_abono = 0;

		// SE COMENTO POR FACTURAS QUE MOSTRABAN UNA DEVOLUCION AL CLIENTE (EN PRUEBA)
		// SI SE COMENTA ESTA VALIDACION, PERMITE PASAR LAS FACTURAS CON SALDO NEGATIVO EN LA CUENTA DE BANCO-CAJA, PERO SI HAY SALDO DESCUADRADO EN REFERENCIA A LA CXC NO VALIDA
		// if($total_factura < 0){ response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'La Factura # '.$numeroFactura.' Tiene un saldo negativo'),false); continue; }
		if($tipo_pago == 'Credito'){ $total_factura_sin_abono = $total_factura; };

		//INSERTAR LA CABECERA DEL DOCUMENTO
		$randomFv = responseUnicoRanomico();
	    $sql   = "INSERT INTO ventas_facturas
	    				(id_empresa,
	    				id_sucursal,
						random,
						numero_factura,
						numero_factura_completo,
						total_factura,
						total_factura_sin_abono,
	    				id_configuracion_cuenta_pago,
	    				configuracion_cuenta_pago,
	    				id_cuenta_pago,
	    				cuenta_pago,
	    				cuenta_pago_niif,
	    				fecha_contabilizado,
	    				fecha_creacion,
	    				fecha_inicio,
	    				fecha_vencimiento,
	    				id_cliente,
						cod_cliente,
						nit,
						cliente,
	    				id_usuario,
	    				documento_usuario,
	    				usuario,
	    				estado,
	    				tipo)
	            VALUES('$arrayWs[id_empresa]',
	                	'$id_sucursal',
	                	'$randomFv',
	                	'$numeroFactura',
						'$numeroFactura',
						$total_factura,
						$total_factura_sin_abono,
	                	'$idConfigCuentaPago',
	                	'$nombre_pago',
	                	'$idCuentaPago',
						'$cuentaHead',
						'$cuentaNiifPago',
	                	NOW(),
	                	NOW(),
	                	'$fecha_inicio',
	    				'$fecha_vencimiento',
	                	'$id_tercero',
	                	'$codigo',
	                	'$nit',
	                	'$tercero',
	                	'$arrayWs[id_empleado]',
	                	'$arrayWs[documento_empleado]',
	                	'$arrayWs[nombre_empleado]',
	                	1,
	                	'Ws')";
	    $query = mysql_query($sql,$link);
	    if (!$query) { response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=>'No se inserto el documento')); }

		$sqlSelectId  = "SELECT id FROM ventas_facturas WHERE random='$randomFv' LIMIT 0,1";
		$id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

	    // INSERTAR LAS CUENTAS DEL DOCUMENTO
	    $acumuladoDebito  = 0;
		$acumuladoCredito = 0;

		$valueInsertBody           = '';
		$valueInsertCuentasNiif    = '';
		$valueInsertCuentasColgaap = '';
	    foreach ($arrayDoc['cuentas'] as $key => $arrayCuenta) {

	    	$debito  = $arrayCuenta['debito'];
			$credito = $arrayCuenta['credito'];
			$acumuladoDebito  += $arrayCuenta['debito'];
			$acumuladoCredito += $arrayCuenta['credito'];

			// CONSULTAR Y VALIDAR EL TERCERO
			if($arrayCuenta['nit_tercero_cuenta'] > 0 && 1==2){
				$sqlTerceros       = "SELECT COUNT(id) AS cont, id FROM terceros WHERE activo=1 AND id_empresa='$arrayWs[id_empresa]' AND numero_identificacion='$arrayCuenta[nit_tercero_cuenta]'";
				$queryTerceros     = mysql_query($sqlTerceros,$link);
				$cont_tercero_nota = mysql_result($queryTerceros,0,'cont');
				$id_tercero_cuenta = mysql_result($queryTerceros,0,'id');

				if ($cont_tercero_nota == 0) {
					rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El tercero relacionado a la cuenta $arrayCuenta[cuenta_colgaap] no existe en el sistema");
					continue;
				}
			}
			else{ $id_tercero_cuenta = $id_tercero; }

			// CONSULTAR EL ID DE LA CUENTA COLGAAP
			$sql   = "SELECT C.id AS id_colgaap,
							C.centro_costo,
							C.cuenta_niif,
							N.id AS id_niif
						FROM puc AS C LEFT JOIN puc_niif AS N ON(
								N.activo = 1
								AND N.id_empresa= C.id_empresa
								AND C.cuenta_niif = N.cuenta
							)
						WHERE C.activo=1
							AND C.id_empresa=$arrayWs[id_empresa]
							AND C.cuenta='$arrayCuenta[cuenta_colgaap]'
						LIMIT 0,1";
			$query = mysql_query($sql,$link);

			$idPuc        = mysql_result($query,0,'id_colgaap');
			$idPucNiif    = mysql_result($query,0,'id_niif');
			$cuenta_niif  = mysql_result($query,0,'cuenta_niif');
			$centro_costo = mysql_result($query,0,'centro_costo');

			$codigoCcos = "";
			$nombreCcos = "";
			if($centro_costo == 'Si'){
				if($arrayCuenta['centro_costo'] > 0){
					$sqlCcos   = "SELECT COUNT(id) AS contCCos,id,codigo,nombre FROM centro_costos WHERE id_empresa='$arrayWs[id_empresa]' AND codigo='$arrayCuenta[centro_costo]'";
					$queryCcos = mysql_query($sqlCcos,$link);

					$idCcos     = mysql_result($queryCcos, 0, 'id');
					$contCcos   = mysql_result($queryCcos, 0, 'contCcos');
					$codigoCcos = mysql_result($queryCcos, 0, 'codigo');
					$nombreCcos = mysql_result($queryCcos, 0, 'nombre');

					if($contCcos == 0 || !$queryCcos){ rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El centro de costo $arrayCuenta[centro_costo] No existe!"); continue 2; }
				}
				else{ rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] No tiene un centro de costo!"); continue 2; }
			}

			// CONSULTAR EL ID DE LA CUENTA NIIF SI NO SE ENVIA EN EL ARRAY
			if ($arrayCuenta['cuenta_niif']=='' && $cuenta_niif > 0) { $arrayCuenta['cuenta_niif'] = $cuenta_niif; }
			// SINO EXISTE LA CUENTA NIIF
			else if ($arrayCuenta['cuenta_niif']=='' && $cuenta_niif == '') { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] niif no existe en el sistema"); continue 2; }
			// SI EXISTE LA CUENTA NIIF
			else if($arrayCuenta['cuenta_niif'] > 0) {
				$sqlNiif   = "SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayCuenta[cuenta_niif]'";
				$queryNiif = mysql_query($sqlNiif,$link);
				$idPucNiif = mysql_result($queryNiif,0,'id');
			}

			if ($idPuc==0) { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] colgaap no existe en el sistema"); continue 2; }
			else if ($idPucNiif==0) { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_niif] niif no existe en el sistema"); continue 2; }

			if(!$arrayCuenta['cuenta_pago']){
				$valueInsertBody .= "('$id_documento',
									'".$arrayCuenta['cuenta_colgaap']."',
									'".$arrayCuenta['cuenta_niif']."',
									'$id_tercero_cuenta',
									'".$debito."',
									'".$credito."',
									'$idCcos',
									'$arrayCuenta[centro_costo]',
									'$arrayCuenta[concepto]',
									'$arrayWs[id_empresa]'),";
			}


	    	$valueInsertCuentasColgaap .= "('$id_documento',
											'$numeroFactura',
											'FV',
											'Factura de Venta',
											'".$debito."',
											'".$credito."',
											'".$arrayCuenta['cuenta_colgaap']."',
											'$id_sucursal',
											'$id_tercero_cuenta',
											'$arrayWs[id_empresa]',
											'$arrayDoc[fecha_documento]',
											'$id_documento',
											'FV',
											'$numeroFactura',
											'$idCcos'),";

			$valueInsertCuentasNiif .= "('$id_documento',
											'$numeroFactura',
											'FV',
											'Factura de Venta',
											'".$debito."',
											'".$credito."',
											'".$arrayCuenta['cuenta_niif']."',
											'$id_sucursal',
											'$id_tercero_cuenta',
											'$arrayWs[id_empresa]',
											'$arrayDoc[fecha_documento]',
											'$id_documento',
											'FV',
											'$numeroFactura',
											'$idCcos'),";
	    }

		$saldoTotal = ROUND($acumuladoDebito,2) - ROUND($acumuladoCredito,2);
	    if ($saldoTotal!=0){ rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El saldo Debito y Credito es diferente en la factura #$numeroFactura!"); continue; }
		else if ($valueInsertBody=='') { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no hay cuentas para insertar en la factura #$numeroFactura!"); continue; }

	    // SI ES UNA NOTA INSERTA EL CUERPO DE LA NOTA
		$valueInsertBody = substr($valueInsertBody, 0, -1);
		$sql   = "INSERT INTO ventas_facturas_cuentas(
						id_factura_venta,
						cuenta_puc,
						cuenta_niif,
						id_tercero,
						debito,
						credito,
						id_centro_costos,
						codigo_concepto,
						concepto,
						id_empresa
					)
				VALUES $valueInsertBody";
		$query = mysql_query($sql,$link);
		if (!$query) { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron las cuentas en la factura!"); }

		$valueInsertBody = '';
		$valueInsertCuentasColgaap = substr($valueInsertCuentasColgaap, 0, -1);
		$valueInsertCuentasNiif    = substr($valueInsertCuentasNiif, 0, -1);
	    if ($valueInsertCuentasColgaap =='' || $valueInsertCuentasColgaap =='') { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no hay cuentas para insertar!"); continue; }

	    $sqlInsert   = "INSERT INTO asientos_colgaap(
							id_documento,
							consecutivo_documento,
							tipo_documento,
							tipo_documento_extendido,
							debe,
							haber,
							codigo_cuenta,
							id_sucursal,
							id_tercero,
							id_empresa,
							fecha,
							id_documento_cruce,
							tipo_documento_cruce,
							numero_documento_cruce,
							id_centro_costos)
						VALUES $valueInsertCuentasColgaap";
		$queryInsert = mysql_query($sqlInsert,$link);
		if (!$queryInsert ) { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron los asientos colgaap!"); }

	    $sqlInsert   = "INSERT INTO asientos_niif(
									id_documento,
									consecutivo_documento,
									tipo_documento,
									tipo_documento_extendido,
									debe,
									haber,
									codigo_cuenta,
									id_sucursal,
									id_tercero,
									id_empresa,
									fecha,
									id_documento_cruce,
									tipo_documento_cruce,
									numero_documento_cruce,
									id_centro_costos)
								VALUES $valueInsertCuentasNiif";
		$queryInsert = mysql_query($sqlInsert,$link);
		if (!$queryInsert ) { rollback($numeroFactura, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron los asientos niif!"); continue; }

		response_error(array('factura' => $numeroFactura,'estado' => 'true', 'msj' => 'Factura #'.$numeroFactura.'  ok'),false);
	}

	//===================================// ROLLBAK DEL PROCESO DE INSERT //===================================//
	//**********************************************************************************************************/
	function rollback($numeroFactura, $tablaPrincipal,$id_documento,$id_empresa,$link,$msj=""){

		if($id_documento > 0){
			// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
			$sql   = "DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			// SI ES UNA NOTA, ELIMINAR EL CUERPO
			$sql   = "DELETE FROM ventas_facturas_cuentas WHERE activo=1 AND id_nota_general=$id_documento AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			// ELIMINAR LOS ASIENTO COLGAAP
			$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento AND tipo_documento='FV' AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			// ELIMINAR LOS ASIENTO NIIF
			$sql   = "DELETE FROM asientos_niif WHERE id_documento=$id_documento AND tipo_documento='FV' AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);
		}

		if($msj != ""){ response_error(array('factura' => $numeroFactura,'estado' => 'error','msj'=> $msj),false); }
	}

	//==========================================// RANDOMICO UNICO //==========================================//
	//**********************************************************************************************************/
	function responseUnicoRanomico(){

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