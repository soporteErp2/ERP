<?php
	// session_start();
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");
exit;
	$sql="SELECT
			id,
			codigo,
			descripcion,
			formula,
			nivel_formula,
			tipo_concepto,
			id_cuenta_colgaap,
			cuenta_colgaap,
			descripcion_cuenta_colgaap,
			id_cuenta_niif,
			cuenta_niif,
			descripcion_cuenta_niif,
			caracter,
			centro_costos,
			id_cuenta_contrapartida_colgaap,
			cuenta_contrapartida_colgaap,
			descripcion_cuenta_contrapartida_colgaap,
			id_cuenta_contrapartida_niif,
			cuenta_contrapartida_niif,
			descripcion_cuenta_contrapartida_niif,
			caracter_contrapartida,
			centro_costos_contrapartida,
			naturaleza,
			imprimir_volante,
			resta_dias,
			id_empresa
		FROM nomina_conceptos
		WHERE
			activo=1
		AND codigo = 'VC'
		AND(
			   id_empresa = 48
			OR id_empresa = 49
			OR id_empresa = 50
			OR id_empresa = 51
			OR id_empresa = 52
			OR id_empresa = 54
			OR id_empresa = 1
			OR id_empresa = 47
		)";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=mysql_fetch_array($query)) {
		$arrayConceptoVC[$row['id_empresa']]=array(
												'id'                                       => $row['id'],
												'codigo'                                   => $row['codigo'],
												'concepto'                                 => $row['descripcion'],
												'formula'                                  => $row['formula'],
												'formula_original'                         => $row['formula'],
												'nivel_formula'                            => $row['nivel_formula'],
												'valor_concepto'                           => 0,
												'insert'                                   => 'false',
												'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
												'cuenta_colgaap'                           => $row['cuenta_colgaap'],
												'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
												'id_cuenta_niif'                           => $row['id_cuenta_niif'],
												'cuenta_niif'                              => $row['cuenta_niif'],
												'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
												'caracter'                                 => $row['caracter'],
												'centro_costos'                            => $row['centro_costos'],
												'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
												'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
												'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
												'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
												'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
												'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
												'caracter_contrapartida'                   => $row['caracter_contrapartida'],
												'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
												'naturaleza'                               => $row['naturaleza'],
												'imprimir_volante'                         => $row['imprimir_volante'],
												'resta_dias'                               => $row['resta_dias'],
												);
	}

	$whereIdPlanillas = '
						id_planilla=344
						OR id_planilla=345
						OR id_planilla=346
						OR id_planilla=347
						OR id_planilla=348
						OR id_planilla=349
						OR id_planilla=350
						OR id_planilla=351
						OR id_planilla=352
						OR id_planilla=353
						OR id_planilla=354
						OR id_planilla=355
						OR id_planilla=356
						OR id_planilla=357
						OR id_planilla=358
						OR id_planilla=362
						OR id_planilla=363
						OR id_planilla=364
						OR id_planilla=365
						OR id_planilla=366
						OR id_planilla=367
						OR id_planilla=368
						OR id_planilla=369
						OR id_planilla=370
						OR id_planilla=371
						OR id_planilla=375
						OR id_planilla=376
						OR id_planilla=377
						OR id_planilla=378
						OR id_planilla=379
						OR id_planilla=380
						OR id_planilla=381
						OR id_planilla=382
						OR id_planilla=383
						OR id_planilla=384
						OR id_planilla=385
						OR id_planilla=386
						OR id_planilla=387
						OR id_planilla=388
						OR id_planilla=389
						OR id_planilla=392
						OR id_planilla=393
						OR id_planilla=397
						OR id_planilla=402
						OR id_planilla=403
						OR id_planilla=405
						OR id_planilla=406
						OR id_planilla=407
						OR id_planilla=408
						OR id_planilla=409
						OR id_planilla=410
						OR id_planilla=413
						OR id_planilla=414
						OR id_planilla=415
						OR id_planilla=419
						OR id_planilla=420
						OR id_planilla=421
						OR id_planilla=422
						OR id_planilla=423
						OR id_planilla=424
						OR id_planilla=425
						OR id_planilla=427
						OR id_planilla=428
						OR id_planilla=429
						OR id_planilla=430
						OR id_planilla=431
						OR id_planilla=432
						OR id_planilla=433
						OR id_planilla=434
						OR id_planilla=438
						';

	$whereIdPlanillasP = str_replace('id_planilla', 'id', $whereIdPlanillas);

	echo$sql="SELECT
			id_planilla,
			id_empleado,
			documento_empleado,
			nombre_empleado,
			id_contrato,
			dias_laborados_empleado
		FROM nomina_planillas_empleados
		WHERE activo=1
		AND ($whereIdPlanillas) ";

	// exit;

	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$whereIdEmpleados.=($whereIdEmpleados=='')? 'id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
		$arrayEmpleados[ $row['id_planilla'] ][$row['id_empleado'] ] = array(
																			'id_empleado'             => $row['id_empleado'],
																			'documento_empleado'      => $row['documento_empleado'],
																			'nombre_empleado'         => $row['nombre_empleado'],
																			'id_contrato'             => $row['id_contrato'],
																			'dias_laborados_empleado' => $row['dias_laborados_empleado'],
																			);
	}


	//CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
	$sql   = "SELECT
					id,
					id_empleado,
					tipo_documento_empleado,
					documento_empleado,
					nombre_empleado,
					numero_contrato,
					salario_basico,
					id_centro_costos,
					fecha_inicio_nomina,
					id_grupo_trabajo,
					valor_nivel_riesgo_laboral,
					IF(fecha_fin_contrato <= '$fecha', 'Si', 'No') AS terminar_contrato,
					id_sucursal,
					id_empresa
				FROM empleados_contratos
				WHERE activo=1 AND ($whereIdEmpleados)";
	$query = mysql_query($sql,$link);

	// $valueInsertEmpleados  = '';
	$whereIdEmpleados  = '';
	// $whereDeleteEmpleados  = '';
	$whereId_grupo_trabajo = '';

	while ($row=mysql_fetch_array($query)) {
		$whereIdEmpleados  .= ($whereIdEmpleados=='')? ' id='.$row['id_empleado'] : ' OR id='.$row['id_empleado'] ;
		// $whereDeleteEmpleados  .= ($whereDeleteEmpleados=='')? ' id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
		$whereId_grupo_trabajo .= ($whereId_grupo_trabajo=='')? ' id_grupo_trabajo='.$row['id_grupo_trabajo'] : ' OR id_grupo_trabajo='.$row['id_grupo_trabajo'] ;

		// $arrayEmpleados[$row['id_empleado']]         = $row['id_grupo_trabajo'];
		$arrayEmpleadosInfo[$row['id_empresa']][$row['id_empleado']]  = array(
																				'salario_basico'             => $row['salario_basico'],
																				'id_grupo_trabajo'           => $row['id_grupo_trabajo'],
																				'id_contrato'                => $row['id'],
																				'id_sucursal'                => $row['id_sucursal'],
																				'valor_nivel_riesgo_laboral' => $row['valor_nivel_riesgo_laboral'],
																				'id_centro_costos'           => $row['id_centro_costos']
																			);

	}

	// CREAR ARRAY CON LOS ID TERCERO DE CADA EMPLEADO
	$sql="SELECT id,id_tercero FROM empleados WHERE activo=1 AND ($whereIdEmpleados) ";
	$query=$mysql->query($sql,$mysql->link);
	while ( $row=mysql_fetch_array($query) ) {

		$arrayIdTercero[$row['id']] = $row['id_tercero'];

	}

	// print_r($arrayEmpleadosInfo);
	// CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
	$sql   = "SELECT id_concepto,
					nivel_formula,
					formula,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					id_grupo_trabajo,
					id_empresa
				FROM nomina_conceptos_grupos_trabajo
				WHERE activo=1
					AND id_empresa=$id_empresa
					AND ($whereId_grupo_trabajo)";
	$query = mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$id               = $row['id_concepto'];
		$nivel_formula    = $row['nivel_formula'];
		$id_grupo_trabajo = $row['id_grupo_trabajo'];
		$arrayGruposTrabajo[$row['id_empresa']][$id_grupo_trabajo]=array(
																		'formula'                                  => $row['formula'],
																		'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
																		'cuenta_colgaap'                           => $row['cuenta_colgaap'],
																		'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
																		'id_cuenta_niif'                           => $row['id_cuenta_niif'],
																		'cuenta_niif'                              => $row['cuenta_niif'],
																		'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
																		'caracter'                                 => $row['caracter'],
																		'centro_costos'                            => $row['centro_costos'],
																		'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
																		'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
																		'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
																		'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
																		'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
																		'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
																		'caracter_contrapartida'                   => $row['caracter_contrapartida'],
																		'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
																		);
	}

	$sql   = "SELECT
					id,
					consecutivo,
					fecha_inicio,
					fecha_final,
					usuario,
					id_empresa,
					fecha_documento,
					CASE id_empresa
						WHEN 48 THEN 'LOGICALSOFT S.A.S.'
						WHEN 49 THEN 'MINDZ SAS'
						WHEN 50 THEN 'TUNEDWORDS S.A.S.'
						WHEN 51 THEN 'SOLUCIONES INNOVADORES DE COLOMBIA S.A.S.'
						WHEN 52 THEN 'FACTOR EVENTOS Y PRODUCCIONES S.A.S.'
						WHEN 53 THEN 'CONTROL BINARIO '
						ELSE 'Sin Empresa'
					END AS 'empresa'

				FROM
					nomina_planillas
				WHERE
					activo = 1
				AND estado = 1
				AND fecha_inicio >= '2015-09-01'
				AND ($whereIdPlanillasP)
				AND (
					   id_empresa = 48
					OR id_empresa = 49
					OR id_empresa = 50
					OR id_empresa = 51
					OR id_empresa = 52
					OR id_empresa = 54
					OR id_empresa = 1
					OR id_empresa = 47
				)
				ORDER BY id_empresa";

	$query=$mysql->query($sql,$link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayPlanillas[$row['id']]= array(
											'consecutivo'     =>$row['consecutivo'],
											'fecha_inicio'    =>$row['fecha_inicio'],
											'fecha_documento' =>$row['fecha_documento'],
											'fecha_final'     =>$row['fecha_final'],
											'usuario'         =>$row['usuario'],
											'id_empresa'      =>$row['id_empresa'],
											'empresa'         =>$row['empresa'],
											);

	}

	// foreach ($arrayPlanillas as $id_planilla => $value) {
	// 	# code...
	// }

	// RECORRER LOS EMPLEADOS
	foreach ($arrayEmpleadosInfo as $id_empresa => $arrayEmpleadosInfoResul) {

		foreach ($arrayEmpleadosInfoResul as $id_empleado => $arrayEmpleadosInfoResul2) {

			$arrayEmpleadosConceptos[$id_empleado] = $arrayConceptoVC[$id_empresa];

			// RECORRER LOS GRUPOS DE TRABAJO
			foreach ($arrayGruposTrabajo[$id_empresa] as $id_grupo_trabajo => $arrayGruposTrabajoResul) {

				$arrayEmpleadosConceptos[$id_empleado]['formula']                                  = ($formula=='')? $arrayEmpleadosConceptos[$nivel_formula][$id_concepto]['formula'] : $formula;
				$arrayEmpleadosConceptos[$id_empleado]['formula_original']                         = ($formula=='')? $arrayEmpleadosConceptos[$nivel_formula][$id_concepto]['formula_original'] : $formula;
				$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_colgaap']                        = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_colgaap'];
				$arrayEmpleadosConceptos[$id_empleado]['cuenta_colgaap']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_colgaap'];
				$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_colgaap']               = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap'];
				$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_niif']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_niif'];
				$arrayEmpleadosConceptos[$id_empleado]['cuenta_niif']                              = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_niif'];
				$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_niif']                  = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_niif'];
				$arrayEmpleadosConceptos[$id_empleado]['caracter']                                 = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['caracter'];
				$arrayEmpleadosConceptos[$id_empleado]['centro_costos']                            = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['centro_costos'];
				$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_contrapartida_colgaap']          = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap'];
				$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_colgaap']             = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap'];
				$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_contrapartida_colgaap'] = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap'];
				$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_contrapartida_niif']             = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif'];
				$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_niif']                = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_contrapartida_niif'];
				$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_contrapartida_niif']    = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif'];
				$arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']                   = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['caracter_contrapartida'];
				$arrayEmpleadosConceptos[$id_empleado]['centro_costos_contrapartida']              = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['centro_costos_contrapartida'];

			}
		}
	}

	// print_r($arrayEmpleadosConceptos);



	// AND (codigo_concepto='DS' OR codigo_concepto='AP' OR id_planilla='PNR' OR id_planilla='RN' OR id_planilla='CM' OR id_planilla='MT')
	$sql=" SELECT
				id,
				id_planilla,
				id_empleado,
				id_concepto,
				codigo_concepto,
				concepto,
				valor_concepto,
				id_empresa
			FROM
				nomina_planillas_empleados_conceptos
			WHERE
				activo = 1

			AND ($whereIdPlanillas)
			AND (
					id_empresa    = 48
					OR id_empresa = 49
					OR id_empresa = 50
					OR id_empresa = 51
					OR id_empresa = 52
					OR id_empresa = 54
					OR id_empresa = 1
					OR id_empresa = 47
				)
			ORDER BY
				id_empresa";
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query) ){
		$arrayConceptosEmpleado[$row['id_planilla']][$row['id_empleado']][$row['codigo_concepto']] = $row['valor_concepto'];
	}

	// print_r($arrayConceptosEmpleado);
	$arrayEmpleadosExepciones['344']['299'] = '299';
	$arrayEmpleadosExepciones['345']['418'] = '418';
	$arrayEmpleadosExepciones['346']['419'] = '419';
	$arrayEmpleadosExepciones['347']['420'] = '420';
	$arrayEmpleadosExepciones['348']['421'] = '421';
	$arrayEmpleadosExepciones['349']['422'] = '422';
	$arrayEmpleadosExepciones['350']['296'] = '296';
	$arrayEmpleadosExepciones['351']['423'] = '423';
	$arrayEmpleadosExepciones['353']['421'] = '421';
	$arrayEmpleadosExepciones['354']['299'] = '299';
	$arrayEmpleadosExepciones['355']['318'] = '318';
	$arrayEmpleadosExepciones['356']['424'] = '424';
	$arrayEmpleadosExepciones['357']['425'] = '425';
	$arrayEmpleadosExepciones['378']['430'] = '430';
	$arrayEmpleadosExepciones['379']['431'] = '431';
	$arrayEmpleadosExepciones['380']['297'] = '297';
	$arrayEmpleadosExepciones['424']['440'] = '440';
	$arrayEmpleadosExepciones['425']['441'] = '441';
	$arrayEmpleadosExepciones['427']['297'] = '297';
	$arrayEmpleadosExepciones['428']['440'] = '440';
	$arrayEmpleadosExepciones['429']['443'] = '443';
	$arrayEmpleadosExepciones['430']['440'] = '440';
	$arrayEmpleadosExepciones['431']['440'] = '440';
	$arrayEmpleadosExepciones['432']['441'] = '441';
	$arrayEmpleadosExepciones['433']['441'] = '441';
	$arrayEmpleadosExepciones['434']['441'] = '441';
	$arrayEmpleadosExepciones['438']['91']  = '91';
	$arrayEmpleadosExepciones['438']['96']  = '96';
	$arrayEmpleadosExepciones['438']['99']  = '99';
	$arrayEmpleadosExepciones['438']['100'] = '100';
	$arrayEmpleadosExepciones['438']['101'] = '101';
	$arrayEmpleadosExepciones['438']['102'] = '102';
	$arrayEmpleadosExepciones['438']['103'] = '103';
	$arrayEmpleadosExepciones['438']['127'] = '127';
	$arrayEmpleadosExepciones['438']['128'] = '128';
	$arrayEmpleadosExepciones['438']['289'] = '289';
	$arrayEmpleadosExepciones['438']['432'] = '432';

