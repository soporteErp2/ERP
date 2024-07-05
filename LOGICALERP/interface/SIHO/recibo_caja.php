<?php
	// print_r($responseWs);
	if ($responseWs=='') { response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'No hay recibos de caja para sincronizar'),false); exit; }
	// response_error(array('factura' => $numeroRecibo,'estado' => 'error','msj'=>'debug'),false); continue;

	//===================// CONFIGURACION DE INFORMACION //===================//
	//************************************************************************//
	$tablaPrincipal = 'recibo_caja';

	$arrayWs['documento']['consecutivo']      = $consecutivo;
	$arrayWs['documento']['codigo_tipo_nota'] = 1;
	$arrayWs['documento']['fecha_documento']  = $fecha_metodo;
	$arrayWs['documento']['nit_tercero']      = $arrayWs['nit_empresa'];

	//===================// CONSULTA CUENTA DE PAGO //===================//
	//*******************************************************************//
	$sqlCuentaPago   = "SELECT COUNT(id) AS contCuentaPago, id, cuenta, id_cuenta, cuenta_niif, estado, nombre,nombre_cuenta
						FROM configuracion_cuentas_pago
						WHERE activo=1 AND id_empresa='$arrayWs[id_empresa]' AND tipo='Venta'
						GROUP BY id";
	$queryCuentaPago = mysql_query($sqlCuentaPago,$link);
	while ($rowCuentaPago = mysql_fetch_assoc($queryCuentaPago)) {
		$cuenta = $rowCuentaPago['cuenta'];
		$arrayCuentaPago[$cuenta] = array(
											'idConfigCuentaPago' => $rowCuentaPago['id'],
											'cuentaPago'         => $rowCuentaPago['cuenta'],
											'idCuentaPago'       => $rowCuentaPago['id_cuenta'],
											'cuentaPagoNiif'     => $rowCuentaPago['cuenta_niif'],
											'contCuentaPago'     => $rowCuentaPago['contCuentaPago'],
											'estadoPago'         => $rowCuentaPago['estado'],
											'nombre'             => $rowCuentaPago['nombre'],
											'nombre_cuenta'      => $rowCuentaPago['nombre_cuenta']
										);
	}

	foreach ($responseWs as $indice => $arrayCuentas) {
		$numeroRecibo = $arrayCuentas['recibo'];

		$arrayCuentas['cuenta_iva_niif']    = 0;
		$arrayCuentas['cuenta_iva_colgaap'] = 0;
		$arrayCuentas['id_cuenta_pago']     = 0;
		$arrayCuentas['cuenta_niif']        = 0;
		$arrayCuentas['estado_pago']        = '';
		$arrayCuentas['total_factura']      = 0;
		$arrayCuentas['total_factura_sin_abono'] = 0;

		//=====================================// VALIDAR LA CUENTA DE PAGO //=====================================//
		//*********************************************************************************************************//
		if($arrayCuentas['tipo']=='R'){

			$arrayNit = explode('-', str_replace('.', '', $arrayCuentas['identificacion']));
			$arrayCuentas['identificacion'] = $arrayNit[0];

			if(!isset($arrayCuentaPago[$arrayCuentas['cuenta']]['idConfigCuentaPago'])){
				$RecibosFail[$numeroRecibo] = true;
				response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'La cuenta de pago '.$arrayCuentas['cuenta'].' no esta configurada en el recibo de caja #'.$numeroRecibo),false); continue;
			}

			$arrayCuentas['id_config_cuenta_pago'] = $arrayCuentaPago[$arrayCuentas['cuenta']]['idConfigCuentaPago'];
			$arrayCuentas['id_cuenta_pago']        = $arrayCuentaPago[$arrayCuentas['cuenta']]['idCuentaPago'];
			$arrayCuentas['cuenta_niif_pago']      = $arrayCuentaPago[$arrayCuentas['cuenta']]['cuentaPagoNiif'];
			$arrayCuentas['estado_pago']           = $arrayCuentaPago[$arrayCuentas['cuenta']]['estadoPago'];
			$arrayCuentas['nombre_pago']           = $arrayCuentaPago[$arrayCuentas['cuenta']]['nombre'];
			$arrayCuentas['nombre_cuenta_pago']    = $arrayCuentaPago[$arrayCuentas['cuenta']]['nombre_cuenta'];
			$arrayCuentas['cuenta_pago']           = $arrayCuentaPago[$arrayCuentas['cuenta']]['cuenta'];

			// if($estadoPago == 'Credito'){ $arrayCuentas['total_factura_sin_abono'] = $arrayCuentas['valor']; }

			$arrayRecibos[$numeroRecibo]['head'] = $arrayCuentas;
		}

		$arrayRecibos[$numeroRecibo]['cuentas'][] = $arrayCuentas;
	}

	// echo json_encode($arrayRecibos); exit;
	// print_r($arrayRecibos); exit;

	foreach ($arrayRecibos as $numeroRecibo => $arrayRecibo) {
		if($RecibosFail[$numeroRecibo]){ continue; }

		$arrayWs['documento']['cuentas'] = array();

		$headRecibo    = $arrayRecibo['head'];
		$cuentasRecibo = $arrayRecibo['cuentas'];

		$cuentaHead = $headRecibo['cuenta'];
		$nitTercero = $headRecibo['identificacion'];
		// $nitTercero = 14469098;

		//CONFIGURACION DE LA CUENTA DE PAGO
		$idConfigCuentaPago = $headRecibo['id_config_cuenta_pago'];
		$cuentaNiifPago     = $headRecibo['cuenta_niif_pago'];
		$cuentaPago     	= $headRecibo['cuenta_pago'];
		$idCuentaPago       = $headRecibo['id_cuenta_pago'];
		$tipo_pago          = $headRecibo['estado_pago'];
		$nombre_pago        = $headRecibo['nombre_pago'];
		$nombre_pago        = $headRecibo['nombre_pago'];
		$nombre_cuenta_pago = $headRecibo['nombre_cuenta_pago'];
		// $cuenta_pago        = $headRecibo['cuenta_pago'];

		$fecha_recibo      = $headRecibo['fecha'];

		$total_factura           = $headRecibo['total_factura'];
		$total_factura_sin_abono = $headRecibo['total_factura_sin_abono'];

		$validaDebito  = 0;
		$validaCredito = 0;

		$saldo_pago_debito    = 0;
		$saldo_pago_credito   = 0;
		$saldoCredito_factura = 0;

		$j = 0;
		foreach ($cuentasRecibo as $i => $arrayCuenta) {
			if($arrayCuenta['Debito'] == 0 && $arrayCuenta['Credito'] == 0){ continue; }		// VALIDACION

			$cuenta_pago = false;
			$saldo       = ($arrayCuenta['Debito'] - $arrayCuenta['Credito']);
			$absSaldo    = ABS($saldo);

			$debito  = ($saldo > 0)? $absSaldo: 0;
			$credito = ($saldo < 0)? $absSaldo: 0;

			if($cuentaHead == $arrayCuenta['cuenta']){
				$cuenta_pago = true;
				$saldo_pago_debito  += $debito;
				$saldo_pago_credito += $credito;
			}

			$validaDebito  += $debito;
			$validaCredito += $credito;

			$j++;
			$arrayWs['documento']['cuentas'][$j] = array('cuenta_niif'=> $arrayCuenta['cuenta_niif'],
															'cuenta_colgaap' => $arrayCuenta['cuenta'],
															'centro_costo'   => $arrayCuenta['centro_costo'],
															'concepto'       => $arrayCuenta['Descripcion'],
															'debito'         => $debito,
															'credito'        => $credito,
															'cuenta_pago'    => $cuenta_pago
														);
		}

		$validaDebito  = ROUND($validaDebito, 2);
		$validaCredito = ROUND($validaCredito, 2);
		if($validaDebito != $validaCredito) { response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'El saldo Debito $'.$validaDebito.' y Credito $'.$validaCredito.' son diferentes! en el recibo de caja #'.$numeroRecibo),false); continue; }
		else if($validaCredito==0) { response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'El saldo Debito/Credito es igual a cero! en el recibo de caja #'.$numeroRecibo),false); continue; }
		$arrayDoc = $arrayWs['documento'];

		//=====================================// VALIDACION RECIBO UNICA //=====================================//
		//********************************************************************************************************//
		$sqlFactura   = "SELECT COUNT(id) AS contRecibo FROM recibo_caja WHERE activo=1 AND id_empresa='$arrayWs[id_empresa]' AND consecutivo = '$numeroRecibo' AND activo=1 AND tipo='ws' LIMIT 0,1";
		$queryFactura = mysql_query($sqlFactura,$link);
		$contRecibo  = mysql_result($queryFactura, 0, 'contRecibo');
		if($contRecibo > 0){ response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'El recibo de caja  # '.$numeroRecibo.' ya ha sido ingresado!'),false); continue; }

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

		if($cont == 0){ response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'No existe el tercero numero de identificacion #'.$nitTercero.' del recibo de caja  # '.$numeroRecibo.' en la empresa'),false); continue; }

		$total_factura = $saldo_pago_debito - $saldo_pago_credito;
		$total_factura_sin_abono = 0;

		// SE COMENTO POR FACTURAS QUE MOSTRABAN UNA DEVOLUCION AL CLIENTE (EN PRUEBA)
		// SI SE COMENTA ESTA VALIDACION, PERMITE PASAR LAS FACTURAS CON SALDO NEGATIVO EN LA CUENTA DE BANCO-CAJA, PERO SI HAY SALDO DESCUADRADO EN REFERENCIA A LA CXC NO VALIDA
		if($total_factura < 0){ response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'El recibo de caja # '.$numeroRecibo.' Tiene un saldo negativo'),false); continue; }
		// if($tipo_pago == 'Credito'){ $total_factura_sin_abono = $total_factura; };

		//INSERTAR LA CABECERA DEL DOCUMENTO
		$randomRC = responseUnicoRanomico();
	    $sql   = "INSERT INTO recibo_caja
	    				(id_empresa,
	    				id_sucursal,
						random,
						consecutivo,
	    				id_configuracion_cuenta,
	    				configuracion_cuenta,
	    				cuenta,
	    				descripcion_cuenta,
	    				cuenta_niif,
	    				fecha_inicial,
	    				fecha_recibo,
	    				fecha_generado,
	    				id_tercero,
						codigo_tercero,
						nit_tercero,
						tercero,
	    				id_usuario,
	    				usuario,
	    				estado,
	    				tipo)
	            VALUES('$arrayWs[id_empresa]',
	                	'$id_sucursal',
	                	'$randomRC',
	                	'$numeroRecibo',
	                	'$idConfigCuentaPago',
	                	'$nombre_cuenta_pago',
	                	'$cuentaHead',
	                	'$nombre_pago',
						'$cuentaNiifPago',
	                	'$fecha_recibo',
	                	'$fecha_recibo',
	                	NOW(),
	                	'$id_tercero',
	                	'$codigo',
	                	'$nit',
	                	'$tercero',
	                	'$arrayWs[id_empleado]',
	                	'$arrayWs[nombre_empleado]',
	                	1,
	                	'Ws')";
	    $query = mysql_query($sql,$link);
	    if (!$query) { response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=>'No se inserto el recibo de caja #'.$numeroRecibo)); continue; }

		$sqlSelectId  = "SELECT id FROM recibo_caja WHERE random='$randomRC' LIMIT 0,1";
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
					rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El tercero relacionado a la cuenta $arrayCuenta[cuenta_colgaap] no existe en el sistema");
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

					if($contCcos == 0 || !$queryCcos){ rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El centro de costo $arrayCuenta[centro_costo] No existe!"); continue 2; }
				}
				else{ rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] - $arrayCuenta[centro_costo] No tiene un centro de costo!"); continue 2; }
			}

			// CONSULTAR EL ID DE LA CUENTA NIIF SI NO SE ENVIA EN EL ARRAY
			if ($arrayCuenta['cuenta_niif']=='' && $cuenta_niif > 0) { $arrayCuenta['cuenta_niif'] = $cuenta_niif; }
			// SINO EXISTE LA CUENTA NIIF
			else if ($arrayCuenta['cuenta_niif']=='' && $cuenta_niif == '') { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] niif no existe en el sistema"); continue 2; }
			// SI EXISTE LA CUENTA NIIF
			else if($arrayCuenta['cuenta_niif'] > 0) {
				$sqlNiif   = "SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayCuenta[cuenta_niif]'";
				$queryNiif = mysql_query($sqlNiif,$link);
				$idPucNiif = mysql_result($queryNiif,0,'id');
			}

			if ($idPuc==0) { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] colgaap no existe en el sistema"); continue 2; }
			else if ($idPucNiif==0) { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_niif] niif no existe en el sistema"); continue 2; }

			if(!$arrayCuenta['cuenta_pago']){
				$valueInsertBody .= "(
										'$id_documento',
										'".$idPuc."',
										'$id_tercero_cuenta',
										'".$debito."',
										'".$credito."',
										'$idCcos'
									),";
			}


	    	$valueInsertCuentasColgaap .= "('$id_documento',
											'$numeroRecibo',
											'RC',
											'Recibo de Caja (SIHO)',
											'".$debito."',
											'".$credito."',
											'".$arrayCuenta['cuenta_colgaap']."',
											'$id_sucursal',
											'$id_tercero_cuenta',
											'$arrayWs[id_empresa]',
											'$arrayDoc[fecha_documento]',
											'$id_documento',
											'RC',
											'$numeroRecibo',
											'$idCcos'),";

			$valueInsertCuentasNiif .= "('$id_documento',
											'$numeroRecibo',
											'RC',
											'Recibo de Caja (SIHO)',
											'".$debito."',
											'".$credito."',
											'".$arrayCuenta['cuenta_niif']."',
											'$id_sucursal',
											'$id_tercero_cuenta',
											'$arrayWs[id_empresa]',
											'$arrayDoc[fecha_documento]',
											'$id_documento',
											'RC',
											'$numeroRecibo',
											'$idCcos'),";
	    }

		$saldoTotal = ROUND($acumuladoDebito,2) - ROUND($acumuladoCredito,2);
	    if ($saldoTotal!=0){ rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El saldo Debito y Credito es diferente en el recibo de caja #$numeroRecibo!"); continue; }
		else if ($valueInsertBody=='') { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no hay cuentas para insertar en el recibo de caja #$numeroRecibo!"); continue; }

	    // SI ES UNA NOTA INSERTA EL CUERPO DE LA NOTA
		$valueInsertBody = substr($valueInsertBody, 0, -1);
		$sql   = "INSERT INTO recibo_caja_cuentas(
						id_recibo_caja,
						id_puc,
						id_tercero,
						debito,
						credito,
						id_centro_costos
					)
				VALUES $valueInsertBody";
		$query = mysql_query($sql,$link);
		if (!$query) { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron las cuentas en el recibo de caja!"); continue; }

		$valueInsertBody = '';
		$valueInsertCuentasColgaap = substr($valueInsertCuentasColgaap, 0, -1);
		$valueInsertCuentasNiif    = substr($valueInsertCuentasNiif, 0, -1);
	    if ($valueInsertCuentasColgaap =='' || $valueInsertCuentasColgaap =='') { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no hay cuentas para insertar!"); continue; }

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
		if (!$queryInsert ) { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron los asientos colgaap!"); }

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
		if (!$queryInsert ) { rollback($numeroRecibo, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron los asientos niif!"); continue; }

		// INSERTAR EN EL LOG DE EVENTOS
		// $sql="INSERT INTO log_documentos_contables (id_documento,id_usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip) VALUES  ($id_documento,$arrayWs[id_empleado],'Sincronizado','RC','Recibo de Caja (SIHO)',$id_sucursal,$arrayWs[id_empresa],'".$_SERVER['REMOTE_ADDR']."')";
		// $query=mysql_query($sql,$link);

		response_error(array('factura' => $numeroRecibo,'estado' => 'true', 'msj' => 'Recibo de caja #'.$numeroRecibo.'  ok'),false);
	}

	//===================================// ROLLBAK DEL PROCESO DE INSERT //===================================//
	//**********************************************************************************************************/
	function rollback($numeroRecibo, $tablaPrincipal,$id_documento,$id_empresa,$link,$msj=""){

		if($id_documento > 0){
			// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
			$sql   = "DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			// SI ES UNA NOTA, ELIMINAR EL CUERPO
			$sql   = "DELETE FROM recibo_caja_cuentas WHERE activo=1 AND id_nota_general=$id_documento AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			// ELIMINAR LOS ASIENTO COLGAAP
			$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento AND tipo_documento='RC' AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			// ELIMINAR LOS ASIENTO NIIF
			$sql   = "DELETE FROM asientos_niif WHERE id_documento=$id_documento AND tipo_documento='RC' AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);
		}

		if($msj != ""){ response_error(array('recibo' => $numeroRecibo,'estado' => 'error','msj'=> $msj),false); }
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