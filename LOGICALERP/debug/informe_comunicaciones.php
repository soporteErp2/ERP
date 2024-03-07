<?php
		include_once("../../configuracion/conectar.php");

	$sql="SELECT
				NP.consecutivo,
				NP.sucursal,
				NP.fecha_documento,
				NP.fecha_inicio,
				NP.fecha_final,
			  (SELECT documento FROM empleados WHERE activo=1 AND id=NPEC.id_empleado) AS documento_empleado,
			  (SELECT nombre FROM empleados WHERE activo=1 AND id=NPEC.id_empleado) AS nombre_empleado,
				NPEC.codigo_concepto,
				NPEC.concepto,
				NPEC.naturaleza,
				NPEC.valor_campo_texto,
				NPEC.valor_concepto,
				NPEC.cuenta_colgaap,
				NPEC.dias_laborados,
				NPEC.dias_adicionales,
				NPEC.base,
			  NPEC.caracter,
				(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NPEC.id_tercero) AS documento_tercero,
				(SELECT nombre FROM terceros WHERE activo=1 AND id=NPEC.id_tercero) AS tercero,
				NPEC.cuenta_contrapartida_colgaap,
			  NPEC.caracter_contrapartida,
				(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_contrapartida) AS documento_tercero_contrapartida,
				(SELECT nombre FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_contrapartida) AS tercero_contrapartida,
				NPEC.codigo_centro_costos,
				NPEC.codigo_centro_costos_contrapartida,
				NPEC.centro_costos_contrapartida,
			  'LE' AS tipo_planilla,
			  'Planilla de Liquidacion' AS descripcion_tipo_planilla,
			  NPEC.valor_concepto_ajustado,
				SUM(NPEC.valor_concepto-NPEC.valor_concepto_ajustado) AS diferencia_ajuste,
			  NPEC.cuenta_colgaap_liquidacion,
			  NPEC.descripcion_cuenta_colgaap_liquidacion

			FROM
				nomina_planillas_liquidacion AS NP,
				nomina_planillas_liquidacion_empleados_conceptos AS NPEC
			WHERE
				NP.id_empresa = 47
			AND NPEC.id_empresa = 47
			AND NP.estado=1
			AND NP.activo = 1
			AND NPEC.activo = 1
			AND NP.fecha_documento BETWEEN '2017-12-01' AND '2017-12-31'
			AND NPEC.id_planilla = NP.id
			GROUP BY NPEC.id";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$body .= "<tr>
					<td>$row[consecutivo]</td>
					<td>$row[sucursal]</td>
					<td>$row[documento_empleado]</td>
					<td>$row[nombre_empleado]</td>
					<td>$row[codigo_concepto]</td>
					<td>$row[concepto]</td>
					<td>$row[naturaleza]</td>
					<td>$row[valor_concepto]</td>
					<td>$row[valor_concepto_ajustado]</td>
					<td>$row[dias_laborados]</td>
					<td>$row[dias_adicionales]</td>
					<td>$row[base]</td>
					<td>$row[fecha_documento]</td>
					<td>$row[fecha_inicio]</td>
					<td>$row[fecha_final]</td>
				</tr>";
	}

		header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=informe_liquidacion_empleados_COMUNICACIONES_".date("Y_m_d").".xls");
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
			<td>consecutivo</td>
			<td>sucursal</td>
			<td>documento_empleado</td>
			<td>nombre_empleado</td>
			<td>codigo_concepto</td>
			<td>concepto</td>
			<td>naturaleza</td>
			<td>valor_concepto</td>
			<td>valor_concepto_ajustado</td>
			<td>dias_laborados</td>
			<td>dias_adicionales</td>
			<td>base</td>
			<td>fecha_documento</td>
			<td>fecha_inicio</td>
			<td>fecha_final</td>
		</tr>
		<?php echo $body; ?>
	</table>
</body>
</html>

