<?php
	//===================// CONFIGURACION DE INFORMACION //===================//
	//************************************************************************//
	$tablaPrincipal = 'nota_contable_general';

	$arrayWs['documento']['consecutivo']      = $consecutivo;
	$arrayWs['documento']['codigo_tipo_nota'] = 1;
	$arrayWs['documento']['fecha_documento']  = $fecha_metodo;
	$arrayWs['documento']['nit_tercero']      = $arrayWs['nit_empresa'];
	$id_empresa                               = $_SESSION['EMPRESA'];
	$id_sucursal                              = $_SESSION['SUCURSAL'];
	$id_usuario                               = $_SESSION['IDUSUARIO'];
	$cedula_usuario                           = $_SESSION['CEDULAFUNCIONARIO'];
	$usuario                                  = $_SESSION['NOMBREFUNCIONARIO'];

	// CONSULTAR EL PUC
	$sql="SELECT id,cuenta,descripcion,cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$id_empresa ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayPuc[$row['cuenta']] = array('id' => $row['id'], 'descripcion' => $row['descripcion'], 'cuenta_niif' => $row['cuenta_niif'] );
	}

	// print_r($responseWs);

	// CONSULTAR EL PUC NIIF
	$sql="SELECT id,cuenta,descripcion FROM puc_niif WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayPucNiif[$row['cuenta']] = array('id' => $row['id'], 'descripcion' => $row['descripcion'] );
	}

	// CONSULTAR CENTRO DE COSTOS
	$sql="SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayCentroCostos[$row['codigo']] = array('id' => $row['id'], 'nombre' => $row['nombre'] );
	}

	// CONSULTAR LOS TERCEROS
	$sql="SELECT id,numero_identificacion,nombre,codigo,tipo_identificacion FROM terceros WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayTerceros[$row['numero_identificacion']] = array('id' => $row['id'], 'nombre'=>$row['nombre'],'codigo'=>$row['codigo'],'tipo_identificacion'=>$row['tipo_identificacion'] );
	}

	// CONSULTAR LOS TIPOS DE NOTAS
	$sql="SELECT id,descripcion FROM tipo_nota_contable WHERE activo=1 AND id_empresa=$id_empresa ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayTipoNotas[$row['descripcion']]=$row['id'];
	}

	$contDocumentos=0;
	foreach ($responseWs as $indice => $arrayCuentas) {
		if($arrayCuentas['tipo_doc']=='F') { continue; }
		$contDocumentos++;
		$consecutivoDocumento = $arrayCuentas['N_Factura'];
		$arrayCuentas['tipo_documento'] = 'DOCUMENTO INTERNO';
		// VALIDAR LA CUENTA CONTABLE
		if (!array_key_exists("$arrayCuentas[Cuenta]", $arrayPuc)){
			$detalle_error = "* $arrayCuentas[tipo_documento] # $consecutivoDocumento: La cuenta PUC <b>$arrayCuentas[Cuenta]</b> no esta creada en el sistema<br>";
			response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>"$detalle_error"),false);
			continue;

		}
		// SI LA CUENTA COLGAAP EXISTE PERO NO TIENE UNA CUENTA NIIF CONFIGURADA
		else if ($arrayPuc[$arrayCuentas['Cuenta']]=='') {
			$detalle_error = "* $arrayCuentas[tipo_documento] # $consecutivoDocumento: La cuenta PUC <b>$arrayCuentas[Cuenta]</b> no tiene cuenta niif configurada <br>";
			response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>"$detalle_error"),false);
			continue;
		}

		// VALIDAR CENTRO DE COSTOS
		if (!array_key_exists("$arrayCuentas[Centro_Costo]", $arrayCentroCostos) && $arrayCuentas['Centro_Costo']<>''){
			$detalle_error = "* $arrayCuentas[tipo_documento] # $consecutivoDocumento: El centro costos <b>$arrayCuentas[Centro_Costo]</b> no esta creada en el sistema <br>";
			response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>"$detalle_error"),false);
			continue;
		}

		// VALIDAR EL TERCERO
		if (!array_key_exists("$arrayCuentas[Identificacion]", $arrayTerceros)){
			$detalle_error = "* $arrayCuentas[tipo_documento] # $consecutivoDocumento: Tercero con identificacion <b>$arrayCuentas[Identificacion]</b> no esta creado en el sistema <br>";
			response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>"$detalle_error"),false);
			continue;
		}

		// VALIDAR LOS TIPOS DE NOTAS
		if (!array_key_exists("$arrayCuentas[tipo_documento]", $arrayTipoNotas)){
			$detalle_error = "* $arrayCuentas[tipo_documento] # $consecutivoDocumento: Tipo de nota <b>$arrayCuentas[tipo_documento]</b> no esta creado en el sistema <br>";
			response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>"$detalle_error"),false);
			continue;
		}

		// AGREGAR LA CABECERA DEL DOCUMENTO
		if($arrayCuentas['tipo']=='N'){
			$arrayDocumentos[$consecutivoDocumento]['head'] = $arrayCuentas;
		}

		$arrayDocumentos[$consecutivoDocumento]['cuentas'][] = $arrayCuentas;
	}

	if ($contDocumentos==0) {
		response_error(array('estado' => 'error','msj'=>"No hay documentos a sincronizar en este dia"),false);
	}
	// echo json_encode($arrayDocumentos); exit;
	// print_r($arrayDocumentos); exit;

	foreach ($arrayDocumentos as $consecutivoDocumento => $arrayDocumentos) {

		$arrayWs['documento']['cuentas'] = array();

		$headFactura    = $arrayDocumentos['head'];
		$cuentasFactura = $arrayDocumentos['cuentas'];

		// $cuentaHead = $headFactura['Cuenta'];
		$nitTercero      = $headFactura['Identificacion'];
		$fecha_documento = $headFactura['inv_date_audit'];

		$validaDebito  = 0;
		$validaCredito = 0;

		$j = 0;
		foreach ($cuentasFactura as $i => $arrayCuenta) {
			if($arrayCuenta['Debito'] == 0 && $arrayCuenta['Credito'] == 0){ continue; }		// VALIDACION

			$saldo       = ($arrayCuenta['Debito'] - $arrayCuenta['Credito']);
			$absSaldo    = ABS($saldo);

			$debito  = ($saldo > 0)? $absSaldo: 0;
			$credito = ($saldo < 0)? $absSaldo: 0;

			$validaDebito  += $debito;
			$validaCredito += $credito;

			$j++;
			$arrayWs['documento']['cuentas'][$j] = array(
															'cuenta_niif'     => $arrayCuenta['cuenta_niif'],
															'cuenta_colgaap'  => $arrayCuenta['Cuenta'],
															'centro_costo'    => $arrayCuenta['Centro_Costo'],
															'concepto'        => $arrayCuenta['Descripcion'],
															'debito'          => $arrayCuenta['Debito'],
															'credito'         => $arrayCuenta['Credito'],
															'Identificacion'  => $arrayCuenta['Identificacion'],
															'tipo_documento'  => $arrayCuenta['tipo_documento'],
															'fecha_documento' => $arrayCuenta['inv_date_audit'],
														);
		}

		$validaDebito  = ROUND($validaDebito, 2);
		$validaCredito = ROUND($validaCredito, 2);
		if($validaDebito != $validaCredito) { response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>'El saldo Debito $'.$validaDebito.' y Credito $'.$validaCredito." son diferentes! documento ".$cuentasFactura['cuentas']['tipo_documento']."  #".$consecutivoDocumento." Comunique el error a soporte <b>SIHO</b>"),false); continue; }
		else if($validaCredito==0) { response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>'El saldo Debito/Credito es igual a cero! en el documento '.$cuentasFactura['cuentas']['tipo_documento'].' #'.$consecutivoDocumento." Comunique el error a soporte <b>SIHO</b>"),false); continue; }
		$arrayDoc = $arrayWs['documento'];

		//=====================================// VALIDACION DOCUMENTO UNICO //=====================================//
		//********************************************************************************************************//
		$sql   = "SELECT COUNT(id) AS contDocumento FROM nota_contable_general WHERE activo=1 AND id_empresa='$id_empresa' AND consecutivo = '$consecutivoDocumento' AND tipo_nota='$arrayCuenta[tipo_documento]' AND estado=1";
		$query = $mysql->query($sql,$mysql->link);
		$contDocumento  = $mysql->result($query, 0, 'contDocumento');
		if($contDocumento > 0){ response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>'El Documento '.$cuentasFactura['cuentas']['tipo_documento'].' # '.$consecutivoDocumento.' ya ha sido ingresado!'),false); continue; }

		//INSERTAR LA CABECERA DEL DOCUMENTO
		$randomDocumento = responseUnicoRanomico();
	    $sql   = "INSERT INTO nota_contable_general
	    			(
						random,
						consecutivo,
						sinc_nota,
						id_empresa,
						id_sucursal,
						fecha_nota,
						fecha_finalizacion,
						id_tipo_nota,
						id_tercero,
						codigo_tercero,
						numero_identificacion_tercero,
						tipo_identificacion_tercero,
						tercero,
						id_usuario,
						cedula_usuario,
						usuario,
						estado
	    			)
	            VALUES
	            	(
						'$randomDocumento',
						'$consecutivoDocumento',
						'colgaap_niif',
						'$id_empresa',
						'$id_sucursal',
						'$headFactura[inv_date_audit]',
						'$headFactura[inv_date_audit]',
						'".$arrayTipoNotas[$headFactura['tipo_documento']]."',
						'".$arrayTerceros[$headFactura['Identificacion']]['id']."',
						'".$arrayTerceros[$headFactura['Identificacion']]['codigo']."',
						'".$headFactura['Identificacion']."',
						'".$arrayTerceros[$headFactura['Identificacion']]['tipo_identificacion']."',
						'".$arrayTerceros[$headFactura['Identificacion']]['nombre']."',
						'$id_usuario',
						'$cedula_usuario',
						'$usuario',
						'1'
	    			)";
	    $query = $mysql->query($sql,$mysql->link);
	    if (!$query) { response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=>'No se inserto el documento '.$cuentasFactura['cuentas']['tipo_documento'].' # '.$consecutivoDocumento.'')); }

	    $sql="SELECT id FROM nota_contable_general WHERE random='$randomDocumento' LIMIT 0,1";
	    $query=$mysql->query($sql,$mysql->link);
	    $id_documento = $mysql->result($query,0,'id');

	    // INSERTAR LAS CUENTAS DEL DOCUMENTO
	    $acumuladoDebito  = 0;
		$acumuladoCredito = 0;

		$valueInsertBody           = '';
		$valueInsertCuentasNiif    = '';
		$valueInsertCuentasColgaap = '';
	    foreach ($arrayDoc['cuentas'] as $key => $arrayCuenta) {
	    	// print_r($arrayCuenta);
	    	$debito  = $arrayCuenta['debito'];
			$credito = $arrayCuenta['credito'];
			$acumuladoDebito  += $arrayCuenta['debito'];
			$acumuladoCredito += $arrayCuenta['credito'];

			$valueInsertBody .= "(
									'$id_documento',
									'".$arrayPuc[$arrayCuenta['cuenta_colgaap']]['id']."',
									'".$arrayPucNiif[$arrayPuc[$arrayCuenta['cuenta_colgaap']]['cuenta_niif']]['id']."',
									'".$arrayTerceros[$arrayCuenta['Identificacion']]['id']."',
									'',
									'',
									'',
									'',
									'$debito',
									'$credito',
									'".$arrayCentroCostos[$arrayCuenta['centro_costo']]['id']." ',
									'',
									'$id_empresa'
								),";

	    	$valueInsertCuentasColgaap .= "(
	    									'$id_documento',
											'$consecutivoDocumento',
											'NCG',
											'$arrayCuenta[tipo_documento]',
											'".$debito."',
											'".$credito."',
											'".$arrayCuenta['cuenta_colgaap']."',
											'$id_sucursal',
											'".$arrayTerceros[$arrayCuenta['Identificacion']]['id']."',
											'$id_empresa',
											'$arrayCuenta[fecha_documento]',
											'$id_documento',
											'NCG',
											'$consecutivoDocumento',
											'".$arrayCentroCostos[$arrayCuenta['centro_costo']]['id']."'
											),";

			$valueInsertCuentasNiif .= "(
											'$id_documento',
											'$consecutivoDocumento',
											'NCG',
											'$arrayCuenta[tipo_documento]',
											'".$debito."',
											'".$credito."',
											'".$arrayPuc[$arrayCuenta['cuenta_colgaap']]['cuenta_niif']."',
											'$id_sucursal',
											'".$arrayTerceros[$arrayCuenta['Identificacion']]['id']."',
											'$id_empresa',
											'$arrayCuenta[fecha_documento]',
											'$id_documento',
											'NCG',
											'$consecutivoDocumento',
											'".$arrayCentroCostos[$arrayCuenta['centro_costo']]['id']."'
										),";
	    }

		$saldoTotal = ROUND($acumuladoDebito,2) - ROUND($acumuladoCredito,2);
	    if ($saldoTotal!=0){ rollback($consecutivoDocumento, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $mysql, "El saldo Debito y Credito es diferente en el documento # $consecutivoDocumento!"); continue; }
		else if ($valueInsertBody=='') { rollback($consecutivoDocumento, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $mysql, "no hay cuentas para insertar en el documento # $consecutivoDocumento!"); continue; }

	    // SI ES UNA NOTA INSERTA EL CUERPO DE LA NOTA
		$valueInsertBody = substr($valueInsertBody, 0, -1);
		$sql   = "INSERT INTO nota_contable_general_cuentas(
															id_nota_general,
															id_puc,
															id_niif,
															id_tercero,
															tipo_documento_cruce,
															id_documento_cruce,
															prefijo_documento_cruce,
															numero_documento_cruce,
															debe,
															haber,
															id_centro_costos,
															observacion,
															id_empresa
															)
					VALUES $valueInsertBody";
		$query = $mysql->query($sql,$mysql->link);
		if (!$query) { rollback($consecutivoDocumento, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $mysql, "no se insertaron las cuentas en el documento!"); }

		$valueInsertBody = '';
		$valueInsertCuentasColgaap = substr($valueInsertCuentasColgaap, 0, -1);
		$valueInsertCuentasNiif    = substr($valueInsertCuentasNiif, 0, -1);
	    if ($valueInsertCuentasColgaap =='' || $valueInsertCuentasColgaap =='') { rollback($consecutivoDocumento, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $mysql, "no hay cuentas para insertar!"); continue; }

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
		$queryInsert = $mysql->query($sqlInsert,$mysql->link);
		if (!$queryInsert ) { rollback($consecutivoDocumento, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $mysql, "no se insertaron los asientos colgaap!"); }

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
		$queryInsert = $mysql->query($sqlInsert,$mysql->link);
		if (!$queryInsert ) { rollback($consecutivoDocumento, $tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $mysql, "no se insertaron los asientos niif!"); continue; }

		response_error(array('factura' => $consecutivoDocumento,'estado' => 'true', 'msj' => 'Documento '.$cuentasFactura['cuentas']['tipo_documento'].' #'.$consecutivoDocumento.'  ok'),false);
	}

	//===================================// ROLLBAK DEL PROCESO DE INSERT //===================================//
	//**********************************************************************************************************/
	function rollback($consecutivoDocumento, $tablaPrincipal,$id_documento,$id_empresa,$mysql,$msj=""){

		if($id_documento > 0){
			// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
			$sql   = "DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$id_empresa";
			$query = $mysql->query($sql,$mysql->link);

			// SI ES UNA NOTA, ELIMINAR EL CUERPO
			$sql   = "DELETE FROM nota_contable_general_cuentas WHERE activo=1 AND id_nota_general=$id_documento AND id_empresa=$id_empresa";
			$query = $mysql->query($sql,$mysql->link);

			// ELIMINAR LOS ASIENTO COLGAAP
			$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento AND tipo_documento='NCG' AND id_empresa=$id_empresa";
			$query = $mysql->query($sql,$mysql->link);

			// ELIMINAR LOS ASIENTO NIIF
			$sql   = "DELETE FROM asientos_niif WHERE id_documento=$id_documento AND tipo_documento='NCG' AND id_empresa=$id_empresa";
			$query = $mysql->query($sql,$mysql->link);
		}

		if($msj != ""){ response_error(array('Documento' => $consecutivoDocumento,'estado' => 'error','msj'=> $msj),false); }
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