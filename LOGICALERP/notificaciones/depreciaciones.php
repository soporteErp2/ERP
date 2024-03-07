<?php

	date_default_timezone_set("America/Bogota");

	$fecha             = date("Y-m-d");
	$hora_notificacion = date("H:i");

	// $fecha = '2014-11-28';

	list($year, $mes, $dia) = explode("-", $fecha);

	$ultimoDiaMes = date("d",(mktime(0,0,0,$mes+1,1,$year)-1));

	$dia = 28;
	$ultimoDiaMes = 28;

	$orConsultaFecha = '';

	if($ultimoDiaMes == 28 && $dia == $ultimoDiaMes){ $orConsultaFecha .= " OR fecha_inicio_depreciacion LIKE '%29'"; }
	if($ultimoDiaMes <= 29 && $dia == $ultimoDiaMes){ $orConsultaFecha .= " OR fecha_inicio_depreciacion LIKE '%30'"; }
	if($ultimoDiaMes <= 30 && $dia == $ultimoDiaMes){ $orConsultaFecha .= " OR fecha_inicio_depreciacion LIKE '%31'"; }


	$sqlActivoFijo = "SELECT id,
							costo,
							id_empresa,
							id_sucursal,
							id_bodega,
							costo_sin_depreciar_anual,
							vida_util,
							cuenta_depreciacion,
							contrapartida_depreciacion,
							cuenta_depreciacion_niif,
							contrapartida_depreciacion_niif,
							id_cuenta_depreciacion,
							id_contrapartida_depreciacion,
							id_cuenta_depreciacion_niif, 
							id_contrapartida_depreciacion_niif, 
							metodo_depreciacion_colgaap,
							valor_salvamento,
							fecha_inicio_depreciacion
						FROM activos_fijos 
						WHERE estado=1 
							AND activo = 1
							AND cuenta_depreciacion > 99999 
							AND contrapartida_depreciacion > 99999
							AND costo_sin_depreciar_anual > 0
							AND fecha_inicio_depreciacion <= '$fecha'
							AND ( fecha_inicio_depreciacion LIKE '%$dia' $orConsultaFecha )
						ORDER BY id_empresa DESC, id_sucursal DESC";

	$queryActivoFijo = mysql_query($sqlActivoFijo, $link);
	if(!$queryActivoFijo){ echo '<script>alert("Error,\nNo se logro establecer comunicacion con el servidor, si el problema persiste favor comuniquese con el administrador del sistema.");</script>'; }
	
	$arrayEmpresaSucursalAsiento = array();
	$arrayNotasContables         = array();

	$contDepreciacion   = 0;					//CONT DEPRECIACIONES
	$contSumaDigitos    = 0;					//CONT METODO DEPRECIACION SUMA DIGITOS
	$contLineaRecta     = 0;					//CONT METODO DEPRECIACION LINEA RECTA
	$contReduccionSaldo = 0;					//CONT METODO DEPRECIACION REDUCCION DE SALDOS
	$acumIdSucursal     = 0;					//CONT CUANTAS NOTAS
	$acumIdEmpresa      = 0;					//CONT CUANTAS NOTAS
	
	$valueInsertNota = "";				//VALUE INSERT EN LA TABLA NOTA_CONTABLE_GENERAL
	$whereSelectNota = "";				//WHERE CONSULTA ID DE LAS NOTAS CON LOS RANDOMICOS
	$whereIdEmpresa  = "";				//WHERE CONSULTA ID DE LAS NOTAS CON LOS RANDOMICOS
	$valueInsertActivoFijoNota = "";			//VALUE INSERT TABLA ACTIVO_FIJO_DEPRECIACION
	
	$whereUpdateSaldo = "";
	while ($rowActivoFijo = mysql_fetch_array($queryActivoFijo)) {
		$contDepreciacion++;
		$depreciacionMes   = 0;

		$idActivoFijo = $rowActivoFijo['id'];
		$idEmpresa    = $rowActivoFijo['id_empresa'];
		$idSucursal   = $rowActivoFijo['id_sucursal'];
		$idBodega     = $rowActivoFijo['id_bodega'];
		$vida_util    = $rowActivoFijo['vida_util'];

		$cuentaDepreciacion            = $rowActivoFijo['cuenta_depreciacion'];
		$contrapartidaDepreciacion     = $rowActivoFijo['contrapartida_depreciacion'];

		$cuentaDepreciacionNiif        = $rowActivoFijo['cuenta_depreciacion_niif'];
		$contrapartidaDepreciacionNiif = $rowActivoFijo['contrapartida_depreciacion_niif'];

		$idCuentaDepreciacion          = $rowActivoFijo['id_cuenta_depreciacion'];
		$idContrapartidaDepreciacion   = $rowActivoFijo['id_contrapartida_depreciacion'];

		$idCuentaDepreciacionNiif        = $rowActivoFijo['id_cuenta_depreciacion_niif'];
		$idContrapartidaDepreciacionNiif = $rowActivoFijo['id_contrapartida_depreciacion_niif'];

		list($yearDb,$mesDb,$diaDb) = explode('-',$rowActivoFijo['fecha_inicio_depreciacion']);
		if($mes == $mesDb){ $whereUpdateSaldo .= "id_activo_fijo = $idActivoFijo OR "; }

		if($rowActivoFijo['metodo_depreciacion_colgaap'] == 'linea_recta'){									// DEPRECIACION LINEA RECTA
			$contLineaRecta++;
			$depreciacionMes = ROUND((($rowActivoFijo['costo'] / $rowActivoFijo['vida_util'])/12),2);
		}

		else if($rowActivoFijo['metodo_depreciacion_colgaap'] == 'reduccion_saldos'){						// DEPRECIACION REDUCCION DE SALDOS
			$contReduccionSaldo++; 
			$tasaDepreciacion = 1-(POW(
										($rowActivoFijo['valor_salvamento']/$rowActivoFijo['costo']),(1/$vida_util)
									));
			
			$depreciacionMes = ROUND(($rowActivoFijo['costo_sin_depreciar_anual'] * $tasaDepreciacion)/12,2); 
		}
		else if($rowActivoFijo['metodo_depreciacion_colgaap'] == 'suma_digitos_year') { 					// DEPRECIACION SUMA DE DIGITOS DEL AÑO
			$contSumaDigitos++;
			$fecha1          = new DateTime($fecha." 24:00:00");
			$fecha2          = new DateTime($rowActivoFijo['fecha_inicio_depreciacion']." 24:00:00");
			$diferenciaFecha = $fecha1->diff($fecha2);
			//printf('%d años, %d meses, %d días, %d horas, %d minutos', $diferenciaFecha->y, $diferenciaFecha->m, $diferenciaFecha->d, $diferenciaFecha->h, $diferenciaFecha->i);

			//list($yearDb,$mesDb,$diaDb) = explode('-',$rowActivoFijo['fecha_inicio_depreciacion']);
			if($mes == $mesDb){ $diferenciaFecha->y = $diferenciaFecha->y - 1; }

			$sumaDigitos     = ROUND(($vida_util*(($vida_util+1)/2)),2);
			$factor          = ($vida_util - $diferenciaFecha->y) / $sumaDigitos;
			$depreciacionMes = ROUND(($rowActivoFijo['costo'] * $factor)/12,2);
		}

		//ID_EMPRESA/ID_SUCURSAL/CUENTA_COLGAAP/CUENTA NIIF

		if(isset($arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$cuentaDepreciacion][$cuentaDepreciacionNiif]['valor'])){
			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$cuentaDepreciacion][$cuentaDepreciacionNiif]['valor'] += $depreciacionMes;
			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$contrapartidaDepreciacion][$contrapartidaDepreciacionNiif]['valor'] += $depreciacionMes;
		}
		else{
			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$cuentaDepreciacion][$cuentaDepreciacionNiif]['valor'] = $depreciacionMes;
			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$contrapartidaDepreciacion][$contrapartidaDepreciacionNiif]['valor'] = $depreciacionMes;

			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$cuentaDepreciacion][$cuentaDepreciacionNiif]['caracter'] = 'credito';
			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$contrapartidaDepreciacion][$contrapartidaDepreciacionNiif]['caracter'] = 'debito';

			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$cuentaDepreciacion][$cuentaDepreciacionNiif]['id_cuenta'] = $idCuentaDepreciacion;
			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$contrapartidaDepreciacion][$contrapartidaDepreciacionNiif]['id_cuenta'] = $idContrapartidaDepreciacion;

			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$cuentaDepreciacion][$cuentaDepreciacionNiif]['id_cuenta_niif'] = $idCuentaDepreciacionNiif;
			$arrayEmpresaSucursalAsiento[$idEmpresa][$idSucursal][$contrapartidaDepreciacion][$contrapartidaDepreciacionNiif]['id_cuenta_niif'] = $idContrapartidaDepreciacionNiif;
		}

		if($acumIdSucursal != $idSucursal){												// INSERT NOTAS CON RANDOMICO
			$acumIdSucursal = $idSucursal;
			$randomNota     = responseUnicoRanomico();

			$arrayNotasContables[$idSucursal] = $randomNota;

			$whereSelectNota .= ($whereSelectNota == "")? "random = '$randomNota'": " OR random = '$randomNota'";
			$valueInsertNota .= "('$randomNota',
									'$idEmpresa',
									'$idSucursal',
									'id_tipo_nota_".$idEmpresa."',
									'NOTA INTERNA',
									NOW(),
									NOW(),
									NOW(),
									'true',
									'NOTA DEPRECIACION',
									1
								),";
		}

		if($acumIdEmpresa != $idEmpresa){
			$whereIdEmpresa .= ($whereIdEmpresa == "")? "id_empresa=$idEmpresa": " OR id_empresa=$idEmpresa";
			$acumIdEmpresa   = $idEmpresa;
		}
		
																						// INSERT ACTIVO FIJO DEPRECIACION
		$valueInsertActivoFijoNota .= "('$idActivoFijo',									
										'$randomNota',
										'$depreciacionMes',
										NOW(),
										NOW(),
										'$idEmpresa',
										'$idSucursal'
									),";
	}

	//REPLACE STRING ID_TIPO_NOTA_ID_EMPRESA POR EL NUMBER ID TIPO DE NOTA
	$sqlTipoNota   = "SELECT MIN(id) AS id, id_empresa FROM tipo_nota_contable WHERE activo=1 AND ($whereIdEmpresa) GROUP BY id_empresa";
	$queryTipoNota = mysql_query($sqlTipoNota,$link);
	while ($rowTipoNota = mysql_fetch_array($queryTipoNota)) { $valueInsertNota = str_replace('id_tipo_nota_'.$rowTipoNota['id_empresa'], $rowTipoNota['id'], $valueInsertNota); }

	// INSERT NOTAS POR SUCURSAL
	$valueInsertNota  = substr($valueInsertNota, 0, -1);
	$sqlInsertNotas   = "INSERT INTO nota_contable_general (random,id_empresa,id_sucursal,id_tipo_nota,tercero,fecha_registro,fecha_nota,fecha_finalizacion,nota_auto,observacion,estado) VALUES $valueInsertNota";
	$queryInsertNotas = mysql_query($sqlInsertNotas,$link);
	if(!$queryInsertNotas){ exit; }

	$whereUpdateEstadoNota = "";

	// CONSULTA DE NOTAS PARA TRAER LOS RESPECTIVOS ID
	$sqlSelectNotas   = "SELECT id,id_sucursal,random,consecutivo FROM nota_contable_general WHERE activo=1 AND estado=1 AND ($whereSelectNota)";
	$querySelectNotas = mysql_query($sqlSelectNotas,$link);
	while ($rowNota = mysql_fetch_array($querySelectNotas)) {
		$idNotaBD     = $rowNota['id'];
		$randomBD     = $rowNota['random'];
		$idSucursalBD = $rowNota['id_sucursal'];

		$arrayIdNota[$idSucursalBD]          = $idNotaBD;
		$arrayConsecutivoNota[$idSucursalBD] = $rowNota['consecutivo'];

		$whereUpdateEstadoNota .= ($whereUpdateEstadoNota == "")? "id=$idNotaBD ": "OR id=$idNotaBD ";

		$valueInsertActivoFijoNota = str_replace($randomBD, $idNotaBD, $valueInsertActivoFijoNota);		// REPLACE RANDOMICO POR ID NOTA
	}

	$tablaDebug              = '<div style="float:left; width:80px;">Debito</div><div style="float:left; width:80px;">Credito</div><div style="float:left; width:80px;">PUC</div><br>';
	$valueInsertNiif         = "";
	$valueInsertAsientos     = "";
	$valueInsertCuentasNotas = "";
	
	$idNotaContable = 0;
	$totalDebito    = 0;
	$totalCredito   = 0;
	if($contDepreciacion > 0){
		foreach ($arrayEmpresaSucursalAsiento AS $idEmpresa => $arraySucursalAsiento){					// CICLO EMPRESA
			$idNotaContable = 0;

			foreach ($arraySucursalAsiento AS $idSucursal => $arrayAsiento){							// CICLO SUCURSAL
				$idNotaContable  = $arrayIdNota[$idSucursal];
				$consecutivoNota = $arrayConsecutivoNota[$idSucursal];

				if($idNotaContable == 0 || $idNotaContable == ''){ continue; }

				foreach ($arrayAsiento AS $cuentaColgaap => $arrayCuentaColgaap){						// CICLO ASIENTO CONTABLE COLGAAP

					foreach ($arrayCuentaColgaap AS $cuentaNiif => $arrayCuenta){						// CICLO CUENTA NIIIF
						$saldoDebito  = ($arrayCuenta['caracter'] == 'debito')? $arrayCuenta['valor'] : 0;
						$saldoCredito = ($arrayCuenta['caracter'] == 'credito')? $arrayCuenta['valor'] : 0;

						$totalDebito  += $saldoDebito;
						$totalCredito += $saldoCredito;

						//VALUE ASIENTOS COLGAAP
						$valueInsertAsientos .= "('$idNotaContable',
												'$consecutivoNota',
												'NCG',
												'Nota contable General',
												NOW(),
												'$saldoDebito',
												'$saldoCredito',
												'$cuentaColgaap',
												'$idSucursal',
												'$idEmpresa'),";
						
						//VALUE ASIENTOS NIIF
						$valueInsertNiif .= "('$idNotaContable',
												'$consecutivoNota',
												'NCG',
												'Nota contable General',
												NOW(),
												'$saldoDebito',
												'$saldoCredito',
												'$cuentaNiif',
												'$idSucursal',
												'$idEmpresa'),";
		
						$valueInsertCuentasNotas .= "('$idNotaContable',
													'".$arrayCuenta['id_cuenta']."',
													'".$arrayCuenta['id_cuenta_niif']."',
													'$saldoDebito',
													'$saldoCredito',
													'$idEmpresa'),";

						$tablaDebug  .='<div style="float:left; width:80px;">-'.$saldoDebito.'</div><div style="float:left; width:80px;">-'.$saldoCredito.'</div><div style="float:left; width:80px;">-'.$cuentaColgaap.'</div><br>';
					}
				}
			}
		}
	}

	$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">-'.$totalCredito.'</div><br>';
	//echo $tablaDebug; exit;

	// INSERT CUENTAS EN LAS NOTAS
	$valueInsertCuentasNotas = substr($valueInsertCuentasNotas, 0, -1);
	$sqlInsertNotas   = "INSERT INTO nota_contable_general_cuentas (id_nota_general,id_puc,id_niif,debe,haber,id_empresa) VALUES $valueInsertCuentasNotas";
	$queryInsertNotas = mysql_query($sqlInsertNotas,$link);

	// INSERT DEPRECIACION  ACTIVO FIJO
	$valueInsertActivoFijoNota = substr($valueInsertActivoFijoNota, 0, -1);
	$sqlInsertDepreciacionActivoFijo = "INSERT INTO activo_fijo_depreciaciones (id_activo_fijo,id_nota_contable,valor,fecha,hora,id_empresa,id_sucursal) VALUES $valueInsertActivoFijoNota";
	$queryInsertDepreciacionActivoFijo = mysql_query($sqlInsertDepreciacionActivoFijo,$link);

	if($whereUpdateSaldo != ''){																			// SI HAY DEPRECIACION ANUAL
		$whereUpdateSaldo = substr($whereUpdateSaldo, 0, -3);
		$sqlUpdateSaldo   = "UPDATE activos_fijos AS AF, (SELECT id_activo_fijo AS id,SUM(valor) AS valor_depreciado
															FROM activo_fijo_depreciaciones
															WHERE id_empresa='$idEmpresa'
																AND id_sucursal = '$idSucursal'
																AND $whereUpdateSaldo
															GROUP BY id_activo_fijo
														) AS AFD 
							SET AF.costo_sin_depreciar_anual = (AF.costo - AFD.valor_depreciado)
							WHERE AF.id=AFD.id
								AND AF.id_empresa='$idEmpresa'
								AND AF.id_sucursal='$idSucursal'
								AND AF.activo=1
								AND AF.estado=1";

		$queryUpdateSaldo = mysql_query($sqlUpdateSaldo,$link);
	}

	// INSERT ASIENTOS COLGAAP
	$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
	$sqlInsertAsientos   = "INSERT INTO asientos_colgaap (id_documento,consecutivo_documento,tipo_documento,tipo_documento_extendido,fecha,debe,haber,codigo_cuenta,id_sucursal,id_empresa) VALUES $valueInsertAsientos";
	$queryInsertAsientos = mysql_query($sqlInsertAsientos,$link);

	// INSERT ASIENTOS NIIF
	$valueInsertNiif = substr($valueInsertNiif, 0, -1);
	$sqlInsertNiif   = "INSERT INTO asientos_niif (id_documento,consecutivo_documento,tipo_documento,tipo_documento_extendido,fecha,debe,haber,codigo_cuenta,id_sucursal,id_empresa) VALUES $valueInsertNiif";
	$queryInsertNiif = mysql_query($sqlInsertNiif,$link);

	function responseUnicoRanomico(){												// FUNCTION RANDOMICO

        $random1 = time();            												// GENERA PRIMERA PARTE DEL ID UNICO
        $chars = array(
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
                'X', 'Y', 'Z', '1', '2', '3', '4', '5',
                '6', '7', '8', '9', '0' );

        $max_chars = count($chars) - 1;
        srand((double) microtime()*1000000);
        $random2 = '';
        for($i=0; $i < 6; $i++){ $random2 = $random2 . $chars[rand(0, $max_chars)]; }

    	$randomico = $random1.''.$random2; // ID UNICO
    	return $randomico;
	}


?>






