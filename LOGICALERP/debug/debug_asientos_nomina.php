<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	$sql="SELECT * FROM nomina_planillas WHERE activo=1 AND debug=1";
	$query=mysql_query($sql,$link);

	while ($row=mysql_fetch_array($query)) {
		$arrayPlanillas[$row['id']] = array(
											'fecha_documento' => $row['fecha_documento'],
											'consecutivo'     => $row['consecutivo'],
											'id_empresa'      => $row['id_empresa'],
											);
		$whereId.=($whereId=='')? 'id_documento='.$row['id'] : ' OR id_documento='.$row['id'] ;
		// echo $row['fecha_documento'].' - '.$row['consecutivo'].' - '.$row['sucursal'].' - '.$row['id_empresa'].'<br>';
	}

	$sql="SELECT * FROM asientos_colgaap WHERE tipo_documento='LN' AND ($whereId) ";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$arrayAsientosColgaap[$row['id_documento']][$row['id']] = array(
																		'fecha'         => $row['fecha'],
																		'codigo_cuenta' => $row['codigo_cuenta'],
																		'cuenta'        => $row['cuenta'],
																		);
	}

	$sql="SELECT * FROM asientos_niif WHERE tipo_documento='LN' AND ($whereId) ";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$arrayAsientosNiif[$row['id_documento']][$row['id']] = array(
																		'fecha'         => $row['fecha'],
																		'codigo_cuenta' => $row['codigo_cuenta'],
																		'cuenta'        => $row['cuenta'],
																		);
	}

	$body='<table>';

	foreach ($arrayPlanillas as $id_planilla => $arrayPlanillasResul) {
		$body.='<tr>
					<td><b>'.$arrayPlanillasResul['fecha_documento'].'</b></td>
					<td><b>'.$arrayPlanillasResul['consecutivo'].'</b></td>
					<td><b>'.$arrayPlanillasResul['id_empresa'].'</b></td>
				</tr>
				<tr><td colspan="3"><b>CUENTAS COLGAAP</b></td></tr>';
		foreach ($arrayAsientosColgaap[$id_planilla] as $id_registro => $arrayResul) {
			$body.='<tr>
						<td>'.$arrayResul['fecha'].'</td>
						<td>'.$arrayResul['codigo_cuenta'].'</td>
						<td>'.$arrayResul['cuenta'].'</td>
					</tr>';

			$sqlColgaap.="UPDATE asientos_colgaap SET fecha='$arrayPlanillasResul[fecha_documento]' WHERE tipo_documento='LN' AND id_documento=$id_planilla AND id=$id_registro; ";
		}
		$body.='<tr><td colspan="3"><b>CUENTAS NIIF</b></td></tr>';
		foreach ($arrayAsientosNiif[$id_planilla] as $id_registro_niif => $arrayResulNiif) {
			$body.='<tr>
						<td>'.$arrayResulNiif['fecha'].'</td>
						<td>'.$arrayResulNiif['codigo_cuenta'].'</td>
						<td>'.$arrayResulNiif['cuenta'].'</td>
					</tr>';

			$sqlNiif.="UPDATE asientos_niif SET fecha='$arrayPlanillasResul[fecha_documento]'  WHERE tipo_documento='LN' AND id_documento=$id_planilla AND id=$id_registro_niif; ";
		}
	}

	$body.='</table>';

	// echo $body;
	echo $sqlNiif;


?>