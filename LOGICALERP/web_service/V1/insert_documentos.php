<?php

	// VALIDAR QUE EXISTA LA SUCURSAL
	$sql         = "SELECT COUNT(id) AS cont,id FROM empresas_sucursales WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND nombre='$arrayWs[sucursal]'";
	$query       = mysql_query($sql,$conexion);
	$cont        = mysql_result($query,0,'cont');
	$id_sucursal = mysql_result($query,0,'id');


	if($cont==0){ return array('estado' => 'false','msj'=>'No existe la sucursal en la empresa' ); }

	// VALIDAR QUE EL TERCERO EXISTA
	$sql   = "SELECT COUNT(id) AS cont,id,codigo,tipo_identificacion,numero_identificacion,nombre_comercial FROM terceros WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND numero_identificacion='$arrayWs[nit_tercero]' ";
	$query = mysql_query($sql,$conexion);
	$cont                  = mysql_result($query,0,'cont');
	$codigo                = mysql_result($query,0,'codigo');
	$id_tercero            = mysql_result($query,0,'id');
	$tipo_identificacion   = mysql_result($query,0,'tipo_identificacion');
	$numero_identificacion = mysql_result($query,0,'numero_identificacion');
	$nombre_comercial      = mysql_result($query,0,'nombre_comercial');

	if($cont==0){  return array('estado' => 'false','msj'=>'No existe el tercero en la empresa' ); }

	// VALIDAR QUE EXISTA LA CUENTA DE COBRO
	$sql   = "SELECT COUNT(id) AS cont, id,nombre,id_cuenta,cuenta,cuenta_niif FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayWs[cuenta_pago_colgaap]'";
	$query = mysql_query($sql,$conexion);
	$cont                 = mysql_result($query,0,'cont');
	$id_cuenta_pago_cobro = mysql_result($query,0,'id');
	$nombre_cuenta_pago   = mysql_result($query,0,'nombre');
	$id_cuenta            = mysql_result($query,0,'id_cuenta');
	$cuenta               = mysql_result($query,0,'cuenta');
	$cuenta_niif          = mysql_result($query,0,'cuenta_niif');

	if($cont==0){ return array('estado' => 'false','msj'=>'la cuenta de pago no existe en el sistema!' ); }


	// INSERTAR LA CABECERA DEL DOCUMENTO
	$random_documento = responseUnicoRanomico();
	if ($arrayWs['tipo_documento']=='factura_venta') {

		if($cont==0){ return array('estado' => 'false','msj'=>'no hay resolucion dian para los consecutivos de las facturas!' ); }

		// CONSULTAR EL CENTRO DE COSTOS SI LO ENVIA
		if ($arrayWs['codigo_centro_costos']!='') {
			$sql   = "SELECT COUNT(id) AS cont,id FROM centro_costos WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND codigo='$arrayWs[codigo_centro_costos]'";
			$query = mysql_query($sql,$conexion);
			$cont            = mysql_result($query,0,'cont');
			$id_centro_costo = mysql_result($query,0,'id');
		}


		if($cont==0){ return array('estado' => 'false','msj'=>'no existe el centro de costos en el sistema!' ); }

		$tablaPrincipal     = 'ventas_facturas';
		$camposInsert       = "(random,
								fecha_creacion,
								fecha_contabilizado,
								fecha_inicio,
								fecha_vencimiento,
								id_cliente,
								documento_vendedor,
								nombre_vendedor,
								id_usuario,
								id_configuracion_cuenta_pago,
								configuracion_cuenta_pago,
								id_cuenta_pago,
								cuenta_pago,
								cuenta_pago_niif,
								id_empresa,
								id_sucursal,
								id_centro_costo,
    							estado)";
		$camposValuesInsert = "('$random_documento',
								'$arrayWs[fecha_documento]',
								'$arrayWs[fecha_documento]',
								'$arrayWs[fecha_documento]',
								'$arrayWs[fecha_vencimiento]',
								'$id_tercero',
								'$arrayWs[documento_empleado]',
								'$arrayWs[nombre_empleado]',
								'$arrayWs[id_empleado]',
								'$id_cuenta_pago_cobro',
								'$nombre_cuenta_pago',
								'$id_cuenta',
								'$cuenta',
								'$cuenta_niif',
								'$arrayWs[id_empresa]',
								'$id_sucursal',
								'$id_centro_costo',
    							'0')";
		$tipo_documento           ='FV';
		$tipo_documento_extendido ='Factura de Venta';
		$whereCamposValidate="prefijo='$arrayWs[prefijo_documento]' AND numero_factura='$arrayWs[numero_documento]'";
	}
	else if ($arrayWs['tipo_documento']=='factura_compra') {
		$tablaPrincipal = 'compras_facturas';
		$camposInsert   = "(id_empresa,
                            random,
                            id_proveedor,
                            id_sucursal,
                            fecha_final,
                            id_configuracion_cuenta_pago,
                            configuracion_cuenta_pago,
                            cuenta_pago,
                            cuenta_pago_niif,
                            id_usuario)";

          $camposValuesInsert="('$arrayWs[id_empresa]',
                                '$random_documento',
                                '$id_tercero',
                                '$id_sucursal',
                                '$arrayWs[fecha_documento]',
                                '$id_cuenta_pago_cobro',
                                '$nombre_cuenta_pago',
                                '$cuenta',
                                '$cuenta_niif',
                                '$arrayWs[id_empresa]')";

		$tipo_documento           ='FC';
		$tipo_documento_extendido ='Factura de Compra';
		$whereCamposValidate="prefijo_factura='$arrayWs[prefijo_documento]' AND numero_factura='$arrayWs[numero_documento]'";

	}

	// VALIDAR QUE EL DOCUMENTO NO EXISTA, TENIENDO COMO PUNTO EL NUMERO DEL DOCUMENTO
	$sql   = "SELECT COUNT(id) AS cont FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND $whereCamposValidate";
	$query = mysql_query($sql,$conexion);
	$cont  = mysql_result($query,0,'cont');
    if ($cont>0) { return array('estado' => 'false','msj'=>'ya existe una factura con ese numero de documento!' ); }

    $sql   = "INSERT INTO $tablaPrincipal $camposInsert VALUES $camposValuesInsert";
    $query = mysql_query($sql,$conexion);

    if (!$query) { return array('estado' => 'false','msj'=>'No se inserto el documento ' ); }

	$sqlSelectId  = "SELECT id FROM $tablaPrincipal  WHERE random='$random_documento' LIMIT 0,1";
	$id_documento = mysql_result(mysql_query($sqlSelectId,$conexion),0,'id');
    $consecutivo_documento  = 0;

    // INSERTAR LAS CUENTAS DEL DOCUMENTO
    foreach ($arrayWs['cuentas'] as $key => $arrayResul) {

    	$debito  = ($arrayResul['naturaleza']=='debito')? $arrayResul['saldo'] : 0;
		$credito = ($arrayResul['naturaleza']=='credito')? $arrayResul['saldo'] : 0;
		$acumuladoDebito  += ($arrayResul['naturaleza']=='debito')? $arrayResul['saldo'] : 0;
		$acumuladoCredito += ($arrayResul['naturaleza']=='credito')? $arrayResul['saldo'] : 0;

		// CONSULTAR EL ID DE LA CUENTA COLGAAP
		$sql   = "SELECT id FROM puc WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayResul[cuenta_colgaap]'";
		$query = mysql_query($sql,$conexion);
		$idPuc = mysql_result($query,0,'id');

		// CONSULTAR EL ID DE LA CUENTA NIIF SI NO SE ENVIA EN EL ARRAY
		if ($arrayResul['cuenta_niif']=='') {
			$sql   = "SELECT cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayResul[cuenta_colgaap]'";
			$query = mysql_query($sql,$conexion);
			$arrayResul['cuenta_niif']=mysql_result($query,0,'cuenta_niif');

			if ($arrayResul['cuenta_niif']=='') {		// SINO EXISTE LA CUENTA NIIF
				rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
				return array('estado' => 'false','msj'=> "La cuenta $arrayResul[cuenta_colgaap] niif no existe en el sistema");
			}
		}

		$sql       = "SELECT id FROM puc_niif WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND cuenta='$arrayResul[cuenta_niif]'";
		$query     = mysql_query($sql,$conexion);
		$idPucNiif = mysql_result($query,0,'id');

		if ($idPuc==0) {
			rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
			return array('estado' => 'false','msj'=> "La cuenta $arrayResul[cuenta_colgaap] colgaap no existe en el sistema");
		}
		if ($idPucNiif==0) {
			rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
			return array('estado' => 'false','msj'=> "La cuenta $arrayResul[cuenta_colgaap] niif no existe en el sistema");
		}

		// VERIFICAR SI TIENE CENTRO DE COSTOS Y SI TIENE, VERIFICAR LA CUENTA QUE SEA 4 -5 - 6, Y SI PASA, VALIDAR QUE EL CENTRO DE COSTOS EXISTA
		if ($arrayResul['codigo_centro_costos']>0) {

		  	if(substr($arrayResul['cuenta_colgaap'], 0,1)=='4' || substr($arrayResul['cuenta_colgaap'], 0,1)=='5' || substr($arrayResul['cuenta_colgaap'], 0,1)=='6') {
				$sql   = "SELECT COUNT(id) AS cont,id,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND codigo='$arrayResul[codigo_centro_costos]'";
				$query = mysql_query($sql,$conexion);
				$cont                 = mysql_result($query,0,'cont');
				$id_centro_costos     = mysql_result($query,0,'id');
				$nombre_centro_costos = mysql_result($query,0,'nombre');

				if ($cont==0) {
					rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
					return array('estado' => 'false','msj'=> "El centro de costos para La cuenta $arrayResul[cuenta_colgaap] no existe en el sistema");
				}
		  	}
			else{ $id_centro_costos=0; }

		}
		else{ $id_centro_costos=0; }

    	$valueInsertCuentasColgaap .= "('$id_documento',
										'$consecutivo_documento',
										'$tipo_documento',
										'$tipo_documento_extendido',
										'".$debito."',
										'".$credito."',
										'".$arrayResul['cuenta_colgaap']."',
										'$id_sucursal',
										'$id_tercero',
										'$arrayWs[id_empresa]',
										'$arrayWs[fecha_documento]',
										'$id_documento',
										'$tipo_documento',
										'$consecutivo_documento',
										'$id_centro_costos'
										),";

		$valueInsertCuentasNiif .= "('$id_documento',
										'$consecutivo_documento',
										'$tipo_documento',
										'$tipo_documento_extendido',
										'".$debito."',
										'".$credito."',
										'".$arrayResul['cuenta_niif']."',
										'$id_sucursal',
										'$id_tercero',
										'$arrayWs[id_empresa]',
										'$arrayWs[fecha_documento]',
										'$id_documento',
										'$tipo_documento',
										'$consecutivo_documento',
										'$id_centro_costos'
										),";
		// CUENTA DE PAGO
		$valor_cuenta_pago += ($arrayResul['cuenta_colgaap']==$arrayWs['cuenta_pago_colgaap'])? (($debito>$credito)? $debito : $credito) : 0;
    }

    $saldoTotal=$acumuladoDebito - $acumuladoCredito;
    if ($saldoTotal!=0){
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
    	return array('estado' => 'false','msj'=> "la suma de lo saldos de las cuentas no son iguales!");
    }

	$valueInsertCuentasColgaap = substr($valueInsertCuentasColgaap, 0, -1);
	$valueInsertCuentasNiif    = substr($valueInsertCuentasNiif, 0, -1);
    if ($valueInsertCuentasColgaap =='' || $valueInsertCuentasColgaap =='') {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
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
								numero_documento_cruce,
								id_centro_costos)
							VALUES $valueInsertCuentasColgaap";
	$queryInsert = mysql_query($sqlInsert,$conexion);
	if (!$queryInsert ) {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
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
						numero_documento_cruce,
						id_centro_costos)
					VALUES $valueInsertCuentasNiif";
	$queryInsert = mysql_query($sqlInsert,$conexion);
	if (!$queryInsert ) {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
    	return array('estado' => 'false','msj'=> "no se insertaron los asientos colgaap!");
    }

    // SI SE INSERTO TODO, ACTUALIZAR LA NOTA PARA GENERAR EL CONSECUTIVO
	$sql   = "UPDATE $tablaPrincipal SET estado=1,total_factura=$valor_cuenta_pago,total_factura_sin_abono=$valor_cuenta_pago WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
	$query = mysql_query($sql,$conexion);

    if (!$query) {
    	rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
    	return array('estado' => 'false','msj'=> "no se genero el consecutivo del documento");
    }

    // CONSULTAR EL CONSECUTIVO PARA ACTUALIZAR LOS ASIENTOS
    if ($tipo_documento=='FV') {

		$consecutivo = ($arrayWs['prefijo_documento']!='')? $arrayWs['prefijo_documento'].' '.$arrayWs['numero_documento'] : $arrayWs['numero_documento'] ;
    	// ACTUALIZAR EL NUMERO DE FACTURA COMPLETO
		$sql   = "UPDATE $tablaPrincipal SET prefijo='$arrayWs[prefijo_documento]',numero_factura='$arrayWs[numero_documento]',numero_factura_completo='$consecutivo' WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
		$query = mysql_query($sql,$conexion);
    	if (!$query) {
    		// rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
    		return array('estado' => 'false','msj'=> "no se actualizo el consecutivo del documento");
    	}
    }
    else{
		$sql   = "SELECT COUNT(id) AS cont,consecutivo FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
		$query = mysql_query($sql,$conexion);
		$cont        = mysql_result($query,0,'cont');
		$consecutivo = mysql_result($query,0,'consecutivo');

		// ACTUALIZAR EL NUMERO DE FACTURA COMPLETO
		$sql   = "UPDATE $tablaPrincipal SET prefijo_factura='$arrayWs[prefijo_documento]',numero_factura='$arrayWs[numero_documento]' WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND id=$id_documento";
		$query = mysql_query($sql,$conexion);
    	if (!$query) {
    		// rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
    		return array('estado' => 'false','msj'=> "no se actualizo el consecutivo del documento");
    	}
    }

    if ($cont==0) {
    	// rollback($tablaPrincipal,$id_documento,$arrayWs['id_empresa'],$conexion,$tipo_documento);
    	return array('estado' => 'false','msj'=> "no se pudo consultar el consecutivo del documento del documento! comuniquese con el administrador del sistema!");
    }


    // ACTUALIZAR LOS ASIENTOS CON LOS CONSECUTIVOS
	$sqlColgaap   = "UPDATE asientos_colgaap SET consecutivo_documento='$consecutivo', numero_documento_cruce='$consecutivo' WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND tipo_documento='$tipo_documento' AND id_documento=$id_documento";
	$queryColgaap = mysql_query($sqlColgaap,$conexion);

	$sqlNiif   = "UPDATE asientos_niif SET consecutivo_documento='$consecutivo', numero_documento_cruce='$consecutivo' WHERE activo=1 AND id_empresa=$arrayWs[id_empresa] AND tipo_documento='$tipo_documento' AND id_documento=$id_documento";
	$queryNiif = mysql_query($sqlNiif,$conexion);

    if (!$queryColgaap || !$queryNiif) {
    	return array('estado' => 'false','msj'=> "no se actualizo el consecutvo a los asientos contables! comuniquese con el administradr del sistema");
    }


	return array('estado' => 'true');

	// ROLLBAK DEL PROCESO DE INSERT
	function rollback($tablaPrincipal,$id_documento,$id_empresa,$conexion,$tipo_documento=''){

		$sql   = "DELETE FROM $tablaPrincipal WHERE id=$id_documento AND id_empresa=$arrayWs[id_empresa]";		// ELIMINAR LA CABECERA DEL DOCUMENTO GENERADO
		$query = mysql_query($sql,$conexion);

		$sql   = "DELETE FROM asientos_colgaap WHERE id_documento=$id_documento  AND tipo_documento='$tipo_documento' AND id_empresa=$id_empresa";		// ELIMINAR LOS ASIENTO COLGAAP
		$query = mysql_query($sql,$conexion);

		$sql   = "DELETE FROM asientos_niif WHERE id_documento=$id_documento  AND tipo_documento='$tipo_documento' AND id_empresa=$id_empresa";		// ELIMINAR LOS ASIENTO NIIF
		$query = mysql_query($sql,$conexion);
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