<?php

	$consecutivo    = str_replace("-", "", $fecha_metodo);
	$tablaPrincipal = 'nota_contable_general';

	/**
	* $id_empresa
	* $id_sucursal
	* $id_empleado
	* $consecutivo
	* $nit_empresa
	*/
	//===================// CONFIGURACION DE INFORMACION //===================//
	//************************************************************************//

	// TIPO NOTA
	$idTipoNota = array();
	$sqlFiltro = "SELECT if(descripcion='INGRESO','I','R') AS tipo,id
					FROM tipo_nota_contable
					WHERE activo=1
						AND id_empresa='$id_empresa'
						AND (descripcion='INGRESO' OR descripcion='REVERSION')
					GROUP BY descripcion
					LIMIT 0,2";
	$queryFiltro = $mysql->query($sqlFiltro);
	while ($row = $mysql->fetch_object($queryFiltro)) { $idTipoNota[$row->tipo] = $row->id; }

	if(!isset($idTipoNota['I']) || !isset($idTipoNota['R'])){ response_error(array('estado' => 'error','msj'=>'Falta la Configuracion de los filtros de nota INGRESO y REVERSION')); }
	else if(!isset($idTipoNota['I'])){ response_error(array('estado' => 'error','msj'=>'Configure el filtro nota tipo INGRESO')); }
	else if(!isset($idTipoNota['R'])){ response_error(array('estado' => 'error','msj'=>'Configure el filtro nota tipo REVERSION')); }

	// TERCERO
	$sql   = "SELECT COUNT(id) AS cont,id,codigo,tipo_identificacion,numero_identificacion,nombre_comercial
				FROM terceros
				WHERE activo=1
					AND id_empresa='$id_empresa'
					AND numero_identificacion='$nit_empresa'";
	$query = mysql_query($sql,$link);

	$sql   = "SELECT COUNT(id) AS cont,codigo,tipo_documento,id_tercero,nit,tercero
				FROM web_service_tercero_causacion
				WHERE activo=1
					AND id_empresa='$id_empresa'
					";
	$query = mysql_query($sql,$link);

	$cont           = mysql_result($query,0,'cont');
	$idTercero      = mysql_result($query,0,'id_tercero');
	$nitTercero     = mysql_result($query,0,'nit');
	$nombreTercero  = mysql_result($query,0,'tercero');
	$codigoTercero  = mysql_result($query,0,'codigo');
	$tipoNitTercero = mysql_result($query,0,'tipo_documento');

	if($cont == 0){ response_error(array('estado' => 'error','msj'=>'No se configuro el tercero de causacion! Dirijase al panel de control -> Configuracion Tercero Ingresos y Reversiones -> y seleccione el tercero con el que se sincronizaran las notas')); }

	// // CCOS
	// $sqlCcos   = "SELECT id,codigo,nombre FROM centro_costos WHERE id_empresa='$id_empresa'";
	// $queryCcos = mysql_query($sqlCcos,$link);
	// while ($row = $mysql->fetch_object($queryCcos)) {
	// 	$arrayCcos["$row->codigo"] = $row->id;
	// }


	//=========================// BUCLE DE CUENTAS //=========================//
	//************************************************************************//
	$arrayNota   = array();
	$arrayCuenta = array();
	$arraySaldo  = array('I'=>array('debito'=>0,'credito'=>0),
						'R'=>array('debito'=>0,'credito'=>0));

	// $arrayCcosWs[0] = '';
	foreach ($responseWs as $i => $arrayWS) {

		if($arrayWS['Debito']==0 && $arrayWS['Credito']==0){ continue; }

		$tipo     = $arrayWS['Tipo'];
		$saldo    = ($arrayWS['Debito'] - $arrayWS['Credito']);
		$absSaldo = ABS($saldo);
		$debito   = ($saldo > 0)? $absSaldo: 0;
		$credito  = ($saldo < 0)? $absSaldo: 0;

		$arrayCuenta["$arrayWS[Cuenta]"]     = array();
		if ($arrayWS["Centro_Costo"]<>'') {$arrayCcosWs["$arrayWS[Centro_Costo]"] = array();}


		$arraySaldo[$tipo]['debito']  += $debito;
		$arraySaldo[$tipo]['credito'] += $credito;

		$arrayNota[$tipo][$i] = array('cuenta'=> $arrayWS['Cuenta'],
										'centro_costo'=> $arrayWS['Centro_Costo'],
										'debito'=> $debito,
										'credito'=> $credito,
										'documento'=>$documento
									);

	}

	//===========================// VALIDACIONES //===========================//
	//************************************************************************//

	// CONSECUTIVO NOTA
	$typeRepeat = array();
	$sqlNota   = "SELECT if(tipo_nota='INGRESO','I','R') AS tipo,id
				FROM nota_contable_general
				WHERE activo=1
					AND (estado=1 OR estado=2)
					AND id_empresa='$id_empresa'
					AND consecutivo_ws = '$consecutivo'
					AND (tipo_nota='INGRESO' OR tipo_nota='REVERSION')
				GROUP BY tipo_nota";
	$queryNota = mysql_query($sqlNota,$link);

	$contNota  = mysql_result($queryNota, 0, 'contNota');
	while ($row = $mysql->fetch_object($queryNota)) { $typeRepeat[$row->tipo] = $row->id; }
	if(isset($typeRepeat['I']) && isset($typeRepeat['R'])){ response_error(array('estado' => 'error','msj'=>'Las notas de Ingreso y Reversion ya han sido ingresadas')); }

	// CUENTAS
	$whereCuentas = "C.cuenta='".implode("' OR C.cuenta='", array_keys($arrayCuenta))."'";
	$sql   = "SELECT C.id AS id_colgaap,
					C.cuenta AS colgaap,
					C.centro_costo AS ccos,
					C.cuenta_niif AS niif,
					N.id AS id_niif
				FROM puc AS C
				INNER JOIN puc_niif AS N ON(
					N.activo = 1
					AND N.id_empresa= C.id_empresa
					AND C.cuenta_niif = N.cuenta
					AND C.cuenta_niif > 0
				)
				WHERE C.activo=1
					AND C.id_empresa=$id_empresa
					AND ($whereCuentas)";
	$query = $mysql->query($sql,$link);

	while ($row = $mysql->fetch_object($query)) {
		$arrayPuc["$row->colgaap"] = array('id_colgaap'=>$row->id_colgaap,
											'id_niif'=>$row->id_niif,
											'ccos'=>$row->ccos,
											'colgaap'=>$row->colgaap,
											'niif'=>$row->niif
										);
	}

	// VALIDACION EXISTE LAS CUENTAS!
	$diffCuentas = array_diff_key($arrayCuenta, $arrayPuc);
	if(COUNT($diffCuentas)){
		$msj = implode("<br>", array_keys($diffCuentas));
		response_error(array('estado' => 'error','msj'=>'Aviso, Configure las cuentas PUC <br><br>'.$msj));
	}

	// CENTROS DE COSTO
	$whereCcos = "codigo='".implode("' OR codigo='", array_keys($arrayCcosWs))."'";
	$sql="SELECT id,codigo FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCcos) ";
	$query=$mysql->query($sql,$mysql->link);
	// $arrayCcos[0]='';
	while ($row = $mysql->fetch_object($query)) {
		$arrayCcos["$row->codigo"] =  $row->id;
	}

	// VALIDACION EXISTE LOS CENTORS DE COSTO!
	$diffCcos = array_diff_key($arrayCcosWs, $arrayCcos);

	if(COUNT($diffCcos)){
		$msj = implode("<br>*", array_keys($diffCcos));
		response_error(array('estado' => 'error','msj'=>'Aviso, Configure los centros de costo <br><br>'.$msj));
	}


	// echo "<script>console.log(' ".." ');</script>";
	foreach ($arrayNota as $tipo => $arrayDatos) {
		$arraySaldo[$tipo]['debito'] = ROUND($arraySaldo[$tipo]['debito'],$_SESSION['DECIMALESMONEDA']);
		$arraySaldo[$tipo]['credito'] = ROUND($arraySaldo[$tipo]['credito'],$_SESSION['DECIMALESMONEDA']);

		if(isset($typeRepeat[$tipo])){ echo '<br>*La nota '.$consecutivo.' tipo '.(($tipo=='I')?'INGRESO':'REVERSION').' ya fue ingresada;'; continue; }
		else if($arraySaldo[$tipo]['debito'] != $arraySaldo[$tipo]['credito']){ echo 'No se cumple doble partida en la nota '.$consecutivo.' tipo '.(($tipo=='I')?'INGRESO':'REVERSION').' '.$arraySaldo[$tipo]['debito'].'!='.$arraySaldo[$tipo]['credito'].';'; continue; }

		$idNota = insertHead($mysql,$id_empresa,$id_sucursal,$idTipoNota[$tipo],$fecha_metodo,$idTercero,$codigoTercero,$nitTercero,$tipoNitTercero,$nombreTercero,$id_empleado,$documento_empleado,$nombre_empleado,$consecutivo);

		$insertBody = "";
		$insertNiif = "";
		$insertColgaap = "";
		foreach ($arrayDatos as $datos) {
			$cuenta    = $datos['cuenta'];
			$idPuc     = $arrayPuc["$cuenta"]['id_colgaap'];
			$idPucNiif = $arrayPuc["$cuenta"]['id_niif'];
			$colgaap   = $arrayPuc["$cuenta"]['colgaap'];
			$niif      = $arrayPuc["$cuenta"]['niif'];
			$idCcos    = ($arrayPuc["$cuenta"]['ccos'] == 'Si')? $arrayCcos[$datos['centro_costo']] : '' ;

			$insertBody .= "('$idNota',
							'$idPuc',
							'$idPucNiif',
							'$datos[debito]',
							'$datos[credito]',
							'$idTercero',
							'$idCcos',
							'$id_empresa'),";

			$insertColgaap .= "('$idNota',
								'$consecutivo',
								'NCG',
								'Nota Contable General',
								'$datos[debito]',
								'$datos[credito]',
								'$colgaap',
								'$id_empresa',
								'$id_sucursal',
								'$idTercero',
								'$fecha_metodo',
								'$idNota',
								'NCG',
								'$consecutivo',
								'$idCcos'),";

			$insertNiif .= "('$idNota',
							'$consecutivo',
							'NCG',
							'Nota Contable General',
							'$datos[debito]',
							'$datos[credito]',
							'$niif',
							'$id_empresa',
							'$id_sucursal',
							'$idTercero',
							'$fecha_metodo',
							'$idNota',
							'NCG',
							'$consecutivo',
							'$idCcos'),";
		}

		// SI ES UNA NOTA INSERTA EL CUERPO DE LA NOTA
		$insertBody = substr($insertBody, 0, -1);
		$sql   = "INSERT INTO nota_contable_general_cuentas(
					id_nota_general,
					id_puc,
					id_niif,
					debe,
					haber,
					id_tercero,
					id_centro_costos,
					id_empresa)
				VALUES $insertBody";
		$query = $mysql->query($sql,$link);
		if (!$query) { rollback($tablaPrincipal, $idNota, $id_empresa, $link, "no se insertaron las cuentas en la nota!"); }

		$insertColgaap = substr($insertColgaap, 0, -1);
		$insertNiif    = substr($insertNiif, 0, -1);
		if ($insertColgaap =='' || $insertColgaap =='') { rollback($tablaPrincipal, $idNota, $id_empresa, $link, "no hay cuentas para insertar!"); }

		$sqlInsert   = "INSERT INTO asientos_colgaap(
							id_documento,
							consecutivo_documento,
							tipo_documento,
							tipo_documento_extendido,
							debe,
							haber,
							codigo_cuenta,
							id_empresa,
							id_sucursal,
							id_tercero,
							fecha,
							id_documento_cruce,
							tipo_documento_cruce,
							numero_documento_cruce,
							id_centro_costos)
						VALUES $insertColgaap";
		$queryInsert = $mysql->query($sqlInsert,$link);
		if (!$queryInsert ) { rollback($tablaPrincipal, $idNota, $id_empresa, $link, "no se insertaron los asientos colgaap!"); }

		$sqlInsert   = "INSERT INTO asientos_niif(
							id_documento,
							consecutivo_documento,
							tipo_documento,
							tipo_documento_extendido,
							debe,
							haber,
							codigo_cuenta,
							id_empresa,
							id_sucursal,
							id_tercero,
							fecha,
							id_documento_cruce,
							tipo_documento_cruce,
							numero_documento_cruce,
							id_centro_costos)
						VALUES $insertNiif";
		$queryInsert = mysql_query($sqlInsert,$link);
		if (!$queryInsert ) { rollback($tablaPrincipal, $idNota, $id_empresa, $link, "no se insertaron los asientos niif!"); }

		echo "<br>* Nota de ".(($tipo=='I')?'ingreso':'reversion')." OK";
	}



	// INSERTAR LA CABECERA DEL DOCUMENTO
	function insertHead($mysql,$id_empresa,$id_sucursal,$idTipoNota,$fecha_metodo,$idTercero,$codigoTercero,$nitTercero,$tipoNitTercero,$nombreTercero,$id_empleado,$documento_empleado,$nombre_empleado,$consecutivo){
		$random_nota = responseUnicoRanomico();
		$sql   = "INSERT INTO nota_contable_general
						(random,
						id_empresa,
						id_sucursal,
						consecutivo,
						consecutivo_niif,
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
				VALUES('$random_nota',
						'$id_empresa',
						'$id_sucursal',
						'$consecutivo',
						'$consecutivo',
						'colgaap_niif',
						'$idTipoNota',
						'$fecha_metodo',
						'$idTercero',
						'$codigoTercero',
						'$nitTercero',
						'$tipoNitTercero',
						'$nombreTercero',
						'$id_empleado',
						'$documento_empleado',
						'$nombre_empleado',
						1,
						'$consecutivo')";
		$query = $mysql->query($sql,$link);
		if (!$query) { response_error(array('estado' => 'error','msj'=>'No se inserto el documento')); }

		$sqlSelectId  = "SELECT id FROM nota_contable_general WHERE random='$random_nota' LIMIT 0,1";
		$id_documento = $mysql->result(mysql_query($sqlSelectId),0,'id');

		return $id_documento;
	}

	//===================================// ROLLBAK DEL PROCESO DE INSERT //===================================//
	//**********************************************************************************************************/
	function rollback($tablaPrincipal,$id_documento,$id_empresa,$link,$msj=""){

		// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
		$sql   = "DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		// SI ES UNA NOTA, ELIMINAR EL CUERPO
		$sql   = "DELETE FROM nota_contable_general_cuentas WHERE activo=1 AND id_nota_general=$id_documento AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		// ELIMINAR LOS ASIENTOS COLGAAP
		$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento  AND tipo_documento='NCG' AND id_empresa=$id_empresa ";
		$query = mysql_query($sql,$link);

		// ELIMINAR LOS ASIENTOS NIIF
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