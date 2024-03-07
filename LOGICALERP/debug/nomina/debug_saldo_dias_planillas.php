<?php
	error_reporting(E_ALL);
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");
	exit;
	// CONSULTAR TODAS LAS EMPRESAS
	// $sqlEmpresas="SELECT id FROM empresas WHERE activo=1 AND (id=48 OR id=49 OR id=50 OR id=51 OR id=52 OR id=54)";
	$sqlEmpresas="SELECT id FROM empresas WHERE activo=1 AND (id=1 OR id=47 )";
	$queryEmpresas=mysql_query($sqlEmpresas,$link);
	while ($rowEmpresas=mysql_fetch_array($queryEmpresas)) {

		$id_empresa=$rowEmpresas['id'];
		// $id_empresa=48;

		// CONSULTAR TODAS LAS PLANILLAS DE LIQUIDACION REALIZADAS A LA FECHA
		$sql="SELECT id,fecha_inicio,fecha_final,id_sucursal,consecutivo,sucursal FROM nomina_planillas_liquidacion WHERE activo=1 AND (estado=1) AND id_empresa=$id_empresa ORDER BY sucursal ASC;";

		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$fecha_inicio = $row['fecha_inicio'];
			$fecha_final  = $row['fecha_final'];
			$id_sucursal  = $row['id_sucursal'];

			$sql2="SELECT id_empleado,id_concepto,concepto
	    			FROM nomina_planillas_liquidacion_empleados_conceptos
	    			WHERE activo=1 AND id_planilla=$row[id] AND id_empresa=$id_empresa AND naturaleza='Provision';";
	    	$query2=mysql_query($sql2,$link);
	    	$whereIdEmpleadosProvision='';
	    	while ($row2=mysql_fetch_array($query2)) {
	    		$whereIdEmpleadosProvision.=($whereIdEmpleadosProvision=='')? ' NPE.id_empleado='.$row2['id_empleado']  : ' OR NPE.id_empleado='.$row2['id_empleado'];
	    		// $arrayIdConceptos[$row2['id_empleado']].=($arrayIdConceptos[$row2['id_empleado']]=='')? ' id_concepto='.$row2['id_concepto'] : ' OR id_concepto='.$row2['id_concepto'] ;
	    		$whereIdConceptos[$row2['id_empleado']].=($whereIdConceptos[$row2['id_empleado']]=='')? ' id_concepto='.$row2['id_concepto'] : ' OR id_concepto='.$row2['id_concepto'] ;
	    	}
	    	if ($whereIdEmpleadosProvision=='') { continue;	}
			// //CONSULTAR LAS PLANILLAS DE NOMINA QUE ESTAN DENTRO DEL RANGO DE FECHAS DE LA LIQUIDACION PARA ACTUALIZAR EL CAMPO ID_PLANILLA_LIQUIDACION
			$sql3="SELECT
						NP.id,
						NP.consecutivo,
						NP.sucursal,
						NPE.dias_laborados,
						NPE.id_empleado,
						NPE.nombre_empleado
					FROM
						nomina_planillas AS NP,
						nomina_planillas_empleados AS NPE
					WHERE
						NP.activo        = 1
					AND NP.estado        = 1
					AND NP.id_empresa    = $id_empresa
					AND NP.fecha_inicio >= '$fecha_inicio'
					AND NP.fecha_final  <= '$fecha_final'
					AND NPE.id_planilla  = NP.id
					AND ($whereIdEmpleadosProvision)
					GROUP BY NP.id,NPE.id_empleado ORDER BY consecutivo ASC;";
			$query3=mysql_query($sql3,$link);
			$whereIdPlanillas='';
			$bodySub = '';
			while ($row3=mysql_fetch_array($query3)) {
				// CONSULTAR LOS REGISTROS A ACTUALIZAR
				$sqlCon="SELECT * FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND naturaleza='Provision' AND id_empleado=$row3[id_empleado] AND id_planilla=$row3[id] AND (".$whereIdConceptos[$row3['id_empleado']].");";
				$queryCon=mysql_query($sqlCon,$link);

				$bodySub .= '<tr><td>LN '.$row3['consecutivo'].' '.$row3['sucursal'].' </td><td>'.$sqlCon.'</td></tr>';

				//$sql4="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=$row[id], tipo_planilla_cruce='LE'
				//	WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$row3[id_empleado] AND (id_planilla_cruce=0 OR ISNULL(id_planilla_cruce)) AND id_planilla=$row3[id] ";

			// 	// ACTUALIZAR LA COLUMNA ID PLANILLA LIQUIDACION DE LAS FILAS DE LOS CONCEPTOS DE LAS PLANILLAS EN ESE PERIODO DE TIEMPO
				$sql4="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=$row[id], tipo_planilla_cruce='LE'
					WHERE activo=1 AND id_empresa=$id_empresa AND naturaleza='Provision' AND id_empleado=$row3[id_empleado] AND id_planilla=$row3[id] AND (".$whereIdConceptos[$row3['id_empleado']].");";
			// 	echo "<br>";// echo "<br>";
				$query4=mysql_query($sql4,$link);

				$sqlUpdate="UPDATE nomina_planillas SET estado=2 WHERE activo=1 AND id_empresa=$id_empresa AND id=$row3[id]";
				$queryUpdate=mysql_query($sqlUpdate,$link);

			// 	// $where.=($where=='')? '(id_planilla='.$row3['id'].' AND id_empleado='.$row3['id_empleado'].' AND ('.$arrayIdConceptos[$row3['id_empleado']].') )'
			// 	// 					: ' OR (id_planilla='.$row3['id'].' AND id_empleado='.$row3['id_empleado'].' AND ('.$arrayIdConceptos[$row3['id_empleado']].') )' ;
			// 	// $whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			}

			// while ($row=mysql_fetch_array($query)) {
			// 	$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			// 	$dias_laborados+=$row['dias_laborados'];
			// }

			echo '<div style="float:left;width:100%;">
					<table>
					<tr>
						<td>Planilla de liquidacion N. '.$row['consecutivo'].' de '.$row['fecha_inicio'].' hasta '.$row['fecha_final'].'  sucursal: '.$row['sucursal'].'</td>
					</tr>
					'.$bodySub.'
				 </table>
				 </div>';

		}

	}
?>