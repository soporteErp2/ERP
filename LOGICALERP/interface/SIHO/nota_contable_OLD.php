<?php
	//===================// CONFIGURACION DE INFORMACION //===================//
	//************************************************************************//
	$arrayWs['documento']['consecutivo']      = $consecutivo;
	$arrayWs['documento']['codigo_tipo_nota'] = 1;
	$arrayWs['documento']['fecha_documento']  = $fecha_metodo;
	$arrayWs['documento']['nit_tercero']      = $arrayWs['nit_empresa'];

	foreach ($responseWs as $i => $arrayCuenta) {
		$arrayCuenta['cuenta'].','.$arrayCuenta['debito'].' , '.$arrayCuenta['credito'].' , '.$arrayCuenta['cc'].' , '.$arrayCuenta['inv_identification'].'<br>';

		if($arrayCuenta['debito']==0 && $arrayCuenta['credito']==0){ continue; }
		$saldo    = ($arrayCuenta['debito'] - $arrayCuenta['credito']);
		$absSaldo = ABS($saldo);
		$debito   = ($saldo > 0)? $absSaldo: 0;
		$credito  = ($saldo < 0)? $absSaldo: 0;

		$arrayWs['documento']['cuentas'][$i] = array('cuenta_colgaap'=> $arrayCuenta['cuenta'],
														'centro_costo'=> $arrayCuenta['cc'],
														'nit_tercero_cuenta'=> 1143148632,
														'debito'=> $debito,
														'credito'=> $credito
													);

		// $arrayWs['documento']['cuentas'][$i] = array('cuenta_colgaap'=> $arrayCuenta['cuenta'],
		// 												'centro_costo'=> $arrayCuenta['cc'],
		// 												'nit_tercero_cuenta'=> $arrayCuenta['inv_identification'],
		// 												'debito'=> $debito,
		// 												'credito'=> $credito
		// 											);
	}

	$tablaPrincipal = 'nota_contable_general';
	$arrayDoc       = $arrayWs['documento'];

	if($arrayDoc['consecutivo'] > 0){
		$sqlNota   = "SELECT COUNT(id) AS contNota FROM nota_contable_general WHERE activo=1 AND id_empresa='$arrayWs[id_empresa]' AND consecutivo_ws = '$arrayDoc[consecutivo]' LIMIT 0,1";
		$queryNota = mysql_query($sqlNota,$link);
		$contNota  = mysql_result($queryNota, 0, 'contNota');
		if($contNota > 0){ response_error(array('estado' => 'error','msj'=>'La presente nota ya ha sido ingresada')); }
	}
	else{ response_error(array('estado' => 'error','msj'=>'La presente nota no tiene consecutivo')); }

	//TERCERO PRINCIPAL
	$sql   = "SELECT COUNT(id) AS cont,id,codigo,tipo_identificacion,numero_identificacion,nombre_comercial
				FROM terceros
				WHERE activo=1
					AND id_empresa='$arrayWs[id_empresa]'
					AND numero_identificacion='$arrayDoc[nit_tercero]'";
	$query = mysql_query($sql,$link);

	$cont       = mysql_result($query,0,'cont');
	$codigo     = mysql_result($query,0,'codigo');
	$id_tercero = mysql_result($query,0,'id');
	$tipo_nit   = mysql_result($query,0,'tipo_identificacion');
	$nit        = mysql_result($query,0,'numero_identificacion');
	$tercero    = mysql_result($query,0,'nombre_comercial');

	if($cont == 0){ response_error(array('estado' => 'error','msj'=>'No existe el tercero en la empresa')); }

	// VALIDAR EL TIPO DE LA NOTA
	$sqlNota    = "SELECT COUNT(id) AS cont,id FROM tipo_nota_contable WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND codigo='$arrayDoc[codigo_tipo_nota]'";
	$queryNota  = mysql_query($sqlNota,$link);
	$contNota   = mysql_result($queryNota,0,'cont');
	$idTipoNota = mysql_result($queryNota,0,'id');
	if($contNota == 0){ response_error(array('estado' => 'error','msj'=>'No existe el tipo de nota en la empresa')); }

	//INSERTAR LA CABECERA DEL DOCUMENTO
	$random_nota = responseUnicoRanomico();
    $sql   = "INSERT INTO nota_contable_general
    				(id_empresa,
					random,
    				id_sucursal,
    				sinc_nota,
    				id_tipo_nota,
    				fecha_finalizacion,
    				id_tercero,
    				codigo_tercero,
    				numero_identificacion_tercero,
    				tipo_identificacion_tercero,
    				tercero,
    				id_usuario,
    				cedula_usuario,
    				usuario,
    				estado,
    				consecutivo_ws)
            VALUES('$arrayWs[id_empresa]',
                	'$random_nota',
                	'$id_sucursal',
                	'colgaap_niif',
                	'$idTipoNota',
                	'$arrayDoc[fecha_documento]',
                	'$id_tercero',
                	'$codigo',
                	'$nit',
                	'$tipo_nit',
                	'$tercero',
                	'$arrayWs[id_empleado]',
                	'$arrayWs[documento_empleado]',
                	'$arrayWs[nombre_empleado]',
                	0,
                	'$arrayDoc[consecutivo]')";
    $query = mysql_query($sql,$link);
    if (!$query) { response_error(array('estado' => 'error','msj'=>'No se inserto el documento')); }

	$sqlSelectId  = "SELECT id FROM nota_contable_general WHERE random='$random_nota' LIMIT 0,1";
	$id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');
    $consecutivo_documento = 0;

    // INSERTAR LAS CUENTAS DEL DOCUMENTO
    foreach ($arrayDoc['cuentas'] as $key => $arrayCuenta) {

    	$debito  = $arrayCuenta['debito'];
		$credito = $arrayCuenta['credito'];
		$acumuladoDebito  += $arrayCuenta['debito'];
		$acumuladoCredito += $arrayCuenta['credito'];

		// CONSULTAR Y VALIDAR EL TERCERO
		if($arrayCuenta['nit_tercero_cuenta'] > 0){
			$sqlTerceros       = "SELECT COUNT(id) AS cont, id FROM terceros WHERE activo=1 AND id_empresa='$arrayWs[id_empresa]' AND numero_identificacion='$arrayCuenta[nit_tercero_cuenta]'";
			$queryTerceros     = mysql_query($sqlTerceros,$link);
			$cont_tercero_nota = mysql_result($queryTerceros,0,'cont');
			$id_tercero_nota   = mysql_result($queryTerceros,0,'id');

			if ($cont_tercero_nota == 0) {
				rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El tercero relacionado a la cuenta $arrayCuenta[cuenta_colgaap] no existe en el sistema");
			}
		}
		else{ $id_tercero_nota = $id_tercero; }

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

				if($contCcos == 0 || !$queryCcos){ rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El centro de costo $arrayCuenta[centro_costo] No existe!"); }
			}
			else{ rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] No tiene un centro de costo!"); }
		}

		// CONSULTAR EL ID DE LA CUENTA NIIF SI NO SE ENVIA EN EL ARRAY
		if ($arrayCuenta['cuenta_niif']=='' && $cuenta_niif != '') { $arrayCuenta['cuenta_niif'] = $cuenta_niif; }
		// SINO EXISTE LA CUENTA NIIF
		else if ($arrayCuenta['cuenta_niif']=='' && $cuenta_niif == '') { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] niif no existe en el sistema"); }
		// SI EXISTE LA CUENTA NIIF
		else if($arrayCuenta['cuenta_niif'] > 0) {
			$sqlNiif   = "SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayCuenta[cuenta_niif]'";
			$queryNiif = mysql_query($sqlNiif,$link);
			$idPucNiif = mysql_result($queryNiif,0,'id');
		}

		if ($idPuc==0) { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_colgaap] colgaap no existe en el sistema"); }
		else if ($idPucNiif==0) { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "La cuenta $arrayCuenta[cuenta_niif] niif no existe en el sistema"); }

		$valueInsertBody .= "('$id_documento',
							'$idPuc',
							'$idPucNiif',
							'$debito',
							'$credito',
							'$id_tercero_nota',
							'$tipoDocumentoCruce',
							'$id_documento_cruce',
							'$prefijoDocumentoCruce',
							'$numeroDocumentoCruce',
							'$idCcos',
							'$arrayWs[id_empresa]'),";


    	$valueInsertCuentasColgaap .= "('$id_documento',
										'$consecutivo_documento',
										'NCG',
										'Nota Contable General',
										'".$debito."',
										'".$credito."',
										'".$arrayCuenta['cuenta_colgaap']."',
										'$id_sucursal',
										'$id_tercero_nota',
										'$arrayWs[id_empresa]',
										'$arrayDoc[fecha_documento]',
										'$id_documento',
										'NCG',
										'$consecutivo_documento',
										'$idCcos'),";

		$valueInsertCuentasNiif .= "('$id_documento',
										'$consecutivo_documento',
										'NCG',
										'Nota Contable General',
										'".$debito."',
										'".$credito."',
										'".$arrayCuenta['cuenta_niif']."',
										'$id_sucursal',
										'$id_tercero_nota',
										'$arrayWs[id_empresa]',
										'$arrayDoc[fecha_documento]',
										'$id_documento',
										'NCG',
										'$consecutivo_documento',
										'$idCcos'),";
    }

	$saldoTotal = ROUND($acumuladoDebito,2) - ROUND($acumuladoCredito,2);
    if ($saldoTotal!=0){ rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "El saldo Debito y Credito es diferente!"); }
	else if ($valueInsertBody=='') { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no hay cuentas para insertar!"); }

    // SI ES UNA NOTA INSERTA EL CUERPO DE LA NOTA
	$valueInsertBody = substr($valueInsertBody, 0, -1);
	$sql   = "INSERT INTO nota_contable_general_cuentas(
							id_nota_general,
							id_puc,
							id_niif,
							debe,
							haber,
							id_tercero,
							tipo_documento_cruce,
							id_documento_cruce,
							prefijo_documento_cruce,
							numero_documento_cruce,
							id_centro_costos,
							id_empresa)
						VALUES $valueInsertBody";
	$query = mysql_query($sql,$link);
	if (!$query) { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron las cuentas en la nota!"); }

	$valueInsertCuentasColgaap = substr($valueInsertCuentasColgaap, 0, -1);
	$valueInsertCuentasNiif    = substr($valueInsertCuentasNiif, 0, -1);
    if ($valueInsertCuentasColgaap =='' || $valueInsertCuentasColgaap =='') { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no hay cuentas para insertar!"); }

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
	if (!$queryInsert ) { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron los asientos colgaap!"); }

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
	if (!$queryInsert ) { rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se insertaron los asientos niif!"); }

    // SI SE INSERTO TODO, ACTUALIZAR LA NOTA PARA GENERAR EL CONSECUTIVO
	$sql   = "UPDATE nota_contable_general SET estado=1 WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
	$query = mysql_query($sql,$link);

    if (!$query){ rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se genero el consecutivo de la nota"); }

    // CONSULTAR EL CONSECUTIVO PARA ACTUALIZAR LOS ASIENTOS
	$sql   = "SELECT COUNT(id) AS cont,consecutivo,consecutivo_niif FROM nota_contable_general WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
	$query = mysql_query($sql,$link);
	$cont             = mysql_result($query,0,'cont');
	$consecutivo      = mysql_result($query,0,'consecutivo');
	$consecutivo_niif = mysql_result($query,0,'consecutivo_niif');

    if ($cont==0) {
    	rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se pudo consultar el consecutivo del documento del documento! comuniquese con el administrador del sistema!");
    }

    // ACTUALIZAR LOS ASIENTOS CON LOS CONSECUTIVOS
	$sqlColgaap   = "UPDATE asientos_colgaap SET consecutivo_documento=$consecutivo, numero_documento_cruce=$consecutivo WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND tipo_documento='NCG' AND id_documento=$id_documento";
	$queryColgaap = mysql_query($sqlColgaap,$link);

	$sqlNiif   = "UPDATE asientos_niif SET consecutivo_documento=$consecutivo_niif, numero_documento_cruce=$consecutivo_niif WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND tipo_documento='NCG' AND id_documento=$id_documento";
	$queryNiif = mysql_query($sqlNiif,$link);

    if (!$queryColgaap || !$queryNiif) {
    	rollback($tablaPrincipal, $id_documento, $arrayWs['id_empresa'], $link, "no se actualizo el consecutivo a los asientos contables! comuniquese con el administradr del sistema");
    }

	response_error(array('estado' => 'true', 'msj' => 'El consecutivo de la Nota es '.$consecutivo));

	//===================================// ROLLBAK DEL PROCESO DE INSERT //===================================//
	//**********************************************************************************************************/
	function rollback($tablaPrincipal,$id_documento,$id_empresa,$link,$msj=""){

		// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
		$sql   = "DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		// SI ES UNA NOTA, ELIMINAR EL CUERPO
		$sql   = "DELETE FROM nota_contable_general_cuentas WHERE activo=1 AND id_nota_general=$id_documento AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		// ELIMINAR LOS ASIENTO COLGAAP
		$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento  AND tipo_documento='NCG' AND id_empresa=$id_empresa ";
		$query = mysql_query($sql,$link);

		// ELIMINAR LOS ASIENTO NIIF
		$sql   = "DELETE FROM asientos_niif WHERE id_documento=$id_documento  AND tipo_documento='NCG' AND id_empresa=$id_empresa ";
		$query = mysql_query($sql,$link);

		if($msj != ""){ response_error(array('estado' => 'error','msj'=> $msj)); }
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