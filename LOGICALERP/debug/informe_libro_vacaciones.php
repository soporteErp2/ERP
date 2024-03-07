<?php
		include_once("../../configuracion/conectar.php");

	$sql="SELECT
			(SELECT consecutivo FROM nomina_planillas_liquidacion WHERE activo=1 AND id=id_planilla) AS consecutivo_planilla,
			(SELECT sucursal FROM nomina_planillas_liquidacion WHERE activo=1 AND id=id_planilla) AS sucursal_planilla,
			documento_empleado,
			nombre_empleado,
			dias_vacaciones_disfrutadas,
			valor_vacaciones_disfrutadas,
			valor_vacaciones_compensadas,
			IF(id_empresa=1,'COLOMBIA','COMUNIACIONES') AS empresa
		FROM
			nomina_vacaciones_empleados
		WHERE
			activo = 1
		AND (
			id_empresa = 1
			OR id_empresa = 47
		)
		HAVING (SELECT consecutivo FROM nomina_planillas_liquidacion WHERE activo=1 AND id=id_planilla) > 0";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$body .= "<tr>
					<td>$row[consecutivo_planilla]</td>
					<td>$row[sucursal_planilla]</td>
					<td>$row[documento_empleado]</td>
					<td>$row[nombre_empleado]</td>
					<td>$row[dias_vacaciones_disfrutadas]</td>
					<td>$row[valor_vacaciones_disfrutadas]</td>
					<td>$row[valor_vacaciones_compensadas]</td>
					<td>$row[empresa]</td>
				</tr>";
	}

		header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=informe_libro_vacaciones".date("Y_m_d").".xls");
       header("Pragma: no-cache");
       header("Expires: 0");



 ?>

<html>
<head>
	<title></title>
</head>
<body>
	<table>
		<tr>
			<td>consecutivo_planilla</td>
			<td>sucursal_planilla</td>
			<td>documento_empleado</td>
			<td>nombre_empleado</td>
			<td>dias_vacaciones_disfrutadas</td>
			<td>valor_vacaciones_disfrutadas</td>
			<td>valor_vacaciones_compensadas</td>
			<td>empresa</td>
		</tr>
		<?php echo $body; ?>
	</table>
</body>
</html>