/*
	$arrayEmpleadosExepciones['344']['299'] = '299';
	$arrayEmpleadosExepciones['345']['418'] = '418';
	$arrayEmpleadosExepciones['346']['419'] = '419';
	$arrayEmpleadosExepciones['347']['420'] = '420';
	$arrayEmpleadosExepciones['348']['421'] = '421';
	$arrayEmpleadosExepciones['349']['422'] = '422';
	$arrayEmpleadosExepciones['350']['296'] = '296';
	$arrayEmpleadosExepciones['351']['423'] = '423';
	$arrayEmpleadosExepciones['352']['238'] = '238';
	$arrayEmpleadosExepciones['353']['421'] = '421';
	$arrayEmpleadosExepciones['354']['299'] = '299';
	$arrayEmpleadosExepciones['355']['318'] = '318';
	$arrayEmpleadosExepciones['356']['424'] = '424';
	$arrayEmpleadosExepciones['357']['425'] = '425';
	$arrayEmpleadosExepciones['358']['91']  = '91';
	$arrayEmpleadosExepciones['358']['96']  = '96';
	$arrayEmpleadosExepciones['358']['99']  = '99';
	$arrayEmpleadosExepciones['358']['100'] = '100';
	$arrayEmpleadosExepciones['358']['101'] = '101';
	$arrayEmpleadosExepciones['358']['102'] = '102';
	$arrayEmpleadosExepciones['358']['103'] = '103';
	$arrayEmpleadosExepciones['358']['127'] = '127';
	$arrayEmpleadosExepciones['358']['128'] = '128';
	$arrayEmpleadosExepciones['358']['289'] = '289';
	$arrayEmpleadosExepciones['362']['45']  = '45';
	$arrayEmpleadosExepciones['362']['59']  = '59';
	$arrayEmpleadosExepciones['362']['63']  = '63';
	$arrayEmpleadosExepciones['362']['67']  = '67';
	$arrayEmpleadosExepciones['362']['68']  = '68';
	$arrayEmpleadosExepciones['362']['70']  = '70';
	$arrayEmpleadosExepciones['362']['71']  = '71';
	$arrayEmpleadosExepciones['362']['72']  = '72';
	$arrayEmpleadosExepciones['362']['73']  = '73';
	$arrayEmpleadosExepciones['362']['74']  = '74';
	$arrayEmpleadosExepciones['362']['77']  = '77';
	$arrayEmpleadosExepciones['362']['81']  = '81';
	$arrayEmpleadosExepciones['362']['83']  = '83';
	$arrayEmpleadosExepciones['362']['105'] = '105';
	$arrayEmpleadosExepciones['362']['106'] = '106';
	$arrayEmpleadosExepciones['362']['109'] = '109';
	$arrayEmpleadosExepciones['362']['110'] = '110';
	$arrayEmpleadosExepciones['362']['111'] = '111';
	$arrayEmpleadosExepciones['362']['112'] = '112';
	$arrayEmpleadosExepciones['362']['113'] = '113';
	$arrayEmpleadosExepciones['362']['114'] = '114';
	$arrayEmpleadosExepciones['362']['115'] = '115';
	$arrayEmpleadosExepciones['362']['116'] = '116';
	$arrayEmpleadosExepciones['362']['120'] = '120';
	$arrayEmpleadosExepciones['362']['121'] = '121';
	$arrayEmpleadosExepciones['362']['122'] = '122';
	$arrayEmpleadosExepciones['362']['123'] = '123';
	$arrayEmpleadosExepciones['362']['124'] = '124';
	$arrayEmpleadosExepciones['362']['125'] = '125';
	$arrayEmpleadosExepciones['362']['129'] = '129';
	$arrayEmpleadosExepciones['362']['130'] = '130';
	$arrayEmpleadosExepciones['362']['131'] = '131';
	$arrayEmpleadosExepciones['362']['132'] = '132';
	$arrayEmpleadosExepciones['362']['133'] = '133';
	$arrayEmpleadosExepciones['362']['135'] = '135';
	$arrayEmpleadosExepciones['362']['140'] = '140';
	$arrayEmpleadosExepciones['362']['141'] = '141';
	$arrayEmpleadosExepciones['362']['145'] = '145';
	$arrayEmpleadosExepciones['362']['146'] = '146';
	$arrayEmpleadosExepciones['362']['147'] = '147';
	$arrayEmpleadosExepciones['362']['148'] = '148';
	$arrayEmpleadosExepciones['362']['150'] = '150';
	$arrayEmpleadosExepciones['362']['152'] = '152';
	$arrayEmpleadosExepciones['362']['153'] = '153';
	$arrayEmpleadosExepciones['362']['154'] = '154';
	$arrayEmpleadosExepciones['362']['156'] = '156';
	$arrayEmpleadosExepciones['362']['157'] = '157';
	$arrayEmpleadosExepciones['362']['158'] = '158';
	$arrayEmpleadosExepciones['362']['161'] = '161';
	$arrayEmpleadosExepciones['362']['162'] = '162';
	$arrayEmpleadosExepciones['362']['164'] = '164';
	$arrayEmpleadosExepciones['362']['165'] = '165';
	$arrayEmpleadosExepciones['362']['166'] = '166';
	$arrayEmpleadosExepciones['362']['167'] = '167';
	$arrayEmpleadosExepciones['362']['169'] = '169';
	$arrayEmpleadosExepciones['362']['265'] = '265';
	$arrayEmpleadosExepciones['362']['266'] = '266';
	$arrayEmpleadosExepciones['362']['274'] = '274';
	$arrayEmpleadosExepciones['362']['285'] = '285';
	$arrayEmpleadosExepciones['362']['290'] = '290';
	$arrayEmpleadosExepciones['362']['293'] = '293';
	$arrayEmpleadosExepciones['362']['294'] = '294';
	$arrayEmpleadosExepciones['362']['305'] = '305';
	$arrayEmpleadosExepciones['362']['307'] = '307';
	$arrayEmpleadosExepciones['362']['311'] = '311';
	$arrayEmpleadosExepciones['362']['313'] = '313';
	$arrayEmpleadosExepciones['362']['321'] = '321';
	$arrayEmpleadosExepciones['362']['322'] = '322';
	$arrayEmpleadosExepciones['362']['328'] = '328';
	$arrayEmpleadosExepciones['362']['330'] = '330';
	$arrayEmpleadosExepciones['362']['340'] = '340';
	$arrayEmpleadosExepciones['362']['341'] = '341';
	$arrayEmpleadosExepciones['362']['345'] = '345';
	$arrayEmpleadosExepciones['362']['347'] = '347';
	$arrayEmpleadosExepciones['362']['349'] = '349';
	$arrayEmpleadosExepciones['362']['350'] = '350';
	$arrayEmpleadosExepciones['362']['360'] = '360';
	$arrayEmpleadosExepciones['362']['365'] = '365';
	$arrayEmpleadosExepciones['362']['366'] = '366';
	$arrayEmpleadosExepciones['362']['384'] = '384';
	$arrayEmpleadosExepciones['362']['386'] = '386';
	$arrayEmpleadosExepciones['362']['388'] = '388';
	$arrayEmpleadosExepciones['362']['400'] = '400';
	$arrayEmpleadosExepciones['362']['413'] = '413';
	$arrayEmpleadosExepciones['362']['414'] = '414';
	$arrayEmpleadosExepciones['362']['417'] = '417';
	$arrayEmpleadosExepciones['362']['426'] = '426';
	$arrayEmpleadosExepciones['362']['427'] = '427';
	$arrayEmpleadosExepciones['363']['52'] = '52';
	$arrayEmpleadosExepciones['363']['53'] = '53';
	$arrayEmpleadosExepciones['363']['173'] = '173';
	$arrayEmpleadosExepciones['363']['174'] = '174';
	$arrayEmpleadosExepciones['363']['176'] = '176';
	$arrayEmpleadosExepciones['363']['178'] = '178';
	$arrayEmpleadosExepciones['363']['179'] = '179';
	$arrayEmpleadosExepciones['363']['182'] = '182';
	$arrayEmpleadosExepciones['363']['183'] = '183';
	$arrayEmpleadosExepciones['363']['184'] = '184';
	$arrayEmpleadosExepciones['363']['292'] = '292';
	$arrayEmpleadosExepciones['363']['344'] = '344';
	$arrayEmpleadosExepciones['364']['84'] = '84';
	$arrayEmpleadosExepciones['364']['175'] = '175';
	$arrayEmpleadosExepciones['364']['191'] = '191';
	$arrayEmpleadosExepciones['364']['196'] = '196';
	$arrayEmpleadosExepciones['364']['198'] = '198';
	$arrayEmpleadosExepciones['364']['199'] = '199';
	$arrayEmpleadosExepciones['364']['200'] = '200';
	$arrayEmpleadosExepciones['364']['280'] = '280';
	$arrayEmpleadosExepciones['364']['315'] = '315';
	$arrayEmpleadosExepciones['364']['316'] = '316';
	$arrayEmpleadosExepciones['365']['85'] = '85';
	$arrayEmpleadosExepciones['365']['186'] = '186';
	$arrayEmpleadosExepciones['365']['310'] = '310';
	$arrayEmpleadosExepciones['366']['54'] = '54';
	$arrayEmpleadosExepciones['366']['55'] = '55';
	$arrayEmpleadosExepciones['366']['56'] = '56';
	$arrayEmpleadosExepciones['366']['75'] = '75';
	$arrayEmpleadosExepciones['366']['88'] = '88';
	$arrayEmpleadosExepciones['366']['89'] = '89';
	$arrayEmpleadosExepciones['366']['187'] = '187';
	$arrayEmpleadosExepciones['366']['188'] = '188';
	$arrayEmpleadosExepciones['366']['189'] = '189';
	$arrayEmpleadosExepciones['366']['190'] = '190';
	$arrayEmpleadosExepciones['366']['192'] = '192';
	$arrayEmpleadosExepciones['366']['193'] = '193';
	$arrayEmpleadosExepciones['366']['194'] = '194';
	$arrayEmpleadosExepciones['366']['195'] = '195';
	$arrayEmpleadosExepciones['366']['197'] = '197';
	$arrayEmpleadosExepciones['366']['312'] = '312';
	$arrayEmpleadosExepciones['366']['324'] = '324';
	$arrayEmpleadosExepciones['366']['346'] = '346';
	$arrayEmpleadosExepciones['366']['428'] = '428';
	$arrayEmpleadosExepciones['366']['429'] = '429';
	$arrayEmpleadosExepciones['367']['12'] = '12';
	$arrayEmpleadosExepciones['367']['14'] = '14';
	$arrayEmpleadosExepciones['367']['15'] = '15';
	$arrayEmpleadosExepciones['367']['21'] = '21';
	$arrayEmpleadosExepciones['367']['23'] = '23';
	$arrayEmpleadosExepciones['367']['24'] = '24';
	$arrayEmpleadosExepciones['367']['25'] = '25';
	$arrayEmpleadosExepciones['367']['26'] = '26';
	$arrayEmpleadosExepciones['367']['27'] = '27';
	$arrayEmpleadosExepciones['367']['29'] = '29';
	$arrayEmpleadosExepciones['367']['30'] = '30';
	$arrayEmpleadosExepciones['367']['31'] = '31';
	$arrayEmpleadosExepciones['367']['57'] = '57';
	$arrayEmpleadosExepciones['367']['94'] = '94';
	$arrayEmpleadosExepciones['367']['95'] = '95';
	$arrayEmpleadosExepciones['367']['104'] = '104';
	$arrayEmpleadosExepciones['367']['177'] = '177';
	$arrayEmpleadosExepciones['367']['201'] = '201';
	$arrayEmpleadosExepciones['367']['202'] = '202';
	$arrayEmpleadosExepciones['367']['203'] = '203';
	$arrayEmpleadosExepciones['367']['204'] = '204';
	$arrayEmpleadosExepciones['367']['206'] = '206';
	$arrayEmpleadosExepciones['367']['207'] = '207';
	$arrayEmpleadosExepciones['367']['209'] = '209';
	$arrayEmpleadosExepciones['367']['210'] = '210';
	$arrayEmpleadosExepciones['367']['213'] = '213';
	$arrayEmpleadosExepciones['367']['214'] = '214';
	$arrayEmpleadosExepciones['367']['215'] = '215';
	$arrayEmpleadosExepciones['367']['216'] = '216';
	$arrayEmpleadosExepciones['367']['217'] = '217';
	$arrayEmpleadosExepciones['367']['218'] = '218';
	$arrayEmpleadosExepciones['367']['219'] = '219';
	$arrayEmpleadosExepciones['367']['220'] = '220';
	$arrayEmpleadosExepciones['367']['221'] = '221';
	$arrayEmpleadosExepciones['367']['222'] = '222';
	$arrayEmpleadosExepciones['367']['306'] = '306';
	$arrayEmpleadosExepciones['367']['332'] = '332';
	$arrayEmpleadosExepciones['367']['343'] = '343';
	$arrayEmpleadosExepciones['367']['411'] = '411';
	$arrayEmpleadosExepciones['367']['415'] = '415';
	$arrayEmpleadosExepciones['368']['37'] = '37';
	$arrayEmpleadosExepciones['368']['38'] = '38';
	$arrayEmpleadosExepciones['368']['39'] = '39';
	$arrayEmpleadosExepciones['368']['41'] = '41';
	$arrayEmpleadosExepciones['368']['230'] = '230';
	$arrayEmpleadosExepciones['368']['231'] = '231';
	$arrayEmpleadosExepciones['368']['233'] = '233';
	$arrayEmpleadosExepciones['368']['234'] = '234';
	$arrayEmpleadosExepciones['368']['235'] = '235';
	$arrayEmpleadosExepciones['368']['236'] = '236';
	$arrayEmpleadosExepciones['368']['237'] = '237';
	$arrayEmpleadosExepciones['368']['239'] = '239';
	$arrayEmpleadosExepciones['368']['240'] = '240';
	$arrayEmpleadosExepciones['368']['242'] = '242';
	$arrayEmpleadosExepciones['368']['263'] = '263';
	$arrayEmpleadosExepciones['368']['301'] = '301';
	$arrayEmpleadosExepciones['368']['304'] = '304';
	$arrayEmpleadosExepciones['368']['335'] = '335';
	$arrayEmpleadosExepciones['368']['342'] = '342';
	$arrayEmpleadosExepciones['368']['401'] = '401';
	$arrayEmpleadosExepciones['368']['402'] = '402';
	$arrayEmpleadosExepciones['368']['425'] = '425';
	$arrayEmpleadosExepciones['369']['33'] = '33';
	$arrayEmpleadosExepciones['369']['223'] = '223';
	$arrayEmpleadosExepciones['369']['224'] = '224';
	$arrayEmpleadosExepciones['369']['225'] = '225';
	$arrayEmpleadosExepciones['369']['226'] = '226';
	$arrayEmpleadosExepciones['369']['278'] = '278';
	$arrayEmpleadosExepciones['369']['303'] = '303';
	$arrayEmpleadosExepciones['369']['317'] = '317';
	$arrayEmpleadosExepciones['369']['339'] = '339';
	$arrayEmpleadosExepciones['369']['361'] = '361';
	$arrayEmpleadosExepciones['369']['383'] = '383';
	$arrayEmpleadosExepciones['370']['34'] = '34';
	$arrayEmpleadosExepciones['370']['35'] = '35';
	$arrayEmpleadosExepciones['370']['244'] = '244';
	$arrayEmpleadosExepciones['371']['250'] = '250';
	$arrayEmpleadosExepciones['371']['251'] = '251';
	$arrayEmpleadosExepciones['375']['92'] = '92';
	$arrayEmpleadosExepciones['375']['272'] = '272';
	$arrayEmpleadosExepciones['376']['245'] = '245';
	$arrayEmpleadosExepciones['376']['288'] = '288';
	$arrayEmpleadosExepciones['377']['247'] = '247';
	$arrayEmpleadosExepciones['377']['249'] = '249';
	$arrayEmpleadosExepciones['377']['385'] = '385';
	$arrayEmpleadosExepciones['378']['430'] = '430';
	$arrayEmpleadosExepciones['379']['431'] = '431';
	$arrayEmpleadosExepciones['380']['297'] = '297';
	$arrayEmpleadosExepciones['381']['45'] = '45';
	$arrayEmpleadosExepciones['381']['59'] = '59';
	$arrayEmpleadosExepciones['381']['63'] = '63';
	$arrayEmpleadosExepciones['381']['67'] = '67';
	$arrayEmpleadosExepciones['381']['68'] = '68';
	$arrayEmpleadosExepciones['381']['70'] = '70';
	$arrayEmpleadosExepciones['381']['71'] = '71';
	$arrayEmpleadosExepciones['381']['72'] = '72';
	$arrayEmpleadosExepciones['381']['73'] = '73';
	$arrayEmpleadosExepciones['381']['74'] = '74';
	$arrayEmpleadosExepciones['381']['77'] = '77';
	$arrayEmpleadosExepciones['381']['81'] = '81';
	$arrayEmpleadosExepciones['381']['83'] = '83';
	$arrayEmpleadosExepciones['381']['105'] = '105';
	$arrayEmpleadosExepciones['381']['106'] = '106';
	$arrayEmpleadosExepciones['381']['109'] = '109';
	$arrayEmpleadosExepciones['381']['110'] = '110';
	$arrayEmpleadosExepciones['381']['111'] = '111';
	$arrayEmpleadosExepciones['381']['112'] = '112';
	$arrayEmpleadosExepciones['381']['113'] = '113';
	$arrayEmpleadosExepciones['381']['114'] = '114';
	$arrayEmpleadosExepciones['381']['115'] = '115';
	$arrayEmpleadosExepciones['381']['116'] = '116';
	$arrayEmpleadosExepciones['381']['120'] = '120';
	$arrayEmpleadosExepciones['381']['121'] = '121';
	$arrayEmpleadosExepciones['381']['122'] = '122';
	$arrayEmpleadosExepciones['381']['123'] = '123';
	$arrayEmpleadosExepciones['381']['124'] = '124';
	$arrayEmpleadosExepciones['381']['125'] = '125';
	$arrayEmpleadosExepciones['381']['129'] = '129';
	$arrayEmpleadosExepciones['381']['130'] = '130';
	$arrayEmpleadosExepciones['381']['131'] = '131';
	$arrayEmpleadosExepciones['381']['132'] = '132';
	$arrayEmpleadosExepciones['381']['133'] = '133';
	$arrayEmpleadosExepciones['381']['135'] = '135';
	$arrayEmpleadosExepciones['381']['140'] = '140';
	$arrayEmpleadosExepciones['381']['141'] = '141';
	$arrayEmpleadosExepciones['381']['145'] = '145';
	$arrayEmpleadosExepciones['381']['146'] = '146';
	$arrayEmpleadosExepciones['381']['147'] = '147';
	$arrayEmpleadosExepciones['381']['148'] = '148';
	$arrayEmpleadosExepciones['381']['150'] = '150';
	$arrayEmpleadosExepciones['381']['152'] = '152';
	$arrayEmpleadosExepciones['381']['153'] = '153';
	$arrayEmpleadosExepciones['381']['154'] = '154';
	$arrayEmpleadosExepciones['381']['156'] = '156';
	$arrayEmpleadosExepciones['381']['157'] = '157';
	$arrayEmpleadosExepciones['381']['158'] = '158';
	$arrayEmpleadosExepciones['381']['161'] = '161';
	$arrayEmpleadosExepciones['381']['162'] = '162';
	$arrayEmpleadosExepciones['381']['164'] = '164';
	$arrayEmpleadosExepciones['381']['165'] = '165';
	$arrayEmpleadosExepciones['381']['166'] = '166';
	$arrayEmpleadosExepciones['381']['167'] = '167';
	$arrayEmpleadosExepciones['381']['169'] = '169';
	$arrayEmpleadosExepciones['381']['265'] = '265';
	$arrayEmpleadosExepciones['381']['266'] = '266';
	$arrayEmpleadosExepciones['381']['274'] = '274';
	$arrayEmpleadosExepciones['381']['290'] = '290';
	$arrayEmpleadosExepciones['381']['293'] = '293';
	$arrayEmpleadosExepciones['381']['294'] = '294';
	$arrayEmpleadosExepciones['381']['305'] = '305';
	$arrayEmpleadosExepciones['381']['307'] = '307';
	$arrayEmpleadosExepciones['381']['311'] = '311';
	$arrayEmpleadosExepciones['381']['313'] = '313';
	$arrayEmpleadosExepciones['381']['321'] = '321';
	$arrayEmpleadosExepciones['381']['322'] = '322';
	$arrayEmpleadosExepciones['381']['328'] = '328';
	$arrayEmpleadosExepciones['381']['340'] = '340';
	$arrayEmpleadosExepciones['381']['341'] = '341';
	$arrayEmpleadosExepciones['381']['345'] = '345';
	$arrayEmpleadosExepciones['381']['347'] = '347';
	$arrayEmpleadosExepciones['381']['350'] = '350';
	$arrayEmpleadosExepciones['381']['360'] = '360';
	$arrayEmpleadosExepciones['381']['365'] = '365';
	$arrayEmpleadosExepciones['381']['366'] = '366';
	$arrayEmpleadosExepciones['381']['384'] = '384';
	$arrayEmpleadosExepciones['381']['386'] = '386';
	$arrayEmpleadosExepciones['381']['388'] = '388';
	$arrayEmpleadosExepciones['381']['400'] = '400';
	$arrayEmpleadosExepciones['381']['413'] = '413';
	$arrayEmpleadosExepciones['381']['414'] = '414';
	$arrayEmpleadosExepciones['381']['417'] = '417';
	$arrayEmpleadosExepciones['381']['426'] = '426';
	$arrayEmpleadosExepciones['381']['427'] = '427';
	$arrayEmpleadosExepciones['381']['433'] = '433';
	$arrayEmpleadosExepciones['381']['434'] = '434';
	$arrayEmpleadosExepciones['381']['435'] = '435';
	$arrayEmpleadosExepciones['381']['436'] = '436';
	$arrayEmpleadosExepciones['382']['52'] = '52';
	$arrayEmpleadosExepciones['382']['53'] = '53';
	$arrayEmpleadosExepciones['382']['173'] = '173';
	$arrayEmpleadosExepciones['382']['174'] = '174';
	$arrayEmpleadosExepciones['382']['176'] = '176';
	$arrayEmpleadosExepciones['382']['178'] = '178';
	$arrayEmpleadosExepciones['382']['179'] = '179';
	$arrayEmpleadosExepciones['382']['182'] = '182';
	$arrayEmpleadosExepciones['382']['183'] = '183';
	$arrayEmpleadosExepciones['382']['184'] = '184';
	$arrayEmpleadosExepciones['382']['344'] = '344';
	$arrayEmpleadosExepciones['383']['84'] = '84';
	$arrayEmpleadosExepciones['383']['175'] = '175';
	$arrayEmpleadosExepciones['383']['191'] = '191';
	$arrayEmpleadosExepciones['383']['196'] = '196';
	$arrayEmpleadosExepciones['383']['198'] = '198';
	$arrayEmpleadosExepciones['383']['199'] = '199';
	$arrayEmpleadosExepciones['383']['200'] = '200';
	$arrayEmpleadosExepciones['383']['280'] = '280';
	$arrayEmpleadosExepciones['383']['315'] = '315';
	$arrayEmpleadosExepciones['383']['316'] = '316';
	$arrayEmpleadosExepciones['384']['54'] = '54';
	$arrayEmpleadosExepciones['384']['55'] = '55';
	$arrayEmpleadosExepciones['384']['56'] = '56';
	$arrayEmpleadosExepciones['384']['75'] = '75';
	$arrayEmpleadosExepciones['384']['88'] = '88';
	$arrayEmpleadosExepciones['384']['89'] = '89';
	$arrayEmpleadosExepciones['384']['187'] = '187';
	$arrayEmpleadosExepciones['384']['188'] = '188';
	$arrayEmpleadosExepciones['384']['189'] = '189';
	$arrayEmpleadosExepciones['384']['190'] = '190';
	$arrayEmpleadosExepciones['384']['192'] = '192';
	$arrayEmpleadosExepciones['384']['193'] = '193';
	$arrayEmpleadosExepciones['384']['195'] = '195';
	$arrayEmpleadosExepciones['384']['197'] = '197';
	$arrayEmpleadosExepciones['384']['312'] = '312';
	$arrayEmpleadosExepciones['384']['324'] = '324';
	$arrayEmpleadosExepciones['384']['346'] = '346';
	$arrayEmpleadosExepciones['384']['428'] = '428';
	$arrayEmpleadosExepciones['384']['429'] = '429';
	$arrayEmpleadosExepciones['385']['85'] = '85';
	$arrayEmpleadosExepciones['385']['186'] = '186';
	$arrayEmpleadosExepciones['385']['310'] = '310';
	$arrayEmpleadosExepciones['386']['12'] = '12';
	$arrayEmpleadosExepciones['386']['14'] = '14';
	$arrayEmpleadosExepciones['386']['15'] = '15';
	$arrayEmpleadosExepciones['386']['21'] = '21';
	$arrayEmpleadosExepciones['386']['23'] = '23';
	$arrayEmpleadosExepciones['386']['24'] = '24';
	$arrayEmpleadosExepciones['386']['25'] = '25';
	$arrayEmpleadosExepciones['386']['26'] = '26';
	$arrayEmpleadosExepciones['386']['27'] = '27';
	$arrayEmpleadosExepciones['386']['29'] = '29';
	$arrayEmpleadosExepciones['386']['30'] = '30';
	$arrayEmpleadosExepciones['386']['31'] = '31';
	$arrayEmpleadosExepciones['386']['57'] = '57';
	$arrayEmpleadosExepciones['386']['94'] = '94';
	$arrayEmpleadosExepciones['386']['95'] = '95';
	$arrayEmpleadosExepciones['386']['104'] = '104';
	$arrayEmpleadosExepciones['386']['177'] = '177';
	$arrayEmpleadosExepciones['386']['201'] = '201';
	$arrayEmpleadosExepciones['386']['202'] = '202';
	$arrayEmpleadosExepciones['386']['203'] = '203';
	$arrayEmpleadosExepciones['386']['204'] = '204';
	$arrayEmpleadosExepciones['386']['206'] = '206';
	$arrayEmpleadosExepciones['386']['207'] = '207';
	$arrayEmpleadosExepciones['386']['209'] = '209';
	$arrayEmpleadosExepciones['386']['210'] = '210';
	$arrayEmpleadosExepciones['386']['213'] = '213';
	$arrayEmpleadosExepciones['386']['214'] = '214';
	$arrayEmpleadosExepciones['386']['215'] = '215';
	$arrayEmpleadosExepciones['386']['216'] = '216';
	$arrayEmpleadosExepciones['386']['217'] = '217';
	$arrayEmpleadosExepciones['386']['218'] = '218';
	$arrayEmpleadosExepciones['386']['219'] = '219';
	$arrayEmpleadosExepciones['386']['220'] = '220';
	$arrayEmpleadosExepciones['386']['221'] = '221';
	$arrayEmpleadosExepciones['386']['222'] = '222';
	$arrayEmpleadosExepciones['386']['306'] = '306';
	$arrayEmpleadosExepciones['386']['332'] = '332';
	$arrayEmpleadosExepciones['386']['343'] = '343';
	$arrayEmpleadosExepciones['386']['411'] = '411';
	$arrayEmpleadosExepciones['386']['415'] = '415';
	$arrayEmpleadosExepciones['387']['37'] = '37';
	$arrayEmpleadosExepciones['387']['38'] = '38';
	$arrayEmpleadosExepciones['387']['39'] = '39';
	$arrayEmpleadosExepciones['387']['41'] = '41';
	$arrayEmpleadosExepciones['387']['230'] = '230';
	$arrayEmpleadosExepciones['387']['231'] = '231';
	$arrayEmpleadosExepciones['387']['233'] = '233';
	$arrayEmpleadosExepciones['387']['234'] = '234';
	$arrayEmpleadosExepciones['387']['235'] = '235';
	$arrayEmpleadosExepciones['387']['236'] = '236';
	$arrayEmpleadosExepciones['387']['237'] = '237';
	$arrayEmpleadosExepciones['387']['239'] = '239';
	$arrayEmpleadosExepciones['387']['240'] = '240';
	$arrayEmpleadosExepciones['387']['242'] = '242';
	$arrayEmpleadosExepciones['387']['263'] = '263';
	$arrayEmpleadosExepciones['387']['304'] = '304';
	$arrayEmpleadosExepciones['387']['335'] = '335';
	$arrayEmpleadosExepciones['387']['342'] = '342';
	$arrayEmpleadosExepciones['387']['401'] = '401';
	$arrayEmpleadosExepciones['387']['402'] = '402';
	$arrayEmpleadosExepciones['387']['425'] = '425';
	$arrayEmpleadosExepciones['388']['33'] = '33';
	$arrayEmpleadosExepciones['388']['223'] = '223';
	$arrayEmpleadosExepciones['388']['224'] = '224';
	$arrayEmpleadosExepciones['388']['225'] = '225';
	$arrayEmpleadosExepciones['388']['226'] = '226';
	$arrayEmpleadosExepciones['388']['278'] = '278';
	$arrayEmpleadosExepciones['388']['303'] = '303';
	$arrayEmpleadosExepciones['388']['317'] = '317';
	$arrayEmpleadosExepciones['388']['339'] = '339';
	$arrayEmpleadosExepciones['388']['361'] = '361';
	$arrayEmpleadosExepciones['388']['383'] = '383';
	$arrayEmpleadosExepciones['389']['34'] = '34';
	$arrayEmpleadosExepciones['389']['35'] = '35';
	$arrayEmpleadosExepciones['389']['244'] = '244';
	$arrayEmpleadosExepciones['392']['91'] = '91';
	$arrayEmpleadosExepciones['392']['96'] = '96';
	$arrayEmpleadosExepciones['392']['99'] = '99';
	$arrayEmpleadosExepciones['392']['100'] = '100';
	$arrayEmpleadosExepciones['392']['101'] = '101';
	$arrayEmpleadosExepciones['392']['102'] = '102';
	$arrayEmpleadosExepciones['392']['103'] = '103';
	$arrayEmpleadosExepciones['392']['127'] = '127';
	$arrayEmpleadosExepciones['392']['128'] = '128';
	$arrayEmpleadosExepciones['392']['289'] = '289';
	$arrayEmpleadosExepciones['392']['432'] = '432';
	$arrayEmpleadosExepciones['393']['250'] = '250';
	$arrayEmpleadosExepciones['393']['251'] = '251';
	$arrayEmpleadosExepciones['397']['92'] = '92';
	$arrayEmpleadosExepciones['397']['272'] = '272';
	$arrayEmpleadosExepciones['402']['247'] = '247';
	$arrayEmpleadosExepciones['402']['249'] = '249';
	$arrayEmpleadosExepciones['402']['385'] = '385';
	$arrayEmpleadosExepciones['403']['245'] = '245';
	$arrayEmpleadosExepciones['403']['288'] = '288';
	$arrayEmpleadosExepciones['405']['45'] = '45';
	$arrayEmpleadosExepciones['405']['59'] = '59';
	$arrayEmpleadosExepciones['405']['63'] = '63';
	$arrayEmpleadosExepciones['405']['67'] = '67';
	$arrayEmpleadosExepciones['405']['68'] = '68';
	$arrayEmpleadosExepciones['405']['70'] = '70';
	$arrayEmpleadosExepciones['405']['71'] = '71';
	$arrayEmpleadosExepciones['405']['72'] = '72';
	$arrayEmpleadosExepciones['405']['73'] = '73';
	$arrayEmpleadosExepciones['405']['74'] = '74';
	$arrayEmpleadosExepciones['405']['77'] = '77';
	$arrayEmpleadosExepciones['405']['81'] = '81';
	$arrayEmpleadosExepciones['405']['83'] = '83';
	$arrayEmpleadosExepciones['405']['105'] = '105';
	$arrayEmpleadosExepciones['405']['106'] = '106';
	$arrayEmpleadosExepciones['405']['109'] = '109';
	$arrayEmpleadosExepciones['405']['110'] = '110';
	$arrayEmpleadosExepciones['405']['111'] = '111';
	$arrayEmpleadosExepciones['405']['112'] = '112';
	$arrayEmpleadosExepciones['405']['113'] = '113';
	$arrayEmpleadosExepciones['405']['114'] = '114';
	$arrayEmpleadosExepciones['405']['115'] = '115';
	$arrayEmpleadosExepciones['405']['116'] = '116';
	$arrayEmpleadosExepciones['405']['120'] = '120';
	$arrayEmpleadosExepciones['405']['121'] = '121';
	$arrayEmpleadosExepciones['405']['122'] = '122';
	$arrayEmpleadosExepciones['405']['123'] = '123';
	$arrayEmpleadosExepciones['405']['124'] = '124';
	$arrayEmpleadosExepciones['405']['125'] = '125';
	$arrayEmpleadosExepciones['405']['129'] = '129';
	$arrayEmpleadosExepciones['405']['130'] = '130';
	$arrayEmpleadosExepciones['405']['131'] = '131';
	$arrayEmpleadosExepciones['405']['132'] = '132';
	$arrayEmpleadosExepciones['405']['133'] = '133';
	$arrayEmpleadosExepciones['405']['135'] = '135';
	$arrayEmpleadosExepciones['405']['140'] = '140';
	$arrayEmpleadosExepciones['405']['141'] = '141';
	$arrayEmpleadosExepciones['405']['145'] = '145';
	$arrayEmpleadosExepciones['405']['146'] = '146';
	$arrayEmpleadosExepciones['405']['147'] = '147';
	$arrayEmpleadosExepciones['405']['148'] = '148';
	$arrayEmpleadosExepciones['405']['150'] = '150';
	$arrayEmpleadosExepciones['405']['152'] = '152';
	$arrayEmpleadosExepciones['405']['153'] = '153';
	$arrayEmpleadosExepciones['405']['154'] = '154';
	$arrayEmpleadosExepciones['405']['156'] = '156';
	$arrayEmpleadosExepciones['405']['157'] = '157';
	$arrayEmpleadosExepciones['405']['158'] = '158';
	$arrayEmpleadosExepciones['405']['161'] = '161';
	$arrayEmpleadosExepciones['405']['162'] = '162';
	$arrayEmpleadosExepciones['405']['164'] = '164';
	$arrayEmpleadosExepciones['405']['166'] = '166';
	$arrayEmpleadosExepciones['405']['167'] = '167';
	$arrayEmpleadosExepciones['405']['169'] = '169';
	$arrayEmpleadosExepciones['405']['265'] = '265';
	$arrayEmpleadosExepciones['405']['274'] = '274';
	$arrayEmpleadosExepciones['405']['290'] = '290';
	$arrayEmpleadosExepciones['405']['293'] = '293';
	$arrayEmpleadosExepciones['405']['294'] = '294';
	$arrayEmpleadosExepciones['405']['305'] = '305';
	$arrayEmpleadosExepciones['405']['307'] = '307';
	$arrayEmpleadosExepciones['405']['311'] = '311';
	$arrayEmpleadosExepciones['405']['313'] = '313';
	$arrayEmpleadosExepciones['405']['321'] = '321';
	$arrayEmpleadosExepciones['405']['322'] = '322';
	$arrayEmpleadosExepciones['405']['328'] = '328';
	$arrayEmpleadosExepciones['405']['340'] = '340';
	$arrayEmpleadosExepciones['405']['345'] = '345';
	$arrayEmpleadosExepciones['405']['347'] = '347';
	$arrayEmpleadosExepciones['405']['350'] = '350';
	$arrayEmpleadosExepciones['405']['360'] = '360';
	$arrayEmpleadosExepciones['405']['365'] = '365';
	$arrayEmpleadosExepciones['405']['366'] = '366';
	$arrayEmpleadosExepciones['405']['384'] = '384';
	$arrayEmpleadosExepciones['405']['386'] = '386';
	$arrayEmpleadosExepciones['405']['388'] = '388';
	$arrayEmpleadosExepciones['405']['400'] = '400';
	$arrayEmpleadosExepciones['405']['413'] = '413';
	$arrayEmpleadosExepciones['405']['414'] = '414';
	$arrayEmpleadosExepciones['405']['417'] = '417';
	$arrayEmpleadosExepciones['405']['427'] = '427';
	$arrayEmpleadosExepciones['405']['433'] = '433';
	$arrayEmpleadosExepciones['405']['434'] = '434';
	$arrayEmpleadosExepciones['405']['435'] = '435';
	$arrayEmpleadosExepciones['405']['436'] = '436';
	$arrayEmpleadosExepciones['405']['438'] = '438';
	$arrayEmpleadosExepciones['405']['439'] = '439';
	$arrayEmpleadosExepciones['406']['52'] = '52';
	$arrayEmpleadosExepciones['406']['53'] = '53';
	$arrayEmpleadosExepciones['406']['173'] = '173';
	$arrayEmpleadosExepciones['406']['174'] = '174';
	$arrayEmpleadosExepciones['406']['176'] = '176';
	$arrayEmpleadosExepciones['406']['178'] = '178';
	$arrayEmpleadosExepciones['406']['179'] = '179';
	$arrayEmpleadosExepciones['406']['182'] = '182';
	$arrayEmpleadosExepciones['406']['183'] = '183';
	$arrayEmpleadosExepciones['406']['184'] = '184';
	$arrayEmpleadosExepciones['406']['341'] = '341';
	$arrayEmpleadosExepciones['406']['344'] = '344';
	$arrayEmpleadosExepciones['407']['84'] = '84';
	$arrayEmpleadosExepciones['407']['175'] = '175';
	$arrayEmpleadosExepciones['407']['191'] = '191';
	$arrayEmpleadosExepciones['407']['196'] = '196';
	$arrayEmpleadosExepciones['407']['198'] = '198';
	$arrayEmpleadosExepciones['407']['199'] = '199';
	$arrayEmpleadosExepciones['407']['200'] = '200';
	$arrayEmpleadosExepciones['407']['280'] = '280';
	$arrayEmpleadosExepciones['407']['315'] = '315';
	$arrayEmpleadosExepciones['407']['316'] = '316';
	$arrayEmpleadosExepciones['408']['54'] = '54';
	$arrayEmpleadosExepciones['408']['55'] = '55';
	$arrayEmpleadosExepciones['408']['56'] = '56';
	$arrayEmpleadosExepciones['408']['75'] = '75';
	$arrayEmpleadosExepciones['408']['88'] = '88';
	$arrayEmpleadosExepciones['408']['89'] = '89';
	$arrayEmpleadosExepciones['408']['187'] = '187';
	$arrayEmpleadosExepciones['408']['188'] = '188';
	$arrayEmpleadosExepciones['408']['189'] = '189';
	$arrayEmpleadosExepciones['408']['190'] = '190';
	$arrayEmpleadosExepciones['408']['192'] = '192';
	$arrayEmpleadosExepciones['408']['193'] = '193';
	$arrayEmpleadosExepciones['408']['195'] = '195';
	$arrayEmpleadosExepciones['408']['197'] = '197';
	$arrayEmpleadosExepciones['408']['312'] = '312';
	$arrayEmpleadosExepciones['408']['324'] = '324';
	$arrayEmpleadosExepciones['408']['346'] = '346';
	$arrayEmpleadosExepciones['408']['428'] = '428';
	$arrayEmpleadosExepciones['408']['429'] = '429';
	$arrayEmpleadosExepciones['409']['85'] = '85';
	$arrayEmpleadosExepciones['409']['186'] = '186';
	$arrayEmpleadosExepciones['409']['310'] = '310';
	$arrayEmpleadosExepciones['410']['12'] = '12';
	$arrayEmpleadosExepciones['410']['14'] = '14';
	$arrayEmpleadosExepciones['410']['15'] = '15';
	$arrayEmpleadosExepciones['410']['21'] = '21';
	$arrayEmpleadosExepciones['410']['23'] = '23';
	$arrayEmpleadosExepciones['410']['24'] = '24';
	$arrayEmpleadosExepciones['410']['25'] = '25';
	$arrayEmpleadosExepciones['410']['26'] = '26';
	$arrayEmpleadosExepciones['410']['27'] = '27';
	$arrayEmpleadosExepciones['410']['29'] = '29';
	$arrayEmpleadosExepciones['410']['30'] = '30';
	$arrayEmpleadosExepciones['410']['31'] = '31';
	$arrayEmpleadosExepciones['410']['57'] = '57';
	$arrayEmpleadosExepciones['410']['94'] = '94';
	$arrayEmpleadosExepciones['410']['95'] = '95';
	$arrayEmpleadosExepciones['410']['104'] = '104';
	$arrayEmpleadosExepciones['410']['177'] = '177';
	$arrayEmpleadosExepciones['410']['201'] = '201';
	$arrayEmpleadosExepciones['410']['202'] = '202';
	$arrayEmpleadosExepciones['410']['203'] = '203';
	$arrayEmpleadosExepciones['410']['204'] = '204';
	$arrayEmpleadosExepciones['410']['206'] = '206';
	$arrayEmpleadosExepciones['410']['207'] = '207';
	$arrayEmpleadosExepciones['410']['209'] = '209';
	$arrayEmpleadosExepciones['410']['210'] = '210';
	$arrayEmpleadosExepciones['410']['213'] = '213';
	$arrayEmpleadosExepciones['410']['214'] = '214';
	$arrayEmpleadosExepciones['410']['215'] = '215';
	$arrayEmpleadosExepciones['410']['216'] = '216';
	$arrayEmpleadosExepciones['410']['217'] = '217';
	$arrayEmpleadosExepciones['410']['218'] = '218';
	$arrayEmpleadosExepciones['410']['219'] = '219';
	$arrayEmpleadosExepciones['410']['220'] = '220';
	$arrayEmpleadosExepciones['410']['221'] = '221';
	$arrayEmpleadosExepciones['410']['222'] = '222';
	$arrayEmpleadosExepciones['410']['306'] = '306';
	$arrayEmpleadosExepciones['410']['332'] = '332';
	$arrayEmpleadosExepciones['410']['343'] = '343';
	$arrayEmpleadosExepciones['410']['411'] = '411';
	$arrayEmpleadosExepciones['410']['415'] = '415';
	$arrayEmpleadosExepciones['413']['37'] = '37';
	$arrayEmpleadosExepciones['413']['38'] = '38';
	$arrayEmpleadosExepciones['413']['39'] = '39';
	$arrayEmpleadosExepciones['413']['41'] = '41';
	$arrayEmpleadosExepciones['413']['230'] = '230';
	$arrayEmpleadosExepciones['413']['231'] = '231';
	$arrayEmpleadosExepciones['413']['233'] = '233';
	$arrayEmpleadosExepciones['413']['234'] = '234';
	$arrayEmpleadosExepciones['413']['235'] = '235';
	$arrayEmpleadosExepciones['413']['236'] = '236';
	$arrayEmpleadosExepciones['413']['237'] = '237';
	$arrayEmpleadosExepciones['413']['239'] = '239';
	$arrayEmpleadosExepciones['413']['240'] = '240';
	$arrayEmpleadosExepciones['413']['242'] = '242';
	$arrayEmpleadosExepciones['413']['263'] = '263';
	$arrayEmpleadosExepciones['413']['304'] = '304';
	$arrayEmpleadosExepciones['413']['335'] = '335';
	$arrayEmpleadosExepciones['413']['342'] = '342';
	$arrayEmpleadosExepciones['413']['401'] = '401';
	$arrayEmpleadosExepciones['413']['402'] = '402';
	$arrayEmpleadosExepciones['413']['425'] = '425';
	$arrayEmpleadosExepciones['414']['33'] = '33';
	$arrayEmpleadosExepciones['414']['223'] = '223';
	$arrayEmpleadosExepciones['414']['224'] = '224';
	$arrayEmpleadosExepciones['414']['225'] = '225';
	$arrayEmpleadosExepciones['414']['226'] = '226';
	$arrayEmpleadosExepciones['414']['278'] = '278';
	$arrayEmpleadosExepciones['414']['303'] = '303';
	$arrayEmpleadosExepciones['414']['317'] = '317';
	$arrayEmpleadosExepciones['414']['339'] = '339';
	$arrayEmpleadosExepciones['414']['361'] = '361';
	$arrayEmpleadosExepciones['414']['383'] = '383';
	$arrayEmpleadosExepciones['415']['34'] = '34';
	$arrayEmpleadosExepciones['415']['35'] = '35';
	$arrayEmpleadosExepciones['415']['244'] = '244';
	$arrayEmpleadosExepciones['419']['91'] = '91';
	$arrayEmpleadosExepciones['419']['96'] = '96';
	$arrayEmpleadosExepciones['419']['99'] = '99';
	$arrayEmpleadosExepciones['419']['100'] = '100';
	$arrayEmpleadosExepciones['419']['101'] = '101';
	$arrayEmpleadosExepciones['419']['102'] = '102';
	$arrayEmpleadosExepciones['419']['103'] = '103';
	$arrayEmpleadosExepciones['419']['127'] = '127';
	$arrayEmpleadosExepciones['419']['128'] = '128';
	$arrayEmpleadosExepciones['419']['289'] = '289';
	$arrayEmpleadosExepciones['419']['432'] = '432';
	$arrayEmpleadosExepciones['420']['250'] = '250';
	$arrayEmpleadosExepciones['420']['251'] = '251';
	$arrayEmpleadosExepciones['421']['92'] = '92';
	$arrayEmpleadosExepciones['421']['272'] = '272';
	$arrayEmpleadosExepciones['422']['247'] = '247';
	$arrayEmpleadosExepciones['422']['249'] = '249';
	$arrayEmpleadosExepciones['422']['385'] = '385';
	$arrayEmpleadosExepciones['423']['245'] = '245';
	$arrayEmpleadosExepciones['423']['288'] = '288';
	$arrayEmpleadosExepciones['424']['440'] = '440';
	$arrayEmpleadosExepciones['425']['441'] = '441';
	$arrayEmpleadosExepciones['427']['297'] = '297';
	$arrayEmpleadosExepciones['428']['440'] = '440';
	$arrayEmpleadosExepciones['429']['443'] = '443';
	$arrayEmpleadosExepciones['430']['440'] = '440';
	$arrayEmpleadosExepciones['431']['440'] = '440';
	$arrayEmpleadosExepciones['432']['441'] = '441';
	$arrayEmpleadosExepciones['433']['441'] = '441';
	$arrayEmpleadosExepciones['434']['441'] = '441';
	$arrayEmpleadosExepciones['438']['91'] = '91';
	$arrayEmpleadosExepciones['438']['96'] = '96';
	$arrayEmpleadosExepciones['438']['99'] = '99';
	$arrayEmpleadosExepciones['438']['100'] = '100';
	$arrayEmpleadosExepciones['438']['101'] = '101';
	$arrayEmpleadosExepciones['438']['102'] = '102';
	$arrayEmpleadosExepciones['438']['103'] = '103';
	$arrayEmpleadosExepciones['438']['127'] = '127';
	$arrayEmpleadosExepciones['438']['128'] = '128';
	$arrayEmpleadosExepciones['438']['289'] = '289';
	$arrayEmpleadosExepciones['438']['432'] = '432';
*/
/*
	$arrayEmpleadosExepciones['91']  = '91';
	$arrayEmpleadosExepciones['96']  = '96';
	$arrayEmpleadosExepciones['99']  = '99';
	$arrayEmpleadosExepciones['100'] = '100';
	$arrayEmpleadosExepciones['101'] = '101';
	$arrayEmpleadosExepciones['102'] = '102';
	$arrayEmpleadosExepciones['103'] = '103';
	$arrayEmpleadosExepciones['127'] = '127';
	$arrayEmpleadosExepciones['128'] = '128';
	$arrayEmpleadosExepciones['289'] = '289';
	$arrayEmpleadosExepciones['296'] = '296';
	$arrayEmpleadosExepciones['297'] = '297';
	$arrayEmpleadosExepciones['299'] = '299';
	$arrayEmpleadosExepciones['318'] = '318';
	$arrayEmpleadosExepciones['418'] = '418';
	$arrayEmpleadosExepciones['419'] = '419';
	$arrayEmpleadosExepciones['420'] = '420';
	$arrayEmpleadosExepciones['421'] = '421';
	$arrayEmpleadosExepciones['422'] = '422';
	$arrayEmpleadosExepciones['423'] = '423';
	$arrayEmpleadosExepciones['424'] = '424';
	$arrayEmpleadosExepciones['425'] = '425';
	$arrayEmpleadosExepciones['430'] = '430';
	$arrayEmpleadosExepciones['431'] = '431';
	$arrayEmpleadosExepciones['432'] = '432';
	$arrayEmpleadosExepciones['440'] = '440';
	$arrayEmpleadosExepciones['441'] = '441';
	$arrayEmpleadosExepciones['443'] = '443';
*/

	foreach ($arrayPlanillas as $id_planilla => $arrayPlanillasResul) {
		echo '<table style="border-collapse:collapse;">
				<tr style="color:#FFF;background-color:#000;">
					<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">consecutivo</td>
					<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">fecha inicio</td>
					<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">fecha final</td>
					<td colspan="5" style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">usuario</td>
					<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">empresa</td>
				</tr>
				<tr>
					<td>'.$arrayPlanillasResul['consecutivo'].'</td>
					<td>'.$arrayPlanillasResul['fecha_inicio'].'</td>
					<td>'.$arrayPlanillasResul['fecha_final'].'</td>
					<td colspan="5">'.$arrayPlanillasResul['usuario'].'</td>
					<td style="text-align:right;">'.$arrayPlanillasResul['id_empresa'].' </td>
				</tr>
				<tr style="color:#FFF;background-color:#AED4AA;">
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;"></td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Documento</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Empleado</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">id_contrato</td>
	 			</tr>
				';
		$id_empresa = $arrayPlanillasResul['id_empresa'];
		foreach ($arrayEmpleados[$id_planilla] as $id_empleado => $arrayEmpleadosResul) {
			if ($arrayEmpleadosExepciones[$id_planilla][$id_empleado]==$id_empleado) {
				echo "<br></br>".$id_empleado."</br><br>";
				continue;
			}
			$formula = $arrayEmpleadosConceptos[$id_empleado]['formula'];
			$formula = ($formula=='')? $arrayConceptoVC[$id_empresa]['formula'] : $formula ;
			$formulaInsert = ($formula=='')? $arrayConceptoVC[$id_empresa]['formula'] : $formula ;
			$formula = str_replace('{SC}', $arrayEmpleadosInfo[$id_empresa][$id_empleado]['salario_basico'], $formula );			// SALARIO DEL CONTRATO
			$formula = str_replace('{DL}', $arrayEmpleadosResul['dias_laborados_empleado'], $formula );			// DIAS LABORADOS



			foreach ($arrayConceptosEmpleado[$id_planilla][$id_empleado] as $codigo_concepto => $valor_concepto) {
				$formula = str_replace('['.$codigo_concepto.']', $valor_concepto, $formula );
			}

			$formula = reemplazarValoresFaltantes($formula);
			$valor_concepto = calcula_formula($formula);

			$valueInsert.="('$id_planilla',
							'$id_empleado',
							'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_contrato']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['id']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['codigo']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['concepto']."',
							'$valor_concepto',
							'$formula',
							'$formulaInsert',
							'1',
							'$id_empresa',
							'".$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_colgaap']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_colgaap']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_colgaap']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_niif']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_niif']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_niif']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['caracter']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['centro_costos']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_contrapartida_colgaap']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_colgaap']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_contrapartida_colgaap']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['id_cuenta_contrapartida_niif']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_niif']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['descripcion_cuenta_contrapartida_niif']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['centro_costos_contrapartida']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['naturaleza']."',
							'".$arrayEmpleadosConceptos[$id_empleado]['imprimir_volante']."','true'
						),";


			if (strpos($arrayEmpleadosConceptos[$id_empleado]['cuenta_colgaap'], '2')===0) {
				$valueInsertConfiguracion .= "('".$arrayIdTercero[$id_empleado]."',
		    								'$id_planilla',
		    								'".$arrayPlanillasResul['consecutivo']."',
		    								'".$arrayEmpleadosConceptos[$id_empleado]['id']."',
		    								'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_colgaap']."',
		    								'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_niif']."',
		    								'".(($arrayEmpleadosConceptos[$id_empleado]['caracter']=='debito')? $valor_concepto : 0 )."',
											'".(($arrayEmpleadosConceptos[$id_empleado]['caracter']=='credito')? $valor_concepto : 0 )."',
											'0',
											'$valor_concepto',
											'".$arrayPlanillasResul['fecha_inicio']."',
											'".$arrayPlanillasResul['fecha_final']."',
											'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_sucursal']."',
											'$id_empresa','true'),";
			}else if (strpos($arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_colgaap'], '2')===0) {
				$valueInsertConfiguracion .= "('".$arrayIdTercero[$id_empleado]."',
		    									'$id_planilla',
		    									'".$arrayPlanillasResul['consecutivo']."',
		    									'".$arrayEmpleadosConceptos[$id_empleado]['id']."',
		    									'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_colgaap']."',
		    									'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_niif']."',
		    									'".(($arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']=='debito')? $valor_concepto : 0 )."',
												'".(($arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']=='credito')? $valor_concepto : 0 )."',
												'0',
												'$valor_concepto',
												'".$arrayPlanillasResul['fecha_inicio']."',
												'".$arrayPlanillasResul['fecha_final']."',
												'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_sucursal']."',
												'$id_empresa','true'),";
			}




			$valueInsertAsientos .= "('$id_planilla',
										'".$arrayPlanillasResul['consecutivo']."',
										'LN',
										'$id_planilla',
										'".$arrayPlanillasResul['consecutivo']."',
										'LN',
										'Liquidacion Nomina',
										'".$arrayPlanillasResul['fecha_documento']."',
										'".(($arrayEmpleadosConceptos[$id_empleado]['caracter']=='debito')? $valor_concepto : 0 )."',
										'".(($arrayEmpleadosConceptos[$id_empleado]['caracter']=='credito')? $valor_concepto : 0 )."',
										'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_colgaap']."',
										'".$arrayIdTercero[$id_empleado]."',
										'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_centro_costos']."',
										'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_sucursal']."',
										'$id_empresa','true'),";

			$valueInsertAsientos .= "('$id_planilla',
										'".$arrayPlanillasResul['consecutivo']."',
										'LN',
										'$id_planilla',
										'".$arrayPlanillasResul['consecutivo']."',
										'LN',
										'Liquidacion Nomina',
										'".$arrayPlanillasResul['fecha_documento']."',
										'".(($arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']=='debito')? $valor_concepto : 0 )."',
										'".(($arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']=='credito')? $valor_concepto : 0 )."',
										'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_colgaap']."',
										'".$arrayIdTercero[$id_empleado]."',
										'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_centro_costos']."',
										'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_sucursal']."',
										'$id_empresa','true'),";


			$valueInsertAsientosNiif .= "('$id_planilla',
												'".$arrayPlanillasResul['consecutivo']."',
												'LN',
												'$id_planilla',
												'".$arrayPlanillasResul['consecutivo']."',
												'LN',
												'Liquidacion Nomina',
												'".$arrayPlanillasResul['fecha_documento']."',
												'".(($arrayEmpleadosConceptos[$id_empleado]['caracter']=='debito')? $valor_concepto : 0 )."',
												'".(($arrayEmpleadosConceptos[$id_empleado]['caracter']=='credito')? $valor_concepto : 0 )."',
												'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_niif']."',
												'".$arrayIdTercero[$id_empleado]."',
												'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_centro_costos']."',
												'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_sucursal']."',
												'$id_empresa','true'),";

			$valueInsertAsientosNiif .= "('$id_planilla',
												'".$arrayPlanillasResul['consecutivo']."',
												'LN',
												'$id_planilla',
												'".$arrayPlanillasResul['consecutivo']."',
												'LN',
												'Liquidacion Nomina',
												'".$arrayPlanillasResul['fecha_documento']."',
												'".(($arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']=='debito')? $valor_concepto : 0 )."',
												'".(($arrayEmpleadosConceptos[$id_empleado]['caracter_contrapartida']=='credito')? $valor_concepto : 0 )."',
												'".$arrayEmpleadosConceptos[$id_empleado]['cuenta_contrapartida_niif']."',
												'".$arrayIdTercero[$id_empleado]."',
												'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_centro_costos']."',
												'".$arrayEmpleadosInfo[$id_empresa][$id_empleado]['id_sucursal']."',
												'$id_empresa','true'),";

			// print_r($arrayEmpleadosInfo[$id_empresa]);
			echo '<tr>
		 			<td></td>
		 			<td>'.$arrayEmpleadosResul['documento_empleado'].'</td>
		 			<td>'.$arrayEmpleadosResul['nombre_empleado'].' - '.$id_empresa.' - '.$id_empleado.'</td>
		 			<td>'.$arrayEmpleadosResul['dias_laborados_empleado'].'</td>
		 			<!--<td>'.$arrayConceptoVC[$arrayPlanillasResul['id_empresa']]['formula'].'</td>-->
		 			<td>'.$arrayEmpleadosInfo[$id_empresa][$id_empleado]['salario_basico'].'</td>
		 			<td style="text-align:right;">'.$valor_concepto.'</td>
	 			</tr>';

		}

		echo '</table>';


	}

	$valueInsert = substr($valueInsert, 0, -1);
	echo$sql   = "INSERT INTO nomina_planillas_empleados_conceptos(
																id_planilla,
																id_empleado,
																id_contrato,
																id_concepto,
																codigo_concepto,
																concepto,
																valor_concepto,
																formula,
																formula_original,
																nivel_formula,
																id_empresa,
																id_cuenta_colgaap,
																cuenta_colgaap,
																descripcion_cuenta_colgaap,
																id_cuenta_niif,
																cuenta_niif,
																descripcion_cuenta_niif,
																caracter,
																centro_costos,
																id_cuenta_contrapartida_colgaap,
																cuenta_contrapartida_colgaap,
																descripcion_cuenta_contrapartida_colgaap,
																id_cuenta_contrapartida_niif,
																cuenta_contrapartida_niif,
																descripcion_cuenta_contrapartida_niif,
																caracter_contrapartida,
																centro_costos_contrapartida,
																naturaleza,
																imprimir_volante,debug_nomina)
															VALUES $valueInsert;";

		$valueInsertConfiguracion = substr($valueInsertConfiguracion, 0, -1);
		echo$sql   = "INSERT INTO nomina_planillas_empleados_contabilizacion
							(id_tercero,
								id_planilla,
								consecutivo_planilla,
								id_concepto,
								cuenta_colgaap,
								cuenta_niif,
								debito,
								credito,
								total_sin_abono,
								total_sin_abono_provision,
								fecha_inicio_planilla,
								fecha_final_planilla,
								id_sucursal,
								id_empresa,debug_nomina)
					VALUES $valueInsertConfiguracion;";

		$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
		$valueInsertAsientosNiif = substr($valueInsertAsientosNiif, 0, -1);

		//INSERT COLGAAP
    	echo$sqlColgaap = "INSERT INTO asientos_colgaap(
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
							id_centro_costos,
							id_sucursal,
							id_empresa,
							debug_nomina)
						VALUES $valueInsertAsientos;";

		echo$sqlNiif = "INSERT INTO asientos_niif(
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
						id_centro_costos,
						id_sucursal,
						id_empresa,
						debug_nomina)
					VALUES $valueInsertAsientosNiif;";


	function reemplazarValoresFaltantes($formula){
		$conceptos=EncuentraVariablesCadena($formula);
		foreach ($conceptos as $key => $codigo) {
			$formula=str_replace('['.$codigo.']', 0, $formula);
		}
		return $formula;
	}

	function EncuentraVariablesCadena($mensaje){
		$resultado = array();
		$esta = stripos($mensaje,"[");
		if($esta !== false){
			$primera = explode("[",$mensaje);
			for($i=0;$i<count($primera);$i++){
				$esta2 = stripos($primera[$i],"]");
				if($esta2 !== false){
					$r = count($resultado);
					$segunda = explode("]",$primera[$i]);
					$resultado[$r] = $segunda[0];
				}
			}
		}
		return $resultado;
	}

	//FUNCION PARA CALCULAR LA FORMULA DEL CONCEPTO
	function calcula_formula($equation){
    	if ($equation==''){ return round(0,$_SESSION['DECIMALESMONEDA']); }

        // Remove whitespaces
        $equation = preg_replace('/\s+/', '', $equation);
        // echo "$equation\n=";
        // echo 'alert("'.$equation.'"=)';

		$number    = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?|pi|π)'; // What is a number
		$functions = '(?:sinh?|cosh?|tanh?|acosh?|asinh?|atanh?|exp|log(10)?|deg2rad|rad2deg|sqrt|pow|abs|intval|ceil|floor|round|(mt_)?rand|gmp_fact)'; // Allowed PHP functions
		$operators = '[\/*\^\+-,]'; // Allowed math operators
		$regexp    = '/^([+-]?('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns

        if (preg_match($regexp, $equation)){
            $equation = preg_replace('!pi|π!', 'pi()', $equation); // Replace pi with pi function
            eval('$result = '.$equation.';');
        }
        else{ $result = false; }

        return round($result,2);
    }


 ?>
