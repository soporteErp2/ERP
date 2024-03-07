<?php
	// session_start();
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");

	$sql   = "SELECT
					id,
					consecutivo,
					fecha_inicio,
					fecha_final,
					usuario,
					sucursal,
					id_empresa,
					CASE id_empresa
						WHEN 1 THEN 'COLOMBIA'
						WHEN 47 THEN 'COMUNICACIONES'
						ELSE 'Sin Empresa'
					END AS 'empresa'

				FROM
					nomina_planillas
				WHERE
					activo = 1
				AND consecutivo>0
				/*AND estado = 1*/
				AND fecha_inicio >= '2016-01-01'
				AND (
					   /*id_empresa = 1 OR*/
					   id_empresa = 47
				)
				ORDER BY id_empresa,consecutivo ASC;";

	$query=$mysql->query($sql,$link);
	while ($row=$mysql->fetch_array($query)) {
		$whereIdPlanillas.=($whereIdPlanillas=='')? ' NPE.id_planilla='.$row['id'] : ' OR NPE.id_planilla='.$row['id'];
		$arrayPlanillas[$row['id']]= array(
											'consecutivo'  => $row['consecutivo'],
											'fecha_inicio' => $row['fecha_inicio'],
											'fecha_final'  => $row['fecha_final'],
											'usuario'      => $row['usuario'],
											'id_empresa'   => $row['id_empresa'],
											'sucursal'     => $row['sucursal'],
											'empresa'      => $row['empresa'],
											);
	}


	$sql   = "SELECT
					NPE.id_planilla,
					NPE.id_empleado,
					NPE.documento_empleado,
					NPE.nombre_empleado,
					NPE.dias_laborados_empleado,
					NPEC.id,
					NPEC.codigo_concepto,
					NPEC.id_concepto,
					NPEC.concepto,
					NPEC.valor_concepto,
					NPEC.id_cuenta_colgaap,
					NPEC.cuenta_colgaap,
					NPEC.descripcion_cuenta_colgaap,
					NPEC.id_cuenta_niif,
					NPEC.cuenta_niif,
					NPEC.descripcion_cuenta_niif,
					NPEC.caracter,
					NPEC.id_cuenta_contrapartida_colgaap,
					NPEC.cuenta_contrapartida_colgaap,
					NPEC.descripcion_cuenta_contrapartida_colgaap,
					NPEC.id_cuenta_contrapartida_niif,
					NPEC.cuenta_contrapartida_niif,
					NPEC.descripcion_cuenta_contrapartida_niif,
					NPEC.caracter_contrapartida,
					NPEC.id_tercero,
					NPEC.id_tercero_contrapartida
				FROM
					nomina_planillas_empleados AS NPE,
					nomina_planillas_empleados_conceptos AS NPEC
				WHERE
					NPE.activo=1
					AND (NPEC.id_planilla_cruce = 0 OR NPEC.id_planilla_cruce IS NULL)
					AND NPEC.id_empleado = NPE.id_empleado
					AND NPEC.id_planilla = NPE.id_planilla
					AND (
						NPEC.codigo_concepto = 'ISC'
						OR NPEC.codigo_concepto = 'CS'
						)
					AND ($whereIdPlanillas)
				GROUP BY NPE.id_planilla,NPE.id_empleado,NPEC.id_concepto;";
	// exit;
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query) ){

		$arrayPlanillasEmpleados[$row['id_planilla']][$row['id_empleado']]=array(
																				'documento_empleado'=>$row['documento_empleado'],
																				'nombre_empleado'	=>$row['nombre_empleado'],
																				);

		$arrayPlanillasEmpleadosConceptos[$row['id_planilla']][$row['id_empleado']][$row['id_concepto']]= array(
																												'id'                                       => $row['id'],
																												'documento_empleado'                       => $row['documento_empleado'],
																												'nombre_empleado'                          => $row['nombre_empleado'],
																												'dias_laborados_empleado'                  => $row['dias_laborados_empleado'],
																												'codigo_concepto'                          => $row['codigo_concepto'],
																												'concepto'                                 => $row['concepto'],
																												'valor_concepto'                           => $row['valor_concepto'],
																												'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
																												'cuenta_colgaap'                           => $row['cuenta_colgaap'],
																												'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
																												'id_cuenta_niif'                           => $row['id_cuenta_niif'],
																												'cuenta_niif'                              => $row['cuenta_niif'],
																												'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
																												'caracter'                                 => $row['caracter'],
																												'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
																												'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
																												'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
																												'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
																												'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
																												'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
																												'caracter_contrapartida'                   => $row['caracter_contrapartida'],
																												'id_tercero'                               => $row['id_tercero'],
																												'id_tercero_contrapartida'                 => $row['id_tercero_contrapartida']
																												);
		$arrayValoresConceptos[$row['id_planilla']][$row['id_empleado']][$row['codigo_concepto']]=$row['valor_concepto'];

		if ($row['codigo_concepto']=='ISC' ) {
			$arrayWhereCuentas[$row['cuenta_colgaap']]               = $row['cuenta_colgaap'];
			$arrayWhereCuentas[$row['cuenta_contrapartida_colgaap']] = $row['cuenta_contrapartida_colgaap'];

			$arrayIdTerceros[$row['id_tercero']]                   = $row['id_tercero'];
			$arrayIdTerceros[$row['id_tercero_contrapartida']]     = $row['id_tercero_contrapartida'];

			// $arrayWhereCuentasNiif[$row['cuenta_colgaap']] = $row['cuenta_colgaap'];
			$whereCuenta    .=($whereCuenta=='')? "codigo_cuenta='".$row['cuenta_colgaap']."'" : " OR codigo_cuenta='".$row['cuenta_colgaap']."'" ;
			$whereIdTercero .=($whereIdTercero=='')? 'id_tercero='.$row['id_tercero'] : ' OR id_tercero='.$row['id_tercero'] ;
			$whereCuenta    .=($whereCuenta=='')? "codigo_cuenta='".$row['cuenta_contrapartida_colgaap']."'" : " OR codigo_cuenta='".$row['cuenta_contrapartida_colgaap']."'" ;
			$whereIdTercero .=($whereIdTercero=='')? 'id_tercero='.$row['id_tercero_contrapartida'] : ' OR id_tercero='.$row['id_tercero_contrapartida'] ;
		}

	}

	$whereIdPlanillas = str_replace("NPE.", '', $whereIdPlanillas);
	$whereIdPlanillasAsientos = str_replace("id_planilla", 'id_documento', $whereIdPlanillas);

	$whereCuenta = '';
	foreach ($arrayWhereCuentas as $key => $cuenta) {
		$whereCuenta   .=($whereCuenta=='')? "codigo_cuenta='".$cuenta."'" : " OR codigo_cuenta='".$cuenta."'" ;
	}
	$whereIdTercero ='';
	foreach ($arrayIdTerceros as $key => $id_tercero) {
		$whereIdTercero   .=($whereIdTercero=='')? "id_tercero='".$id_tercero."'" : " OR id_tercero='".$id_tercero."'" ;
	}

	// SELECCIONAR LOS ASIENTOS
	$sql="SELECT
				id,
				id_documento,
				consecutivo_documento,
				tipo_documento,
				tipo_documento_extendido,
				fecha,
				debe,
				haber,
				codigo_cuenta,
				id_tercero,
				tercero
			FROM asientos_colgaap
			WHERE
				activo=1
			AND tipo_documento='LN'
			AND ($whereCuenta)
			AND ($whereIdTercero)
			AND ($whereIdPlanillasAsientos)
			";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=mysql_fetch_array($query)){
		$arrayAsientos[$row['id_documento']][$row['id_tercero']][]=array(
																			'id'                       =>$row['id'],
																			'tipo_documento_extendido' =>$row['tipo_documento_extendido'],
																			'fecha'                    =>$row['fecha'],
																			'debe'                     =>$row['debe'],
																			'haber'                    =>$row['haber'],
																			'codigo_cuenta'            =>$row['codigo_cuenta'],
																			'tercero'                  =>$row['tercero']
																		);
	}
	// print_r($arrayAsientos);
	// exit;
	// SELECCIONAR LOS ASIENTOS
	$sql="SELECT
				id,
				id_documento,
				consecutivo_documento,
				tipo_documento,
				tipo_documento_extendido,
				fecha,
				debe,
				haber,
				codigo_cuenta,
				id_tercero,
				tercero
			FROM asientos_niif
			WHERE
				activo=1
			AND tipo_documento='LN'
			AND ($whereCuenta)
			AND ($whereIdTercero)
			AND ($whereIdPlanillasAsientos)
			";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=mysql_fetch_array($query)){
		$arrayAsientosNiif[$row['id_documento']][$row['id_tercero']][]=array(
																				'id'                       =>$row['id'],
																				'tipo_documento_extendido' =>$row['tipo_documento_extendido'],
																				'fecha'                    =>$row['fecha'],
																				'debe'                     =>$row['debe'],
																				'haber'                    =>$row['haber'],
																				'codigo_cuenta'            =>$row['codigo_cuenta'],
																				'tercero'                  =>$row['tercero']
																			);
	}

	$sql="SELECT
				id,
				id_planilla,
				documento_tercero,
				tercero,
				debito,
				credito,
				total_sin_abono,
				cuenta_colgaap,
				id_tercero
			FROM nomina_planillas_empleados_contabilizacion
			WHERE
			activo=1
			AND tipo_planilla='LN'
			AND ($whereIdPlanillas)
			AND ($whereIdTercero)";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=mysql_fetch_array($query)){
		$arrayCuentaCruce[$row['id_planilla']][$row['id_tercero']]=array(
																		'id'                =>$row['id'],
																		'documento_tercero' =>$row['documento_tercero'],
																		'tercero'           =>$row['tercero'],
																		'debito'            =>$row['debito'],
																		'credito'           =>$row['credito'],
																		'total_sin_abono'   =>$row['total_sin_abono'],
																		'cuenta_colgaap'    =>$row['cuenta_colgaap']
																	);
	}

	// ARRAY CON LAS PLANILLAS
	foreach($arrayPlanillas as $id_planilla => $arrayPlanillasResul) {
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
					<td>'.$arrayPlanillasResul['sucursal'].' - '.$arrayPlanillasResul['empresa'].'</td>
				</tr>
				<tr style="color:#FFF;background-color:#aed4aa;">
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;"></td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Documento</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Empleado</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Codigo Concepto</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Cuenta</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">DL</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Valor Concepto</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Valor Nuevo</td>
		 			<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:14px;font-family:sans-serif;">Tercero</td>
	 			</tr>
				';
		$head_emp=0;
		// ARRAY CON LOS EMPLEADOS
		foreach ($arrayPlanillasEmpleados[$id_planilla] as $id_empleado => $arrayPlanillasEmpleadosResul) {
			// ARRAY CON LOS CONCEPTOS
			foreach ($arrayPlanillasEmpleadosConceptos[$id_planilla][$id_empleado] as $id_concepto => $arrayPlanillasEmpleadosConceptosResul) {

			 	if ($arrayPlanillasEmpleadosConceptosResul['caracter']=='debito'){
		 			$cuenta=$arrayPlanillasEmpleadosConceptosResul['cuenta_colgaap'];
		 			$id_tercero = $arrayPlanillasEmpleadosConceptosResul['id_tercero'];
		 		}
		 		else if ($arrayPlanillasEmpleadosConceptosResul['caracter_contrapartida']=='debito'){
		 			$cuenta=$arrayPlanillasEmpleadosConceptosResul['cuenta_contrapartida_colgaap'];
		 			$id_tercero = $arrayPlanillasEmpleadosConceptosResul['id_tercero_contrapartida'];
		 		}

		 		$valor_nuevo=$arrayValoresConceptos[$id_planilla][$id_empleado]['CS']*0.12;
		 		$valor_nuevo=round($valor_nuevo, 2);

		 		if ($arrayPlanillasEmpleadosConceptosResul['codigo_concepto']=='ISC') {
		 			$tabla_cuentas.='<tr style="color:#FFF;background-color:#ffa499;font-weight:bold;"><td colspan="10"> ASIENTOS</td></tr>
		 							<tr style="color:#FFF;background-color:#ffa499;font-weight:bold;">
		 								<td>tipo_doc</td>
		 								<td>fecha</td>
		 								<td>debito</td>
		 								<td>credito</td>
		 								<td>cuenta</td>
		 								<td colspan="4">tercero</td>
		 							</tr>
		 							<tr style="color:#444;background-color:#EEE;">
		 								<td>'.$arrayAsientos[$id_planilla][$id_tercero]['tipo_documento_extendido'].'</td>
		 								<td>'.$arrayAsientos[$id_planilla][$id_tercero]['fecha'].'</td>
		 								<td>'.$arrayAsientos[$id_planilla][$id_tercero]['debe'].'</td>
		 								<td>'.$arrayAsientos[$id_planilla][$id_tercero]['haber'].'</td>
		 								<td>'.$arrayAsientos[$id_planilla][$id_tercero]['codigo_cuenta'].'</td>
		 								<td colspan="4">'.$arrayAsientos[$id_planilla][$id_tercero]['tercero'].'</td>
		 							</tr>';
					$tabla_cuentas.='<tr style="color:#FFF;background-color:#4790ff;font-weight:bold;"><td colspan="10"> REFERENCIA CRUCE</td></tr>
									<tr style="color:#FFF;background-color:#4790ff;font-weight:bold;">
		 								<td>doc_tercero</td>
		 								<td>tercero</td>
		 								<td>debito</td>
		 								<td>credito</td>
		 								<td>cuenta</td>
		 								<td colspan="4">sin abono</td>
		 							</tr>
		 							<tr style="color:#444;background-color:#EEE;">
		 								<td>'.$arrayCuentaCruce[$id_planilla][$id_tercero]['documento_tercero'].'</td>
		 								<td>'.$arrayCuentaCruce[$id_planilla][$id_tercero]['tercero'].'</td>
		 								<td>'.$arrayCuentaCruce[$id_planilla][$id_tercero]['debito'].'</td>
		 								<td>'.$arrayCuentaCruce[$id_planilla][$id_tercero]['credito'].'</td>
		 								<td>'.$arrayCuentaCruce[$id_planilla][$id_tercero]['cuenta_colgaap'].'</td>
		 								<td>'.$arrayCuentaCruce[$id_planilla][$id_tercero]['total_sin_abono'].'</td>
		 							</tr>';

					$cadenaUpdate="UPDATE asientos_colgaap SET debe=IF(debe>0,$valor_nuevo,0),haber=IF(haber>0,$valor_nuevo,0) WHERE id='".$arrayAsientos[$id_planilla][$id_tercero][0]['id']."';";
					$cadenaUpdate.="<br>UPDATE asientos_colgaap SET debe=IF(debe>0,$valor_nuevo,0),haber=IF(haber>0,$valor_nuevo,0) WHERE id='".$arrayAsientos[$id_planilla][$id_tercero][1]['id']."';";
					$cadenaUpdate.="<br>UPDATE asientos_niif SET debe=IF(debe>0,$valor_nuevo,0),haber=IF(haber>0,$valor_nuevo,0) WHERE id='".$arrayAsientosNiif[$id_planilla][$id_tercero][0]['id']."';";
					$cadenaUpdate.="<br>UPDATE asientos_niif SET debe=IF(debe>0,$valor_nuevo,0),haber=IF(haber>0,$valor_nuevo,0) WHERE id='".$arrayAsientosNiif[$id_planilla][$id_tercero][1]['id']."';";
					$cadenaUpdate.="<br>UPDATE nomina_planillas_empleados_contabilizacion SET debito=IF(debito>0,$valor_nuevo,0),credito=IF(credito>0,$valor_nuevo,0),total_sin_abono_provision=$valor_nuevo WHERE id='".$arrayCuentaCruce[$id_planilla][$id_tercero]['id']."';";
					$cadenaUpdate.="<br>UPDATE nomina_planillas_empleados_conceptos SET valor_concepto=$valor_nuevo WHERE id='".$arrayPlanillasEmpleadosConceptosResul['id']."';";

					$cadenaUpdateFinal.=$cadenaUpdate;

					$tabla_cuentas.="<tr style='color:#444;background-color:#d0d0a3;'><td>SQL UPDATE</td></tr>
									<tr style='background-color:#d0d0a3;' ><td colspan='9' >".$cadenaUpdate."</td></tr>";

		 		}

			 	echo '
			 		<tr>
			 			<td> </td>
			 			<td>'.$arrayPlanillasEmpleadosConceptosResul['documento_empleado'].'</td>
			 			<td>'.$arrayPlanillasEmpleadosConceptosResul['nombre_empleado'].'</td>
			 			<td>'.$arrayPlanillasEmpleadosConceptosResul['codigo_concepto'].'</td>
			 			<td>'.$cuenta.'</td>
			 			<td>'.$arrayPlanillasEmpleadosConceptosResul['dias_laborados_empleado'].'</td>
			 			<td style="text-align:right;">'.$arrayPlanillasEmpleadosConceptosResul['valor_concepto'].'</td>
			 			<td style="text-align:right;">'.(($arrayPlanillasEmpleadosConceptosResul['codigo_concepto']=='ISC')? $valor_nuevo : '' ).'</td>
			 			<td style="text-align:right;">'.$id_tercero.'</td>
		 			</tr>
		 			'.$tabla_cuentas;

	 			$tabla_cuentas='';

		 	}
		}
		echo "</table><br><br><br>";
	}
	echo $cadenaUpdateFinal;
	echo '<table style="border-collapse:collapse;">
			<tr style="color:#FFF;background-color:#000;">
				<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">consecutivo</td>
				<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">fecha inicio</td>
				<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">fecha final</td>
				<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">usuario</td>
				<td style="padding: 10px 10px 10px 10px;font-weight:bold;font-size:16px;font-family:sans-serif;">empresa</td>
			</tr>
				'.$body.'
			</table>
		';

 ?>
