<?php
exit;
	// session_start();
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");

	$whereIdPlanillas='	(id_planilla = 352
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
						OR id_planilla=423)';
	$sql="SELECT id,id_planilla,id_empleado,codigo_concepto,dias_laborados FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND ($whereIdPlanillas)";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=mysql_fetch_array($query)) {
		if ($row['codigo_concepto']=='VC' || $row['codigo_concepto']=='CS') {
			$arrayConceptos[$row['id_planilla']][$row['id_empleado']][$row['codigo_concepto']]=array('id'=>$row['id'],'dias_laborados'=>$row['dias_laborados']);
		}
	}

	echo "<table>
			<tr>
					<td>ID PLANILLA</td>
					<td>ID EMPLEADO</td>
					<td>CONCEPTO</td>
					<td>VALOR</td>
				</tr>
			";
	foreach ($arrayConceptos as $id_planilla => $arrayConceptosResul) {
		foreach ($arrayConceptosResul as $id_empleado => $arrayResul) {
			echo '<tr>
					<td>'.$id_planilla.'</td>
					<td>'.$id_empleado.'</td>
					<td>VC</td>
					<td>'.$arrayResul['VC']['dias_laborados'].'</td>
				</tr>
				<tr>
					<td>'.$id_planilla.'</td>
					<td>'.$id_empleado.'</td>
					<td>CS</td>
					<td>'.$arrayResul['CS']['dias_laborados'].'</td>
				</tr>';
			$cadenaUpdate.="UPDATE nomina_planillas_empleados_conceptos
							SET dias_laborados='".$arrayResul['CS']['dias_laborados']."',saldo_dias_laborados='".$arrayResul['CS']['dias_laborados']."' WHERE id='".$arrayResul['VC']['id']."';";
		}
	}
	echo "<table>";
	echo $cadenaUpdate;
	// print_r($arrayConceptos);

 ?>
