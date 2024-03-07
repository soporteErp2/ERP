<?php

	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");
	exit;
// INSERT INTO `erp`.`empleados_contratos_entidades` (`id`, `id_empleado`, `id_contrato`, `id_entidad`, `entidad`, `id_concepto`, `concepto`, `id_empresa`, `activo`) VALUES ('12', '77', '32', '3942', 'ENTIDAD PROMOTORA DE SALUD FAMISANAR LTDA CAFAM COLSUBSIDIO', '23', 'EPS EMPLEADO', '47', '1');

	// CONSULTAR TODOS LOS CONCEPTOS
	$sql="SELECT * FROM nomina_conceptos WHERE activo=1";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$whereIds.=($whereIds=='')? '(id_concepto<>'.$row['id'].' AND id_empresa<>'.$row['id_empresa'].')' : ' OR (id_concepto<>'.$row['id'].' AND id_empresa<>'.$row['id_empresa'].')' ;
		$arrayIdConceptos[$row['id_empresa']][$row['descripcion']]=$row['id'];
	}

	// CONSULTAR TODOS LOS CONTRATOS
	$sql="SELECT
				id,
				id_empleado,
				id_contrato,
				id_entidad,
				entidad,
				id_concepto,
				concepto,
				id_empresa
		FROM empleados_contratos_entidades WHERE activo=1 AND ($whereIds) ";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$bodyContratos.='<tr>
							<td>'.$row['id_empleado'].'</td>
							<td>'.$row['id_contrato'].'</td>
							<td>'.$row['id_entidad'].'</td>
							<td>'.$row['entidad'].'</td>
							<td>'.$row['id_concepto'].'</td>
							<td>'.$row['concepto'].'</td>
							<td>'.$row['id_empresa'].'</td>
						</tr>';
		$id_empresa = $row['id_empresa'];
		$indice     = $row['concepto'];
		$sql_update = "UPDATE empleados_contratos_entidades SET id_concepto='".$arrayIdConceptos[$id_empresa][$indice]."' WHERE activo=1 AND id_empresa=$id_empresa AND concepto='$indice'  ";
		$query_update=mysql_query($sql_update,$link);
		if (!$query_update) {
			echo "false<br>";
		}
		// echo 'id_concepto: '.$row['id_concepto'].'<br>';
		// echo $sql_update.'<br>';
	}

	// echo '<table align="center">
	// 		<tr>
	// 			<td>id_empleado </td>
	// 			<td>id_contrato </td>
	// 			<td>id_entidad </td>
	// 			<td>entidad </td>
	// 			<td>id_concepto </td>
	// 			<td>concepto </td>
	// 			<td>id_empresa </td>
	// 		</tr>
	// 		'.$bodyContratos.'
	// 		</table>';


?>