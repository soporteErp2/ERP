<?php

	// VALIDAR QUE EXISTA LA SUCURSAL
	$sql="SELECT COUNT(id) AS cont,id FROM empresas_sucursales WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND nombre='$arrayWs[sucursal]' ";
	$query=mysql_query($sql,$conexion);
	$cont=mysql_result($query,0,'cont');
	$id_sucursal=mysql_result($query,0,'id');


	if($cont==0){  return array('estado' => 'false','msj'=>'No existe la sucursal en la empresa' );}

	// VALIDAR QUE EL TERCERO EXISTA
	$sql="SELECT COUNT(id) AS cont,id,codigo,tipo_identificacion,numero_identificacion,nombre_comercial FROM terceros WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND numero_identificacion='$arrayWs[nit_tercero]' ";
	$query=mysql_query($sql,$conexion);
	$cont                  =mysql_result($query,0,'cont');
	$codigo                =mysql_result($query,0,'codigo');
	$id_tercero            =mysql_result($query,0,'id');
	$tipo_identificacion   =mysql_result($query,0,'tipo_identificacion');
	$numero_identificacion =mysql_result($query,0,'numero_identificacion');
	$nombre_comercial      =mysql_result($query,0,'nombre_comercial');

	if($cont==0){  return array('estado' => 'false','msj'=>'No existe el tercero en la empresa' );}

	// VALIDAR EL TIPO DE LA NOTA
	$sql="SELECT COUNT(id) AS cont,id FROM tipo_nota_contable WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND descripcion='$arrayWs[tipo_nota]' ";
	$query=mysql_query($sql,$conexion);
	$cont =mysql_result($query,0,'cont');
	$id_tipo_nota =mysql_result($query,0,'id');
	if($cont==0){  return array('estado' => 'false','msj'=>'No existe el tipo de nota en la empresa' );}

	// INSERTAR LA CABECERA DEL DOCUMENTO
	$random_nota = responseUnicoRanomico();

	$tablaPrincipal='nota_contable_general';

    $sql   ="INSERT INTO $tablaPrincipal
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
    				estado)
            VALUES('$arrayWs[id_empresa]',
                	'$random_nota',
                	'$id_sucursal',
                	'colgaap_niif',
                	'$id_tipo_nota',
                	'$arrayWs[fecha_documento]',
                	'$id_tercero',
                	'$codigo',
                	'$numero_identificacion',
                	'$tipo_identificacion',
                	'$nombre_comercial',
                	'$arrayWs[id_empleado]',
                	'$arrayWs[documento_empleado]',
                	'$arrayWs[nombre_empleado]',
                	0)";
    $query = mysql_query($sql,$conexion);

    if (!$query) {   return array('estado' => 'false','msj'=>'No se inserto el documento' ); }

    $sqlSelectId = "SELECT id FROM $tablaPrincipal  WHERE random='$random_nota' LIMIT 0,1";
    $id_documento     = mysql_result(mysql_query($sqlSelectId,$conexion),0,'id');
    $consecutivo_documento  = 0;

    // INSERTAR LAS CUENTAS DEL DOCUMENTO
    foreach ($arrayWs['cuentas'] as $key => $arrayResul) {

    	$debito  = ($arrayResul['naturaleza']=='debito')? $arrayResul['saldo'] : 0 ;
		$credito = ($arrayResul['naturaleza']=='credito')? $arrayResul['saldo'] : 0 ;
		$acumuladoDebito  += ($arrayResul['naturaleza']=='debito')? $arrayResul['saldo'] : 0 ;
		$acumuladoCredito += ($arrayResul['naturaleza']=='credito')? $arrayResul['saldo'] : 0 ;

		// CONSULTAR Y VALIDAR EL TERCERO
		$sql = "SELECT COUNT(id) AS cont, id FROM terceros WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND numero_identificacion='$arrayResul[nit_tercero_cuenta]'";
		$query = mysql_query($sql,$conexion);
		$id_tercero_nota = mysql_result($query,0,'id');

		if ($id_tercero_nota==0) {
			rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
			return array('estado' => 'false','msj'=> "El tercero relacionado a la cuenta $arrayResul[cuenta_colgaap] en $arrayResul[naturaleza] no existe en el sistema");
		}

		// CONSULTAR EL ID DE LA CUENTA COLGAAP
		$sql="SELECT id FROM puc WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayResul[cuenta_colgaap]' ";
		$query=mysql_query($sql,$conexion);
		$idPuc=mysql_result($query,0,'id');

		// CONSULTAR EL ID DE LA CUENTA NIIF SI NO SE ENVIA EN EL ARRAY
		if ($arrayResul['cuenta_niif']=='') {
			$sql="SELECT cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayResul[cuenta_colgaap]'";
			$query=mysql_query($sql,$conexion);
			$arrayResul['cuenta_niif']=mysql_result($query,0,'cuenta_niif');
			// SINO EXISTE LA CUENTA NIIF
			if ($arrayResul['cuenta_niif']=='') {
				rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
				return array('estado' => 'false','msj'=> "La cuenta $arrayResul[cuenta_colgaap] niif no existe en el sistema");
			}
		}

		$sql="SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayResul[cuenta_niif]' ";
		$query=mysql_query($sql,$conexion);
		$idPucNiif=mysql_result($query,0,'id');

		if ($idPuc==0) {
			rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
			return array('estado' => 'false','msj'=> "La cuenta $arrayResul[cuenta_colgaap] colgaap no existe en el sistema");
		}
		if ($idPucNiif==0) {
			rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
			return array('estado' => 'false','msj'=> "La cuenta $arrayResul[cuenta_colgaap] niif no existe en el sistema");
		}

		$valueInsertBody.="(
							'$id_documento',
							'$idPuc',
							'$idPucNiif',
							'$debito',
							'$credito',
							'$id_tercero_nota',
							'$tipoDocumentoCruce',
							'$id_documento_cruce',
							'$prefijoDocumentoCruce',
							'$numeroDocumentoCruce',
							'$arrayWs[id_empresa]'),";


    	$valueInsertCuentasColgaap .= "('$id_documento',
										'$consecutivo_documento',
										'NCG',
										'Nota Contable General',
										'".$debito."',
										'".$credito."',
										'".$arrayResul['cuenta_colgaap']."',
										'$id_sucursal',
										'$id_tercero_nota',
										'$arrayWs[id_empresa]',
										'$arrayWs[fecha_documento]',
										'$id_documento',
										'NCG',
										'$consecutivo_documento'
										),";

		$valueInsertCuentasNiif .= "('$id_documento',
										'$consecutivo_documento',
										'NCG',
										'Nota Contable General',
										'".$debito."',
										'".$credito."',
										'".$arrayResul['cuenta_niif']."',
										'$id_sucursal',
										'$id_tercero_nota',
										'$arrayWs[id_empresa]',
										'$arrayWs[fecha_documento]',
										'$id_documento',
										'NCG',
										'$consecutivo_documento'
										),";

    }

    // SI ES UNA NOTA INSERTA EL CUERPO DE LA NOTA
	if ($valueInsertBody=='') {
		rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
		return array('estado' => 'false','msj'=> "no hay cuentas para insertar!");
	}
	$valueInsertBody = substr($valueInsertBody, 0, -1);
	$sql= "INSERT INTO nota_contable_general_cuentas(
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
							id_empresa)
						VALUES $valueInsertBody";
	$query=mysql_query($sql,$conexion);
	if (!$query) {
		rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
		return array('estado' => 'false','msj'=> "no se insertaron las cuentas en la nota!");
	}


    $saldoTotal=$acumuladoDebito - $acumuladoCredito;
    if ($saldoTotal!=0){
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
    	return array('estado' => 'false','msj'=> "la suma de lo saldos de las cuentas no son iguales!");
    }

	$valueInsertCuentasColgaap = substr($valueInsertCuentasColgaap, 0, -1);
	$valueInsertCuentasNiif    = substr($valueInsertCuentasNiif, 0, -1);
    if ($valueInsertCuentasColgaap =='' || $valueInsertCuentasColgaap =='') {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
    	return array('estado' => 'false','msj'=> "no hay cuentas para insertar!");
    }



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
								numero_documento_cruce)
							VALUES $valueInsertCuentasColgaap";
	$queryInsert = mysql_query($sqlInsert,$conexion);
	if (!$queryInsert ) {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
    	return array('estado' => 'false','msj'=> "no se insertaron los asientos colgaap!");
    }

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
								numero_documento_cruce)
							VALUES $valueInsertCuentasNiif";
	$queryInsert = mysql_query($sqlInsert,$conexion);
	if (!$queryInsert ) {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
    	return array('estado' => 'false','msj'=> "no se insertaron los asientos colgaap!");
    }

    // SI SE INSERTO TODO, ACTUALIZAR LA NOTA PARA GENERAR EL CONSECUTIVO
    $sql="UPDATE $tablaPrincipal SET estado=1 WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
    $query=mysql_query($sql,$conexion);

    if (!$query) {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
    	return array('estado' => 'false','msj'=> "no se genero el consecutivo de la nota");
    }

    // CONSULTAR EL CONSECUTIVO PARA ACTUALIZAR LOS ASIENTOS
    $sql="SELECT COUNT(id) AS cont,consecutivo,consecutivo_niif FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
    $query=mysql_query($sql,$conexion);
	$cont             = mysql_result($query,0,'cont');
	$consecutivo      = mysql_result($query,0,'consecutivo');
	$consecutivo_niif = mysql_result($query,0,'consecutivo_niif');

    if ($cont==0) {
    	// rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$arrayWs['tipo_documento']);
    	return array('estado' => 'false','msj'=> "no se pudo consultar el consecutivo del documento del documento! comuniquese con el administrador del sistema!");
    }

    // ACTUALIZAR LOS ASIENTOS CON LOS CONSECUTIVOS
    $sql="UPDATE asientos_colgaap SET consecutivo_documento=$consecutivo, numero_documento_cruce=$consecutivo WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND tipo_documento='NCG' AND id_documento=$id_documento";
    	// return array('estado' => 'false','msj'=> $sql);
    $queryColgaap=mysql_query($sql,$conexion);
    $sql="UPDATE asientos_niif SET consecutivo_documento=$consecutivo_niif, numero_documento_cruce=$consecutivo_niif WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND tipo_documento='NCG' AND id_documento=$id_documento";
    $queryNiif=mysql_query($sql,$conexion);

    if (!$queryColgaap || !$queryNiif) {
    	return array('estado' => 'false','msj'=> "no se actualizo el consecutvo a los asientos contables! comuniquese con el administradr del sistema");
    }

	return array('estado' => 'true');

	// ROLLBAK DEL PROCESO DE INSERT
	function rollback($tablaPrincipal,$id_documento,$id_empresa,$conexion,$tipo_documento=''){

		// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
		$sql="DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$arrayWs[id_empresa]";
		$query=mysql_query($sql,$conexion);

		// SI ES UNA NOTA, ELIMINAR EL CUERPO
		$sql="DELETE FROM nota_contable_general_cuentas WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id_nota_general=$id_documento";
		$query=mysql_query($sql,$conexion);

		// ELIMINAR LOS ASIENTO COLGAAP
		$sql="DELETE FROM asientos_colgaap WHERE id_documento=$id_documento  AND tipo_documento='$tipo_documento' AND id_empresa=$id_empresa ";
		$query=mysql_query($sql,$conexion);

		// ELIMINAR LOS ASIENTO NIIF
		$sql="DELETE FROM asientos_niif WHERE id_documento=$id_documento  AND tipo_documento='$tipo_documento' AND id_empresa=$id_empresa ";
		$query=mysql_query($sql,$conexion);

	}

	function responseUnicoRanomico(){

		//Si es un Nuevo Documento -->
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