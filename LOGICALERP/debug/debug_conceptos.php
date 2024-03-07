<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	// exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$id_empresa=50;

	$sql="SELECT * FROM nomina_planillas WHERE activo=1 AND (estado=1 OR estado=3) AND id_empresa=$id_empresa";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$whereIdPlanilla.=($whereIdPlanilla=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id'] ;
		// echo $row['consecutivo'].' - '.$row['sucursal'].'<br>';
		$arrayPlanillas[$row['id']]=array('consecutivo'=> $row['consecutivo'],
											'sucursal' => $row['sucursal'],
										);
	}

	$sql="SELECT * FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdPlanilla) ";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$whereIdPlanillaEmpleados.=($whereIdPlanillaEmpleados=='')? '(id_planilla='.$row['id_planilla'].' AND id_empleado='.$row['id_empleado'].')' : ' OR (id_planilla='.$row['id_planilla'].' AND id_empleado='.$row['id_empleado'].')' ;
		$arrayPlanillasEmpleados[$row['id_planilla']][$row['id_empleado']]=array(
																					'documento_empleado'      => $row['documento_empleado'],
																					'nombre_empleado'         => $row['nombre_empleado'],
																					'dias_laborados'          => $row['dias_laborados'],
																					'dias_laborados_empleado' => $row['dias_laborados_empleado']
																				);
	}

	$sql="SELECT * FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND dias_laborados=0 AND id_empresa=$id_empresa AND ($whereIdPlanillaEmpleados) ";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$arrayPlanillasEmpleadosConceptos[$row['id_planilla']][$row['id_empleado']][$row['id_concepto']]=array(
																								'concepto'             => $row['concepto'],
																								'naturaleza'           => $row['naturaleza'],
																								'valor_concepto'       => $row['valor_concepto'],
																								'dias_laborados'       => $row['dias_laborados'],
																								'saldo_dias_laborados' => $row['saldo_dias_laborados'],
																							);
				}
	$sql='';
	foreach ($arrayPlanillas as $id_planilla => $arrayPlanillasResul) {
		$body.='<table align="center" border="1" style="margin-top:20px;">';
		$body.='<tr style="font-weight:bold;">
				<td>'.$id_planilla.'</td>
				<td>'.$arrayPlanillasResul['consecutivo'].'</td>
				<td>'.$arrayPlanillasResul['sucursal'].'</td>
				</tr>';

		foreach ($arrayPlanillasEmpleados[$id_planilla] as $id_empleado => $arrayPlanillasEmpleadosResul) {
			$body.='<tr style="font-weight:bold;margin-top:20px;">
						<td>documento_empleado</td>
						<td>nombre_empleado</td>
						<td>dias_laborados</td>
						<td>dias_laborados_empleado</td>
					</tr>';
			$body.='<tr>
						<td>'.$id_empleado.'</td>
						<td>'.$arrayPlanillasEmpleadosResul['documento_empleado'].'</td>
						<td>'.$arrayPlanillasEmpleadosResul['nombre_empleado'].'</td>
						<td>'.$arrayPlanillasEmpleadosResul['dias_laborados'].'</td>
						<td>'.$arrayPlanillasEmpleadosResul['dias_laborados_empleado'].'</td>
					</tr>';
			$body.='<tr style="font-weight:bold;margin-top:20px;">
						<td>concepto</td>
						<td>naturaleza</td>
						<td>valor_concepto</td>
						<td>dias_laborados</td>
						<td>saldo_dias_laborados</td>
						</tr>';
			foreach ($arrayPlanillasEmpleadosConceptos[$id_planilla][$id_empleado] as $id_concepto => $arrayPlanillasEmpleadosConceptosResul) {
				// echo $arrayPlanillasEmpleadosConceptosResul['concepto'];
				// if ($arrayPlanillasEmpleadosConceptosResul['dias_laborados']>0 || $arrayPlanillasEmpleadosConceptosResul['saldo_dias_laborados']>0) {

					$body.='<tr>
							<td>'.$arrayPlanillasEmpleadosConceptosResul['concepto'].'</td>
							<td>'.$arrayPlanillasEmpleadosConceptosResul['naturaleza'].'</td>
							<td>'.$arrayPlanillasEmpleadosConceptosResul['valor_concepto'].'</td>
							<td>'.$arrayPlanillasEmpleadosConceptosResul['dias_laborados'].'</td>
							<td>'.$arrayPlanillasEmpleadosConceptosResul['saldo_dias_laborados'].'</td>
							</tr>';
				// }
				$sql.="UPDATE nomina_planillas_empleados_conceptos SET dias_laborados=$arrayPlanillasEmpleadosResul[dias_laborados_empleado],saldo_dias_laborados=$arrayPlanillasEmpleadosResul[dias_laborados_empleado]
						WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_concepto=$id_concepto;";
				// echo "<br>";
			}
		}

		$body.='</table>';
	}


	echo $body;
	echo $sql;
	// print_r($arrayPlanillasEmpleados);
	// echo "<br>";
	// print_r($arrayPlanillasEmpleadosConceptos);

?>