<?php
	include_once("../../configuracion/conectar.php");

	echo$sql="SELECT
            N.consecutivo,
            N.sucursal,
            NE.documento_empleado,
            NE.nombre_empleado,
            NC.codigo_concepto,
            NC.concepto,
            NC.naturaleza,
            NC.valor_concepto,
            NC.valor_concepto_ajustado,
            NC.dias_laborados,
            NC.dias_adicionales,
            NC.base,
            N.fecha_documento,
            N.fecha_inicio,
            N.fecha_final
          FROM
            nomina_planillas_liquidacion AS N,
            nomina_planillas_liquidacion_empleados AS NE,
            nomina_planillas_liquidacion_empleados_conceptos AS NC
          WHERE N.fecha_inicio >= '$_GET[start]' AND N.fecha_final<='$_GET[end]'
          AND N.estado = 1
          AND N.id_empresa = $_GET[empresa_id]
          AND NE.id_planilla = N.id
          AND NC.id_planilla = N.id
          AND NC.id_empleado = NE.id_empleado";
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
	header("Content-Disposition: attachment; filename=informe_liquidacion_empleados_".date("Y_m_d H.i:s").".xls");
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
			<td>Fecha inicio <?= $_GET['start'] ?></td>
			<td>Fecha final <?=$_GET['end'] ?></td>
		</tr>
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

